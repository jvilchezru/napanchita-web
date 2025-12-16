<?php

/**
 * Modelo Pedido
 * Gestiona las operaciones CRUD de pedidos multi-canal
 * Sistema Napanchita
 */
class Pedido
{
    private $conn;
    private $table = "pedidos";
    private $table_items = "pedido_items";

    // Propiedades del pedido
    public $id;
    public $cliente_id;
    public $mesa_id;
    public $usuario_id;
    public $tipo; // 'mesa', 'delivery', 'para_llevar'
    public $estado; // 'pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado'
    public $subtotal;
    public $costo_envio;
    public $descuento;
    public $total;
    public $notas;
    public $fecha_pedido;
    public $fecha_actualizacion;

    /**
     * Constructor
     * @param PDO $db Conexión a base de datos
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Crear nuevo pedido
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (cliente_id, mesa_id, usuario_id, tipo, estado, 
                   subtotal, costo_envio, descuento, total, notas) 
                  VALUES (:cliente_id, :mesa_id, :usuario_id, :tipo, :estado,
                          :subtotal, :costo_envio, :descuento, :total, :notas)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->notas = $this->notas ? htmlspecialchars(strip_tags($this->notas)) : null;

        // Bind de parámetros
        $stmt->bindParam(":cliente_id", $this->cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(":mesa_id", $this->mesa_id, PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $this->usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":subtotal", $this->subtotal);
        $stmt->bindParam(":costo_envio", $this->costo_envio);
        $stmt->bindParam(":descuento", $this->descuento);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":notas", $this->notas);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Agregar item al pedido
     * @param array $item Datos del item
     * @return boolean True si se agregó correctamente
     */
    public function agregarItem($item)
    {
        $query = "INSERT INTO " . $this->table_items . " 
                  (pedido_id, plato_id, combo_id, tipo, nombre, cantidad, precio_unitario, subtotal, notas) 
                  VALUES (:pedido_id, :plato_id, :combo_id, :tipo, :nombre, :cantidad, :precio_unitario, :subtotal, :notas)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":pedido_id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":plato_id", $item['plato_id'], PDO::PARAM_INT);
        $stmt->bindParam(":combo_id", $item['combo_id'], PDO::PARAM_INT);
        $stmt->bindParam(":tipo", $item['tipo']);
        $stmt->bindParam(":nombre", $item['nombre']);
        $stmt->bindParam(":cantidad", $item['cantidad'], PDO::PARAM_INT);
        $stmt->bindParam(":precio_unitario", $item['precio_unitario']);
        $stmt->bindParam(":subtotal", $item['subtotal']);
        $stmt->bindParam(":notas", $item['notas']);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al agregar item al pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener pedido por ID con items
     * @param int $id ID del pedido
     * @return array|false Array con datos del pedido o false
     */
    public function obtenerPorId($id = null)
    {
        // Si se pasa el ID como parámetro, usarlo; si no, usar $this->id
        $pedido_id = $id ?? $this->id;
        
        $query = "SELECT p.*, 
                         c.nombre as cliente_nombre, 
                         c.telefono as cliente_telefono,
                         c.direcciones as cliente_direcciones,
                         m.numero as mesa_numero,
                         u.nombre as usuario_nombre
                  FROM " . $this->table . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN mesas m ON p.mesa_id = m.id
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $pedido_id, PDO::PARAM_INT);
        $stmt->execute();

        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            // Decodificar direcciones JSON
            if (isset($pedido['cliente_direcciones']) && $pedido['cliente_direcciones']) {
                $pedido['cliente_direcciones'] = json_decode($pedido['cliente_direcciones'], true);
            }
            
            // Obtener items del pedido - SIEMPRE inicializar como array
            $pedido['items'] = [];
            
            try {
                $queryItems = "SELECT 
                                id,
                                pedido_id,
                                plato_id,
                                combo_id,
                                tipo,
                                nombre,
                                cantidad,
                                precio_unitario,
                                subtotal,
                                notas
                              FROM pedido_items 
                              WHERE pedido_id = :pedido_id
                              ORDER BY id ASC";
                
                $stmtItems = $this->conn->prepare($queryItems);
                $stmtItems->bindParam(":pedido_id", $pedido_id, PDO::PARAM_INT);
                
                if ($stmtItems->execute()) {
                    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                    if ($items !== false && is_array($items)) {
                        $pedido['items'] = $items;
                    }
                }
            } catch (PDOException $e) {
                error_log("Error al cargar items del pedido {$pedido_id}: " . $e->getMessage());
                $pedido['items'] = [];
            }
        }

        return $pedido;
    }

    /**
     * Obtener items de un pedido
     * @param int $pedido_id ID del pedido
     * @return array Array de items
     */
    public function obtenerItemsDelPedido($pedido_id)
    {
        $query = "SELECT 
                    id,
                    pedido_id,
                    plato_id,
                    combo_id,
                    tipo,
                    nombre,
                    cantidad,
                    precio_unitario,
                    subtotal,
                    notas
                  FROM " . $this->table_items . " 
                  WHERE pedido_id = :pedido_id
                  ORDER BY id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pedido_id", $pedido_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $items;
        }
        
        return [];
    }

    /**
     * Listar todos los pedidos
     * @param array $filtros Filtros opcionales (tipo, estado, fecha)
     * @return array Array de pedidos
     */
    public function listar($filtros = [])
    {
        $query = "SELECT p.*, 
                         c.nombre as cliente_nombre, 
                         c.telefono as cliente_telefono,
                         m.numero as mesa_numero,
                         u.nombre as usuario_nombre,
                         COUNT(pi.id) as total_items
                  FROM " . $this->table . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN mesas m ON p.mesa_id = m.id
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  LEFT JOIN " . $this->table_items . " pi ON p.id = pi.pedido_id
                  WHERE 1=1";

        // Aplicar filtros
        if (isset($filtros['tipo']) && !empty($filtros['tipo'])) {
            $query .= " AND p.tipo = :tipo";
        }

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $query .= " AND p.estado = :estado";
        }

        if (isset($filtros['fecha']) && !empty($filtros['fecha'])) {
            $query .= " AND DATE(p.fecha_pedido) = :fecha";
        }

        if (isset($filtros['usuario_id']) && !empty($filtros['usuario_id'])) {
            $query .= " AND p.usuario_id = :usuario_id";
        }

        $query .= " GROUP BY p.id
                    ORDER BY p.fecha_pedido DESC";
        
        // Aplicar límite si existe
        if (isset($filtros['limit']) && is_numeric($filtros['limit'])) {
            $query .= " LIMIT " . intval($filtros['limit']);
        }

        $stmt = $this->conn->prepare($query);

        // Bind de filtros
        if (isset($filtros['tipo']) && !empty($filtros['tipo'])) {
            $stmt->bindParam(":tipo", $filtros['tipo']);
        }

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $stmt->bindParam(":estado", $filtros['estado']);
        }

        if (isset($filtros['fecha']) && !empty($filtros['fecha'])) {
            $stmt->bindParam(":fecha", $filtros['fecha']);
        }

        if (isset($filtros['usuario_id']) && !empty($filtros['usuario_id'])) {
            $stmt->bindParam(":usuario_id", $filtros['usuario_id'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar estado del pedido
     * @param int $pedido_id ID del pedido
     * @param string $nuevo_estado Nuevo estado
     * @return boolean True si se actualizó correctamente
     */
    public function cambiarEstado($pedido_id, $nuevo_estado)
    {
        $query = "UPDATE " . $this->table . " 
                  SET estado = :estado 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":id", $pedido_id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado del pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar pedido
     * @return boolean True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET cliente_id = :cliente_id,
                      mesa_id = :mesa_id,
                      tipo = :tipo,
                      estado = :estado,
                      subtotal = :subtotal,
                      costo_envio = :costo_envio,
                      descuento = :descuento,
                      total = :total,
                      notas = :notas
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->notas = $this->notas ? htmlspecialchars(strip_tags($this->notas)) : null;

        $stmt->bindParam(":cliente_id", $this->cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(":mesa_id", $this->mesa_id, PDO::PARAM_INT);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":subtotal", $this->subtotal);
        $stmt->bindParam(":costo_envio", $this->costo_envio);
        $stmt->bindParam(":descuento", $this->descuento);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":notas", $this->notas);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar pedido: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancelar pedido
     * @return boolean True si se canceló correctamente
     */
    public function cancelar()
    {
        return $this->cambiarEstado($this->id, 'cancelado');
    }

    /**
     * Obtener pedidos pendientes (para cocina)
     * @return array Array de pedidos
     */
    public function obtenerPendientesCocina()
    {
        $query = "SELECT p.*, 
                         c.nombre as cliente_nombre,
                         m.numero as mesa_numero,
                         TIMESTAMPDIFF(MINUTE, p.fecha_pedido, NOW()) as minutos_transcurridos
                  FROM " . $this->table . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN mesas m ON p.mesa_id = m.id
                  WHERE p.estado IN ('pendiente', 'en_preparacion')
                  ORDER BY p.fecha_pedido ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar items a cada pedido
        foreach ($pedidos as &$pedido) {
            $pedido['items'] = $this->obtenerItemsDelPedido($pedido['id']);
        }

        return $pedidos;
    }

    /**
     * Obtener estadísticas de pedidos
     * @param string $fecha Fecha (formato Y-m-d)
     * @return array Array con estadísticas
     */
    public function obtenerEstadisticas($fecha = null)
    {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        $query = "SELECT 
                      COUNT(*) as total_pedidos,
                      SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                      SUM(CASE WHEN estado = 'en_preparacion' THEN 1 ELSE 0 END) as en_preparacion,
                      SUM(CASE WHEN estado = 'listo' THEN 1 ELSE 0 END) as listos,
                      SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                      SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                      SUM(CASE WHEN estado != 'cancelado' THEN total ELSE 0 END) as total_ventas,
                      AVG(CASE WHEN estado = 'entregado' THEN total ELSE NULL END) as ticket_promedio
                  FROM " . $this->table . "
                  WHERE DATE(fecha_pedido) = :fecha";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Contar pedidos por estado
     * @param string $estado Estado a contar
     * @return int Cantidad de pedidos
     */
    public function contarPorEstado($estado)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE estado = :estado
                  AND DATE(fecha_pedido) = CURDATE()";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Eliminar pedido (soft delete - cancelar)
     * @return boolean True si se eliminó correctamente
     */
    public function eliminar()
    {
        return $this->cancelar();
    }

    /**
     * Recalcular totales del pedido
     * @return boolean True si se recalculó correctamente
     */
    public function recalcularTotales()
    {
        $query = "SELECT SUM(subtotal) as subtotal_items
                  FROM " . $this->table_items . "
                  WHERE pedido_id = :pedido_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pedido_id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->subtotal = $result['subtotal_items'] ?? 0;
        $this->total = $this->subtotal + $this->costo_envio - $this->descuento;

        return $this->actualizar();
    }

    /**
     * Listar pedidos activos por usuario (no entregados, finalizados ni cancelados)
     * @param int $usuario_id ID del usuario
     * @return array Array de pedidos activos
     */
    public function listarPorUsuario($usuario_id)
    {
        $query = "SELECT p.*, 
                         c.nombre as cliente_nombre, 
                         c.telefono as cliente_telefono,
                         m.numero as mesa_numero,
                         u.nombre as usuario_nombre,
                         COUNT(pi.id) as total_items
                  FROM " . $this->table . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  LEFT JOIN mesas m ON p.mesa_id = m.id
                  LEFT JOIN usuarios u ON p.usuario_id = u.id
                  LEFT JOIN " . $this->table_items . " pi ON p.id = pi.pedido_id
                  WHERE p.usuario_id = :usuario_id
                  AND p.estado NOT IN ('entregado', 'finalizado', 'cancelado')
                  GROUP BY p.id
                  ORDER BY p.fecha_pedido DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
