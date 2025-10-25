<?php
/**
 * Configuración de conexión a la base de datos
 */
class Database {
    private $host = "localhost";
    private $db_name = "napanchita_db";
    private $username = "root";
    private $password = "";
    private $conn;

    // Obtener la conexión a la base de datos
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage() . 
                "<br>Verifica que MySQL esté corriendo en XAMPP");
        }
        
        return $this->conn;
    }
}
?>
