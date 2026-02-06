<?php

/**
 * Modelo Resena
 * Gestiona las reseñas de clientes
 * Sistema Napanchita
 */
class Resena
{
    private $conn;
    private $table = "resenas";

    public $id;
    public $cliente_id;
    public $calificacion;
    public $comentario;
    public $fecha_creacion;
    public $activo;
    public $destacado;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear nueva reseña
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (cliente_id, calificacion, comentario, activo) 
                  VALUES (:cliente_id, :calificacion, :comentario, :activo)";

        $stmt = $this->conn->prepare($query);

        $this->comentario = htmlspecialchars(strip_tags($this->comentario));
        $this->activo = isset($this->activo) ? $this->activo : 1;

        $stmt->bindParam(":cliente_id", $this->cliente_id);
        $stmt->bindParam(":calificacion", $this->calificacion);
        $stmt->bindParam(":comentario", $this->comentario);
        $stmt->bindParam(":activo", $this->activo);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear reseña: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar reseñas activas (para el portal)
     */
    public function listarActivas($limite = 10)
    {
        $query = "SELECT r.*, c.nombre as cliente_nombre, 
                         DATE_FORMAT(r.fecha_creacion, '%d/%m/%Y') as fecha_formateada
                  FROM " . $this->table . " r
                  INNER JOIN clientes c ON r.cliente_id = c.id
                  WHERE r.activo = 1
                  ORDER BY r.destacado DESC, r.fecha_creacion DESC
                  LIMIT :limite";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener reseñas de un cliente
     */
    public function listarPorCliente($cliente_id)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE cliente_id = :cliente_id 
                  ORDER BY fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un cliente ya dejó una reseña
     */
    public function clienteTieneResena($cliente_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE cliente_id = :cliente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cliente_id", $cliente_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }

    /**
     * Obtener estadísticas de reseñas
     */
    public function obtenerEstadisticas()
    {
        $query = "SELECT 
                    COUNT(*) as total_resenas,
                    AVG(calificacion) as promedio_calificacion,
                    SUM(CASE WHEN calificacion = 5 THEN 1 ELSE 0 END) as cinco_estrellas,
                    SUM(CASE WHEN calificacion = 4 THEN 1 ELSE 0 END) as cuatro_estrellas,
                    SUM(CASE WHEN calificacion = 3 THEN 1 ELSE 0 END) as tres_estrellas,
                    SUM(CASE WHEN calificacion = 2 THEN 1 ELSE 0 END) as dos_estrellas,
                    SUM(CASE WHEN calificacion = 1 THEN 1 ELSE 0 END) as una_estrella
                FROM " . $this->table . " 
                WHERE activo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cambiar estado de reseña (activar/desactivar)
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
            error_log("Error al cambiar estado de reseña: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar reseña como destacada
     */
    public function marcarDestacado($id, $destacado)
    {
        $query = "UPDATE " . $this->table . " 
                  SET destacado = :destacado 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":destacado", $destacado, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al marcar reseña como destacada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar reseña
     */
    public function eliminar($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar reseña: " . $e->getMessage());
            return false;
        }
    }
}
