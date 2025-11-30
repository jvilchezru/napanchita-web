<?php

/**
 * Modelo Venta
 * Gestiona operaciones de ventas y facturación
 */
class Venta
{
    private $db;
    private $table = 'ventas';

    public $id;
    public $pedido_id;
    public $total;
    public $metodo_pago;
    public $monto_recibido;
    public $monto_cambio;
    public $descuento;
    public $usuario_id;
    public $fecha_venta;
    public $comprobante_tipo;
    public $comprobante_numero;
    public $notas;

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
     * Listar ventas con filtros
     */
    public function listar($filtros = [])
    {
        $query = "SELECT 
                    v.*,
                    u.nombre as usuario_nombre,
                    p.tipo as pedido_tipo,
                    p.estado as pedido_estado
                FROM " . $this->table . " v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN pedidos p ON v.pedido_id = p.id
                WHERE 1=1";

        $params = [];

        if (isset($filtros['fecha_desde']) && $filtros['fecha_desde']) {
            $query .= " AND DATE(v.fecha_venta) >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }

        if (isset($filtros['fecha_hasta']) && $filtros['fecha_hasta']) {
            $query .= " AND DATE(v.fecha_venta) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }

        if (isset($filtros['metodo_pago']) && $filtros['metodo_pago']) {
            $query .= " AND v.metodo_pago = :metodo_pago";
            $params[':metodo_pago'] = $filtros['metodo_pago'];
        }

        if (isset($filtros['usuario_id']) && $filtros['usuario_id']) {
            $query .= " AND v.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        $query .= " ORDER BY v.fecha_venta DESC";

        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener venta por ID
     */
    public function obtenerPorId($id)
    {
        $query = "SELECT 
                    v.*,
                    u.nombre as usuario_nombre,
                    p.tipo as pedido_tipo
                FROM " . $this->table . " v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN pedidos p ON v.pedido_id = p.id
                WHERE v.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar nueva venta
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                    (pedido_id, total, metodo_pago, monto_recibido, monto_cambio, 
                     descuento, usuario_id, comprobante_tipo, comprobante_numero, notas) 
                    VALUES 
                    (:pedido_id, :total, :metodo_pago, :monto_recibido, :monto_cambio,
                     :descuento, :usuario_id, :comprobante_tipo, :comprobante_numero, :notas)";

        $stmt = $this->db->prepare($query);

        $this->notas = htmlspecialchars(strip_tags($this->notas ?? ''));

        $stmt->bindParam(':pedido_id', $this->pedido_id);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':metodo_pago', $this->metodo_pago);
        $stmt->bindParam(':monto_recibido', $this->monto_recibido);
        $stmt->bindParam(':monto_cambio', $this->monto_cambio);
        $stmt->bindParam(':descuento', $this->descuento);
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':comprobante_tipo', $this->comprobante_tipo);
        $stmt->bindParam(':comprobante_numero', $this->comprobante_numero);
        $stmt->bindParam(':notas', $this->notas);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return ['success' => true, 'message' => 'Venta registrada exitosamente', 'id' => $this->id];
        }

        return ['success' => false, 'message' => 'Error al registrar la venta'];
    }

    /**
     * Obtener ventas del día
     */
    public function obtenerVentasDelDia($fecha = null)
    {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        return $this->listar(['fecha_desde' => $fecha, 'fecha_hasta' => $fecha]);
    }

    /**
     * Obtener total de ventas por método de pago
     */
    public function obtenerTotalesPorMetodoPago($fecha_desde = null, $fecha_hasta = null)
    {
        if (!$fecha_desde) $fecha_desde = date('Y-m-d');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-d');

        $query = "SELECT 
                    metodo_pago,
                    COUNT(*) as cantidad,
                    SUM(total) as total
                FROM " . $this->table . " 
                WHERE DATE(fecha_venta) BETWEEN :fecha_desde AND :fecha_hasta
                GROUP BY metodo_pago";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estadísticas de ventas
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null)
    {
        if (!$fecha_desde) $fecha_desde = date('Y-m-d');
        if (!$fecha_hasta) $fecha_hasta = date('Y-m-d');

        $query = "SELECT 
                    COUNT(*) as total_ventas,
                    SUM(total) as total_ingresos,
                    AVG(total) as ticket_promedio,
                    SUM(descuento) as total_descuentos
                FROM " . $this->table . " 
                WHERE DATE(fecha_venta) BETWEEN :fecha_desde AND :fecha_hasta";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
