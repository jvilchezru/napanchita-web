<?php
/**
 * Modelo de Usuario
 * Gestiona las operaciones CRUD de usuarios (clientes y administradores)
 */
class Usuario {
    private $conn;
    private $table = "usuarios";

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $telefono;
    public $direccion;
    public $rol;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear usuario
    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, password, telefono, direccion, rol) 
                  VALUES (:nombre, :email, :password, :telefono, :direccion, :rol)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash del password
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":rol", $this->rol);
        
        return $stmt->execute();
    }

    // Login de usuario
    public function login() {
        $query = "SELECT id, nombre, email, password, telefono, direccion, rol 
                  FROM " . $this->table . " 
                  WHERE email = :email AND activo = TRUE LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->email = $row['email'];
            $this->telefono = $row['telefono'];
            $this->direccion = $row['direccion'];
            $this->rol = $row['rol'];
            return true;
        }
        
        return false;
    }

    // Obtener usuario por ID
    public function obtenerPorId() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todos los usuarios
    public function listar() {
        $query = "SELECT id, nombre, email, telefono, rol, fecha_registro 
                  FROM " . $this->table . " WHERE activo = TRUE ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Actualizar usuario
    public function actualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, email = :email, telefono = :telefono, direccion = :direccion 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
}
?>
