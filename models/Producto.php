<?php
/**
 * Modelo de Producto
 * Gestiona el catálogo de productos del menú
 */
class Producto {
    private $conn;
    private $table = "productos";

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $categoria_id;
    public $imagen;
    public $disponible;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Listar productos disponibles
    public function listar() {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.disponible = TRUE 
                  ORDER BY c.nombre, p.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener producto por ID
    public function obtenerPorId() {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear producto
    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, descripcion, precio, categoria_id, imagen) 
                  VALUES (:nombre, :descripcion, :precio, :categoria_id, :imagen)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":imagen", $this->imagen);
        
        return $stmt->execute();
    }

    // Actualizar producto
    public function actualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                      categoria_id = :categoria_id, disponible = :disponible
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":disponible", $this->disponible);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    // Buscar productos
    public function buscar($termino) {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.disponible = TRUE AND (p.nombre LIKE :termino OR p.descripcion LIKE :termino)
                  ORDER BY p.nombre";
        
        $stmt = $this->conn->prepare($query);
        $termino = "%{$termino}%";
        $stmt->bindParam(":termino", $termino);
        $stmt->execute();
        
        return $stmt;
    }
}
?>
