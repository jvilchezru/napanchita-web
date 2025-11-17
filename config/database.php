<?php

/**
 * Clase Database - Gestión de conexión a MySQL
 * Sistema Napanchita
 */
class Database
{
    private $host = "localhost";
    private $db_name = "napanchita_db";
    private $username = "root";
    private $password = "";
    private $conn = null;

    /**
     * Obtener conexión PDO a la base de datos
     * @return PDO|null Objeto de conexión PDO o null en caso de error
     */
    public function getConnection()
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            // Configurar PDO para que lance excepciones en errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Configurar el charset
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return $this->conn;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Cerrar conexión
     */
    public function closeConnection()
    {
        $this->conn = null;
    }
}
