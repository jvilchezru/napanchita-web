<?php

/**
 * Modelo ZonaDelivery
 * Gestiona las zonas de cobertura de delivery
 * Sistema Napanchita - Módulo Delivery
 */
class ZonaDelivery
{
    private $conn;
    private $table = "zonas_delivery";

    public $id;
    public $nombre;
    public $descripcion;
    public $costo_envio;
    public $tiempo_estimado;
    public $activo;
    public $orden;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear nueva zona
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, descripcion, costo_envio, tiempo_estimado, activo, orden) 
                  VALUES (:nombre, :descripcion, :costo_envio, :tiempo_estimado, :activo, :orden)";

        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":costo_envio", $this->costo_envio);
        $stmt->bindParam(":tiempo_estimado", $this->tiempo_estimado);
        $stmt->bindParam(":activo", $this->activo);
        $stmt->bindParam(":orden", $this->orden);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear zona: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todas las zonas
     */
    public function listar($solo_activas = false)
    {
        $query = "SELECT * FROM " . $this->table;

        if ($solo_activas) {
            $query .= " WHERE activo = 1";
        }

        $query .= " ORDER BY orden ASC, nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener zona por ID
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
     * Actualizar zona
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      descripcion = :descripcion, 
                      costo_envio = :costo_envio, 
                      tiempo_estimado = :tiempo_estimado, 
                      activo = :activo, 
                      orden = :orden 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":costo_envio", $this->costo_envio);
        $stmt->bindParam(":tiempo_estimado", $this->tiempo_estimado);
        $stmt->bindParam(":activo", $this->activo);
        $stmt->bindParam(":orden", $this->orden);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    /**
     * Eliminar zona
     */
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function cambiarEstado()
    {
        $query = "UPDATE " . $this->table . " 
                  SET activo = :activo 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":activo", $this->activo);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    /**
     * Contar zonas activas
     */
    public function contarActivas()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
