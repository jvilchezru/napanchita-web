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
    public $metodo_pago_id;
    public $monto_recibido;
    public $monto_cambio;
    public $descuento_aplicado;
    public $codigo_descuento;
    public $usuario_id;
    public $fecha_venta;
    public $ticket_generado;

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
                    p.estado as pedido_estado,
                    mp.nombre as metodo_pago_nombre
                FROM " . $this->table . " v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN pedidos p ON v.pedido_id = p.id
                LEFT JOIN metodos_pago mp ON v.metodo_pago_id = mp.id
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

        if (isset($filtros['metodo_pago_id']) && $filtros['metodo_pago_id']) {
            $query .= " AND v.metodo_pago_id = :metodo_pago_id";
            $params[':metodo_pago_id'] = $filtros['metodo_pago_id'];
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
                    p.tipo as pedido_tipo,
                    p.estado as pedido_estado,
                    mp.nombre as metodo_pago_nombre
                FROM " . $this->table . " v
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN pedidos p ON v.pedido_id = p.id
                LEFT JOIN metodos_pago mp ON v.metodo_pago_id = mp.id
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
                    (pedido_id, total, metodo_pago_id, monto_recibido, monto_cambio, 
                     descuento_aplicado, codigo_descuento, usuario_id) 
                    VALUES 
                    (:pedido_id, :total, :metodo_pago_id, :monto_recibido, :monto_cambio,
                     :descuento_aplicado, :codigo_descuento, :usuario_id)";

        $stmt = $this->db->prepare($query);

        $this->codigo_descuento = htmlspecialchars(strip_tags($this->codigo_descuento ?? ''));

        $stmt->bindParam(':pedido_id', $this->pedido_id);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':metodo_pago_id', $this->metodo_pago_id);
        $stmt->bindParam(':monto_recibido', $this->monto_recibido);
        $stmt->bindParam(':monto_cambio', $this->monto_cambio);
        $stmt->bindParam(':descuento_aplicado', $this->descuento_aplicado);
        $stmt->bindParam(':codigo_descuento', $this->codigo_descuento);
        $stmt->bindParam(':usuario_id', $this->usuario_id);

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
                    mp.nombre as metodo_pago,
                    COUNT(*) as cantidad,
                    SUM(v.total) as total
                FROM " . $this->table . " v
                LEFT JOIN metodos_pago mp ON v.metodo_pago_id = mp.id 
                WHERE DATE(v.fecha_venta) BETWEEN :fecha_desde AND :fecha_hasta
                GROUP BY v.metodo_pago_id, mp.nombre";

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
                    SUM(descuento_aplicado) as total_descuentos
                FROM " . $this->table . " 
                WHERE DATE(fecha_venta) BETWEEN :fecha_desde AND :fecha_hasta";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
