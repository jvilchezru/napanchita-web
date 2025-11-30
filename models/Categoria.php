<?php

/**
 * Modelo Categoria
 * Gestiona las operaciones CRUD de categorías de platos
 * Sistema Napanchita
 */
class Categoria
{
    private $conn;
    private $table = "categorias";

    // Propiedades de la categoría
    public $id;
    public $nombre;
    public $descripcion;
    public $orden;
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
     * Crear nueva categoría
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, descripcion, orden, activo) 
                  VALUES (:nombre, :descripcion, :orden, :activo)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":orden", $this->orden, PDO::PARAM_INT);
        $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener categoría por ID
     * @return array|false Array con datos de la categoría o false
     */
    public function obtenerPorId()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todas las categorías
     * @param bool $solo_activas Si es true, solo devuelve categorías activas
     * @return array Array de categorías
     */
    public function listar($solo_activas = false)
    {
        $query = "SELECT c.*, 
                  COUNT(p.id) as cantidad_platos
                  FROM " . $this->table . " c
                  LEFT JOIN platos p ON p.categoria_id = c.id";

        if ($solo_activas) {
            $query .= " WHERE c.activo = TRUE";
        }

        $query .= " GROUP BY c.id
                    ORDER BY c.orden ASC, c.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar categoría
     * @return boolean True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      descripcion = :descripcion,
                      orden = :orden,
                      activo = :activo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":orden", $this->orden, PDO::PARAM_INT);
        $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado (activar/desactivar)
     * @param int $id ID de la categoría
     * @param boolean $estado Nuevo estado
     * @return boolean True si se cambió correctamente
     */
    public function cambiarEstado($id, $estado)
    {
        $query = "UPDATE " . $this->table . " 
                  SET activo = :activo 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":activo", $estado, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar categoría (DELETE real)
     * @return boolean True si se eliminó correctamente
     */
    public function eliminar()
    {
        // Verificar si tiene platos asociados
        $query_check = "SELECT COUNT(*) as total FROM platos WHERE categoria_id = :id";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($result['total'] > 0) {
            // No se puede eliminar porque tiene platos asociados
            return false;
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el nombre ya existe
     * @param string $nombre Nombre a verificar
     * @param int|null $exclude_id ID a excluir (para updates)
     * @return boolean True si el nombre ya existe
     */
    public function nombreExiste($nombre, $exclude_id = null)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE nombre = :nombre";

        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);

        if ($exclude_id) {
            $stmt->bindParam(":exclude_id", $exclude_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Contar platos por categoría
     * @param int $categoria_id ID de la categoría
     * @return int Cantidad de platos
     */
    public function contarPlatos($categoria_id)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM platos 
                  WHERE categoria_id = :categoria_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":categoria_id", $categoria_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Obtener el próximo número de orden disponible
     * @return int Siguiente número de orden
     */
    public function obtenerSiguienteOrden()
    {
        $query = "SELECT MAX(orden) as max_orden FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['max_orden'] ?? 0) + 1;
    }

    /**
     * Buscar categorías
     * @param string $termino Término de búsqueda
     * @return array Array de categorías
     */
    public function buscar($termino)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE nombre LIKE :termino 
                  OR descripcion LIKE :termino
                  ORDER BY orden ASC, nombre ASC";

        $stmt = $this->conn->prepare($query);
        $termino_like = "%{$termino}%";
        $stmt->bindParam(":termino", $termino_like);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
