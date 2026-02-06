<?php

/**
 * Modelo Carrito
 * Gestiona el carrito de compras temporal para clientes
 * Sistema Napanchita - Módulo Delivery
 */
class Carrito
{
    private $conn;
    private $table = "carrito";

    public $id;
    public $cliente_id;
    public $session_id;
    public $tipo_producto; // 'plato' o 'combo'
    public $producto_id;
    public $cantidad;
    public $precio_unitario;
    public $notas;
    public $fecha_agregado;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Agregar producto al carrito
     */
    public function agregar()
    {
        // Verificar si el producto ya existe en el carrito
        $existe = $this->verificarProducto();

        if ($existe) {
            // Actualizar cantidad
            return $this->actualizarCantidad($existe['id'], $existe['cantidad'] + $this->cantidad);
        }

        // Insertar nuevo item
        $query = "INSERT INTO " . $this->table . " 
                  (cliente_id, session_id, tipo_producto, producto_id, cantidad, precio_unitario, notas) 
                  VALUES (:cliente_id, :session_id, :tipo_producto, :producto_id, :cantidad, :precio_unitario, :notas)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cliente_id", $this->cliente_id);
        $stmt->bindParam(":session_id", $this->session_id);
        $stmt->bindParam(":tipo_producto", $this->tipo_producto);
        $stmt->bindParam(":producto_id", $this->producto_id);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":precio_unitario", $this->precio_unitario);
        $stmt->bindParam(":notas", $this->notas);

        try {
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al agregar al carrito: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el producto ya está en el carrito
     */
    private function verificarProducto()
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (cliente_id = :cliente_id OR session_id = :session_id) 
                  AND tipo_producto = :tipo_producto 
                  AND producto_id = :producto_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $this->cliente_id);
        $stmt->bindParam(":session_id", $this->session_id);
        $stmt->bindParam(":tipo_producto", $this->tipo_producto);
        $stmt->bindParam(":producto_id", $this->producto_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener items del carrito
     */
    public function obtenerItems($cliente_id = null, $session_id = null)
    {
        $query = "SELECT c.*, 
                         CASE 
                           WHEN c.tipo_producto = 'plato' THEN p.nombre
                           WHEN c.tipo_producto = 'combo' THEN cb.nombre
                         END as nombre_producto,
                         CASE 
                           WHEN c.tipo_producto = 'plato' THEN p.imagen_url
                           WHEN c.tipo_producto = 'combo' THEN cb.imagen_url
                         END as imagen_producto,
                         CASE 
                           WHEN c.tipo_producto = 'plato' THEN cat.nombre
                           ELSE 'Combos'
                         END as categoria_producto
                  FROM " . $this->table . " c
                  LEFT JOIN platos p ON c.tipo_producto = 'plato' AND c.producto_id = p.id
                  LEFT JOIN combos cb ON c.tipo_producto = 'combo' AND c.producto_id = cb.id
                  LEFT JOIN categorias cat ON p.categoria_id = cat.id
                  WHERE (c.cliente_id = :cliente_id OR c.session_id = :session_id)
                  ORDER BY c.fecha_agregado DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->bindParam(":session_id", $session_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar cantidad de un item
     */
    public function actualizarCantidad($item_id, $nueva_cantidad)
    {
        if ($nueva_cantidad <= 0) {
            return $this->eliminarItem($item_id);
        }

        $query = "UPDATE " . $this->table . " 
                  SET cantidad = :cantidad 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cantidad", $nueva_cantidad);
        $stmt->bindParam(":id", $item_id);

        return $stmt->execute();
    }

    /**
     * Eliminar item del carrito
     */
    public function eliminarItem($item_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $item_id);
        return $stmt->execute();
    }

    /**
     * Vaciar carrito
     */
    public function vaciar($cliente_id = null, $session_id = null)
    {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE cliente_id = :cliente_id OR session_id = :session_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->bindParam(":session_id", $session_id);

        return $stmt->execute();
    }

    /**
     * Contar items en el carrito
     */
    public function contarItems($cliente_id = null, $session_id = null)
    {
        $query = "SELECT SUM(cantidad) as total 
                  FROM " . $this->table . " 
                  WHERE cliente_id = :cliente_id OR session_id = :session_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->bindParam(":session_id", $session_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    /**
     * Calcular total del carrito
     */
    public function calcularTotal($cliente_id = null, $session_id = null)
    {
        $query = "SELECT SUM(cantidad * precio_unitario) as total 
                  FROM " . $this->table . " 
                  WHERE cliente_id = :cliente_id OR session_id = :session_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->bindParam(":session_id", $session_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    /**
     * Migrar carrito de sesión anónima a cliente registrado
     */
    public function migrarCarrito($session_id, $cliente_id)
    {
        $query = "UPDATE " . $this->table . " 
                  SET cliente_id = :cliente_id, session_id = NULL 
                  WHERE session_id = :session_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->bindParam(":session_id", $session_id);

        return $stmt->execute();
    }

    /**
     * Limpiar carritos antiguos (más de 7 días)
     */
    public static function limpiarAntiguos()
    {
        $database = new Database();
        $conn = $database->getConnection();

        $query = "DELETE FROM carrito 
                  WHERE fecha_agregado < DATE_SUB(NOW(), INTERVAL 7 DAY)";

        $stmt = $conn->prepare($query);
        return $stmt->execute();
    }
}
