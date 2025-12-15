<?php

/**
 * Modelo Reporte
 * Gestiona consultas para reportes y estadísticas del sistema
 */
class Reporte
{
    private $db;

    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    /**
     * Dashboard - Métricas principales
     */
    public function obtenerMetricasDashboard($fecha = null)
    {
        if (!$fecha) $fecha = date('Y-m-d');

        // Ventas del día
        $query = "SELECT 
                    COUNT(*) as total_ventas,
                    COALESCE(SUM(total), 0) as total_ingresos
                FROM ventas 
                WHERE DATE(fecha_venta) = :fecha";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $ventas = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pedidos por estado
        $query = "SELECT 
                    estado,
                    COUNT(*) as cantidad
                FROM pedidos 
                WHERE DATE(fecha_pedido) = :fecha
                GROUP BY estado";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mesas ocupadas
        $query = "SELECT COUNT(*) as ocupadas FROM mesas WHERE estado = 'ocupada' AND activo = 1";
        $stmt = $this->db->query($query);
        $mesas = $stmt->fetch(PDO::FETCH_ASSOC);

        // Reservas del día
        $query = "SELECT COUNT(*) as total FROM reservas WHERE fecha = :fecha AND estado IN ('Pendiente', 'Confirmada')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $reservas = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'ventas' => $ventas,
            'pedidos' => $pedidos,
            'mesas_ocupadas' => $mesas['ocupadas'],
            'reservas_hoy' => $reservas['total']
        ];
    }

    /**
     * Platos más vendidos
     */
    public function obtenerPlatosMasVendidos($fecha_desde = null, $fecha_hasta = null, $limit = 10)
    {
        if (!$fecha_desde) $fecha_desde = date('Y-m-01');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-t');

        $query = "SELECT 
                    pi.nombre as nombre,
                    MAX(pl.precio) as precio,
                    MAX(c.nombre) as categoria_nombre,
                    SUM(pi.cantidad) as cantidad_vendida,
                    SUM(pi.subtotal) as total_ingresos
                FROM pedido_items pi
                INNER JOIN pedidos p ON pi.pedido_id = p.id
                LEFT JOIN platos pl ON pi.nombre = pl.nombre
                LEFT JOIN categorias c ON pl.categoria_id = c.id
                WHERE DATE(p.fecha_pedido) BETWEEN :fecha_desde AND :fecha_hasta
                    AND p.estado != 'Cancelado'
                    AND (pi.tipo = 'producto' OR pi.tipo = '')
                GROUP BY pi.nombre
                ORDER BY cantidad_vendida DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ventas por período
     */
    public function obtenerVentasPorPeriodo($fecha_desde, $fecha_hasta, $agrupacion = 'dia')
    {
        $formato = match ($agrupacion) {
            'hora' => '%Y-%m-%d %H:00:00',
            'dia' => '%Y-%m-%d',
            'semana' => '%Y-%U',
            'mes' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $query = "SELECT 
                    DATE_FORMAT(fecha_venta, :formato) as periodo,
                    COUNT(*) as cantidad_ventas,
                    SUM(total) as total_ventas
                FROM ventas
                WHERE DATE(fecha_venta) BETWEEN :fecha_desde AND :fecha_hasta
                GROUP BY periodo
                ORDER BY periodo ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':formato', $formato);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Análisis de categorías
     */
    public function obtenerVentasPorCategoria($fecha_desde = null, $fecha_hasta = null)
    {
        if (!$fecha_desde) $fecha_desde = date('Y-m-01');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-t');

        $query = "SELECT 
                    c.id,
                    c.nombre as categoria,
                    COALESCE(COUNT(pi.id), 0) as cantidad_vendida,
                    COALESCE(SUM(pi.subtotal), 0) as total_ingresos
                FROM categorias c
                INNER JOIN platos pl ON c.id = pl.categoria_id
                INNER JOIN pedido_items pi ON pl.nombre = pi.nombre AND pi.tipo = 'producto'
                INNER JOIN pedidos p ON pi.pedido_id = p.id
                WHERE DATE(p.fecha_pedido) BETWEEN :fecha_desde AND :fecha_hasta
                    AND p.estado != 'Cancelado'
                GROUP BY c.id, c.nombre
                HAVING total_ingresos > 0
                ORDER BY total_ingresos DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Análisis de delivery
     */
    public function obtenerEstadisticasDelivery($fecha_desde = null, $fecha_hasta = null)
    {
        if (!$fecha_desde) $fecha_desde = date('Y-m-01');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-t');

        $query = "SELECT 
                    COUNT(*) as total_deliveries,
                    AVG(total) as ticket_promedio,
                    SUM(total) as total_ingresos
                FROM pedidos
                WHERE tipo = 'Delivery'
                AND DATE(fecha_pedido) BETWEEN :fecha_desde AND :fecha_hasta
                AND estado != 'Cancelado'";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Clientes más frecuentes
     */
    public function obtenerClientesFrecuentes($limit = 10)
    {
        $query = "SELECT 
                    c.id,
                    c.nombre,
                    c.telefono,
                    COUNT(p.id) as total_pedidos,
                    SUM(p.total) as total_gastado
                FROM clientes c
                INNER JOIN pedidos p ON c.id = p.cliente_id
                WHERE p.estado != 'Cancelado'
                GROUP BY c.id, c.nombre, c.telefono
                ORDER BY total_pedidos DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
