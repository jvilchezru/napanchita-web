<?php

/**
 * Modelo Delivery
 * Gestiona las entregas a domicilio
 * Sistema Napanchita - Módulo Delivery
 */
class Delivery
{
    private $conn;
    private $table = "deliveries";

    public $id;
    public $pedido_id;
    public $direccion;
    public $referencia;
    public $zona_id;
    public $repartidor_id;
    public $estado;
    public $fecha_asignacion;
    public $fecha_entrega;
    public $observaciones;
    public $latitud;
    public $longitud;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear nuevo delivery
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (pedido_id, direccion, referencia, zona_id, estado, observaciones, latitud, longitud) 
                  VALUES (:pedido_id, :direccion, :referencia, :zona_id, :estado, :observaciones, :latitud, :longitud)";

        $stmt = $this->conn->prepare($query);

        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->referencia = htmlspecialchars(strip_tags($this->referencia));
        $this->observaciones = $this->observaciones ? htmlspecialchars(strip_tags($this->observaciones)) : null;

        $stmt->bindParam(":pedido_id", $this->pedido_id);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":referencia", $this->referencia);
        $stmt->bindParam(":zona_id", $this->zona_id);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":observaciones", $this->observaciones);
        $stmt->bindParam(":latitud", $this->latitud);
        $stmt->bindParam(":longitud", $this->longitud);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear delivery: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener delivery por ID de pedido
     */
    public function obtenerPorPedidoId($pedido_id)
    {
        $query = "SELECT d.*, 
                         z.nombre as zona_nombre,
                         z.costo_envio,
                         z.tiempo_estimado,
                         u.nombre as repartidor_nombre,
                         u.telefono as repartidor_telefono
                  FROM " . $this->table . " d
                  LEFT JOIN zonas_delivery z ON d.zona_id = z.id
                  LEFT JOIN usuarios u ON d.repartidor_id = u.id
                  WHERE d.pedido_id = :pedido_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pedido_id", $pedido_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Listar deliveries con filtros
     */
    public function listar($filtros = [])
    {
        $query = "SELECT d.*, 
                         p.id as pedido_numero,
                         p.total as pedido_total,
                         p.estado as pedido_estado,
                         c.nombre as cliente_nombre,
                         c.telefono as cliente_telefono,
                         z.nombre as zona_nombre,
                         u.nombre as repartidor_nombre
                  FROM " . $this->table . " d
                  INNER JOIN pedidos p ON d.pedido_id = p.id
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN zonas_delivery z ON d.zona_id = z.id
                  LEFT JOIN usuarios u ON d.repartidor_id = u.id
                  WHERE 1=1";

        $params = [];

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $query .= " AND d.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (isset($filtros['repartidor_id']) && !empty($filtros['repartidor_id'])) {
            $query .= " AND d.repartidor_id = :repartidor_id";
            $params[':repartidor_id'] = $filtros['repartidor_id'];
        }

        if (isset($filtros['zona_id']) && !empty($filtros['zona_id'])) {
            $query .= " AND d.zona_id = :zona_id";
            $params[':zona_id'] = $filtros['zona_id'];
        }

        if (isset($filtros['fecha']) && !empty($filtros['fecha'])) {
            $query .= " AND DATE(p.fecha_pedido) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }

        $query .= " ORDER BY p.fecha_pedido DESC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Asignar repartidor a delivery
     */
    public function asignarRepartidor($delivery_id, $repartidor_id)
    {
        $query = "UPDATE " . $this->table . " 
                  SET repartidor_id = :repartidor_id,
                      estado = 'asignado',
                      fecha_asignacion = CURRENT_TIMESTAMP 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":repartidor_id", $repartidor_id);
        $stmt->bindParam(":id", $delivery_id);

        return $stmt->execute();
    }

    /**
     * Cambiar estado del delivery
     */
    public function cambiarEstado($delivery_id, $nuevo_estado, $observaciones = null)
    {
        $query = "UPDATE " . $this->table . " 
                  SET estado = :estado";

        if ($nuevo_estado === 'entregado') {
            $query .= ", fecha_entrega = CURRENT_TIMESTAMP";
        }

        if ($observaciones) {
            $query .= ", observaciones = :observaciones";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":id", $delivery_id);

        if ($observaciones) {
            $observaciones = htmlspecialchars(strip_tags($observaciones));
            $stmt->bindParam(":observaciones", $observaciones);
        }

        return $stmt->execute();
    }

    /**
     * Listar deliveries asignados a un repartidor
     */
    public function listarPorRepartidor($repartidor_id, $solo_activos = false)
    {
        $query = "SELECT d.*, 
                         p.id as pedido_numero,
                         p.total as pedido_total,
                         p.estado as pedido_estado,
                         p.fecha_pedido,
                         c.nombre as cliente_nombre,
                         c.telefono as cliente_telefono,
                         z.nombre as zona_nombre,
                         z.tiempo_estimado
                  FROM " . $this->table . " d
                  INNER JOIN pedidos p ON d.pedido_id = p.id
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN zonas_delivery z ON d.zona_id = z.id
                  WHERE d.repartidor_id = :repartidor_id";

        if ($solo_activos) {
            $query .= " AND d.estado IN ('asignado', 'en_camino')";
        }

        $query .= " ORDER BY 
                    CASE d.estado 
                        WHEN 'en_camino' THEN 1
                        WHEN 'asignado' THEN 2
                        WHEN 'pendiente' THEN 3
                        ELSE 4
                    END,
                    p.fecha_pedido DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":repartidor_id", $repartidor_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estadísticas de deliveries
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null)
    {
        $query = "SELECT 
                    COUNT(*) as total_deliveries,
                    SUM(CASE WHEN d.estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN d.estado = 'asignado' THEN 1 ELSE 0 END) as asignados,
                    SUM(CASE WHEN d.estado = 'en_camino' THEN 1 ELSE 0 END) as en_camino,
                    SUM(CASE WHEN d.estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                    SUM(CASE WHEN d.estado = 'fallido' THEN 1 ELSE 0 END) as fallidos,
                    AVG(TIMESTAMPDIFF(MINUTE, p.fecha_pedido, d.fecha_entrega)) as tiempo_promedio_entrega
                  FROM " . $this->table . " d
                  INNER JOIN pedidos p ON d.pedido_id = p.id
                  WHERE 1=1";

        $params = [];

        if ($fecha_desde) {
            $query .= " AND DATE(p.fecha_pedido) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }

        if ($fecha_hasta) {
            $query .= " AND DATE(p.fecha_pedido) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener delivery por ID
     */
    public function obtenerPorId($id)
    {
        $query = "SELECT d.*, 
                         z.nombre as zona_nombre,
                         u.nombre as repartidor_nombre
                  FROM " . $this->table . " d
                  LEFT JOIN zonas_delivery z ON d.zona_id = z.id
                  LEFT JOIN usuarios u ON d.repartidor_id = u.id
                  WHERE d.id = :id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar ubicación GPS del delivery (para tracking)
     */
    public function actualizarUbicacion($delivery_id, $latitud, $longitud)
    {
        $query = "UPDATE " . $this->table . " 
                  SET latitud = :latitud, 
                      longitud = :longitud 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":latitud", $latitud);
        $stmt->bindParam(":longitud", $longitud);
        $stmt->bindParam(":id", $delivery_id);

        return $stmt->execute();
    }
}
