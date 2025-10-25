<?php
/**
 * Modelo de Pedido
 * Gestiona los pedidos y sus detalles
 */
class Pedido {
    private $conn;
    private $table = "pedidos";

    public $id;
    public $usuario_id;
    public $total;
    public $estado;
    public $direccion_entrega;
    public $telefono_contacto;
    public $notas;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear pedido
    public function crear($items) {
        try {
            $this->conn->beginTransaction();
            
            // Insertar pedido
            $query = "INSERT INTO " . $this->table . " 
                      (usuario_id, total, direccion_entrega, telefono_contacto, notas) 
                      VALUES (:usuario_id, :total, :direccion, :telefono, :notas)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario_id", $this->usuario_id);
            $stmt->bindParam(":total", $this->total);
            $stmt->bindParam(":direccion", $this->direccion_entrega);
            $stmt->bindParam(":telefono", $this->telefono_contacto);
            $stmt->bindParam(":notas", $this->notas);
            $stmt->execute();
            
            $pedido_id = $this->conn->lastInsertId();
            
            // Insertar detalles del pedido
            $query_detalle = "INSERT INTO detalles_pedidos 
                              (pedido_id, producto_id, cantidad, precio_unitario, subtotal) 
                              VALUES (:pedido_id, :producto_id, :cantidad, :precio, :subtotal)";
            
            $stmt_detalle = $this->conn->prepare($query_detalle);
            
            foreach ($items as $item) {
                $stmt_detalle->bindParam(":pedido_id", $pedido_id);
                $stmt_detalle->bindParam(":producto_id", $item['producto_id']);
                $stmt_detalle->bindParam(":cantidad", $item['cantidad']);
                $stmt_detalle->bindParam(":precio", $item['precio']);
                $stmt_detalle->bindParam(":subtotal", $item['subtotal']);
                $stmt_detalle->execute();
            }
            
            $this->conn->commit();
            return $pedido_id;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Listar pedidos por usuario
    public function listarPorUsuario() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE usuario_id = :usuario_id 
                  ORDER BY fecha_pedido DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->execute();
        
        return $stmt;
    }

    // Listar todos los pedidos (admin)
    public function listarTodos() {
        $query = "SELECT p.*, u.nombre as cliente_nombre, u.email 
                  FROM " . $this->table . " p
                  INNER JOIN usuarios u ON p.usuario_id = u.id
                  ORDER BY p.fecha_pedido DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener detalles del pedido
    public function obtenerDetalles() {
        $query = "SELECT dp.*, pr.nombre as producto_nombre 
                  FROM detalles_pedidos dp
                  INNER JOIN productos pr ON dp.producto_id = pr.id
                  WHERE dp.pedido_id = :pedido_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pedido_id", $this->id);
        $stmt->execute();
        
        return $stmt;
    }

    // Actualizar estado del pedido
    public function actualizarEstado() {
        $query = "UPDATE " . $this->table . " 
                  SET estado = :estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $this->estado);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    // Obtener pedido por ID
    public function obtenerPorId() {
        $query = "SELECT p.*, u.nombre as cliente_nombre, u.email, u.telefono 
                  FROM " . $this->table . " p
                  INNER JOIN usuarios u ON p.usuario_id = u.id
                  WHERE p.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
