<?php

/**
 * Modelo Usuario
 * Gestiona las operaciones CRUD de usuarios del sistema (admin, mesero, repartidor)
 * Sistema Napanchita
 */
class Usuario
{
    private $conn;
    private $table = "usuarios";

    // Propiedades del usuario
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $telefono;
    public $rol;
    public $fecha_registro;
    public $activo;

    /**
     * Constructor
     * @param PDO $db Conexión a base de datos
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Crear nuevo usuario
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, password, telefono, rol, activo) 
                  VALUES (:nombre, :email, :password, :telefono, :rol, :activo)";

        $stmt = $this->conn->prepare($query);

        // Hash del password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":rol", $this->rol);
        $stmt->bindParam(":activo", $this->activo);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Login de usuario
     * @return boolean True si las credenciales son correctas
     */
    public function login()
    {
        $query = "SELECT id, nombre, email, password, telefono, rol, activo 
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
            $this->rol = $row['rol'];
            $this->activo = $row['activo'];
            return true;
        }

        return false;
    }

    /**
     * Obtener usuario por ID
     * @return array|false Array con datos del usuario o false
     */
    public function obtenerPorId()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuario por email
     * @param string $email Email del usuario
     * @return array|false Array con datos del usuario o false
     */
    public function obtenerPorEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todos los usuarios
     * @return PDOStatement
     */
    public function listar()
    {
        $query = "SELECT id, nombre, email, telefono, rol, fecha_registro, activo 
                  FROM " . $this->table . " 
                  ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Listar usuarios por rol
     * @param string $rol Rol a filtrar
     * @return PDOStatement
     */
    public function listarPorRol($rol)
    {
        $query = "SELECT id, nombre, email, telefono, rol, fecha_registro, activo 
                  FROM " . $this->table . " 
                  WHERE rol = :rol AND activo = TRUE
                  ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rol", $rol);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Actualizar usuario
     * @return boolean True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      email = :email, 
                      telefono = :telefono,
                      rol = :rol,
                      activo = :activo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":rol", $this->rol);
        $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar contraseña
     * @param string $nueva_password Nueva contraseña
     * @return boolean True si se cambió correctamente
     */
    public function cambiarPassword($nueva_password)
    {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($nueva_password, PASSWORD_BCRYPT);

        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar contraseña: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado (activar/desactivar)
     * @param int $id ID del usuario
     * @param boolean $estado Nuevo estado
     * @return boolean True si se cambió correctamente
     */
    public function cambiarEstado($id, $estado)
    {
        $query = "UPDATE " . $this->table . " 
                  SET activo = :activo 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":activo", $estado, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar usuario (DELETE real)
     * @return boolean True si se eliminó correctamente
     */
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el email ya existe
     * @param string $email Email a verificar
     * @param int|null $exclude_id ID a excluir (para updates)
     * @return boolean True si el email ya existe
     */
    public function emailExiste($email, $exclude_id = null)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);

        if ($exclude_id) {
            $stmt->bindParam(":exclude_id", $exclude_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Contar usuarios por rol
     * @param string $rol Rol a contar
     * @return int Cantidad de usuarios
     */
    public function contarPorRol($rol)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE rol = :rol AND activo = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rol", $rol);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Buscar usuarios
     * @param string $termino Término de búsqueda
     * @return PDOStatement
     */
    public function buscar($termino)
    {
        $query = "SELECT id, nombre, email, telefono, rol, fecha_registro, activo 
                  FROM " . $this->table . " 
                  WHERE (nombre LIKE :termino OR email LIKE :termino OR telefono LIKE :termino)
                  ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $termino_like = "%{$termino}%";
        $stmt->bindParam(":termino", $termino_like);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Actualizar datos del perfil (sin cambiar rol ni password)
     * @return boolean True si se actualizó correctamente
     */
    public function actualizarPerfil()
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre,
                      email = :email,
                      telefono = :telefono
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        // Bind
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage());
            return false;
        }
    }

}
