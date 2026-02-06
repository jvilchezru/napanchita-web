<?php
class Configuracion
{
    private $conn;
    private $table = 'configuracion';

    public $id;
    public $clave;
    public $valor;
    public $descripcion;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Obtener todas las configuraciones
     * @return array
     */
    public function obtenerTodas()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY clave";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener configuración por clave
     * @param string $clave
     * @return string|null
     */
    public function obtenerPorClave($clave)
    {
        $query = "SELECT valor FROM " . $this->table . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['valor'] : null;
    }

    /**
     * Actualizar o insertar configuración
     * @param string $clave
     * @param string $valor
     * @return bool
     */
    public function actualizar($clave, $valor)
    {
        // Verificar si existe
        $query = "SELECT id FROM " . $this->table . " WHERE clave = :clave LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            // Actualizar
            $query = "UPDATE " . $this->table . " 
                      SET valor = :valor 
                      WHERE clave = :clave";
        } else {
            // Insertar
            $query = "INSERT INTO " . $this->table . " 
                      (clave, valor) 
                      VALUES (:clave, :valor)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':valor', $valor);
        
        return $stmt->execute();
    }

    /**
     * Crear configuración con descripción
     * @param string $clave
     * @param string $valor
     * @param string $descripcion
     * @return bool
     */
    public function crear($clave, $valor, $descripcion = null)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (clave, valor, descripcion) 
                  VALUES (:clave, :valor, :descripcion)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':descripcion', $descripcion);
        
        return $stmt->execute();
    }

    /**
     * Eliminar configuración
     * @param string $clave
     * @return bool
     */
    public function eliminar($clave)
    {
        $query = "DELETE FROM " . $this->table . " WHERE clave = :clave";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        
        return $stmt->execute();
    }

    /**
     * Alias de obtenerPorClave - Para compatibilidad
     * @param string $clave
     * @return string|null
     */
    public function obtener($clave)
    {
        return $this->obtenerPorClave($clave);
    }
}
