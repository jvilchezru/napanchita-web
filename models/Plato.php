<?php

/**
 * Modelo Plato
 * Gestiona las operaciones CRUD de platos
 * Sistema Napanchita
 */
class Plato
{
    private $conn;
    private $table = "platos";

    // Propiedades del plato
    public $id;
    public $categoria_id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $imagen_url;
    public $disponible;
    public $fecha_creacion;

    /**
     * Constructor
     * @param PDO $db Conexión a base de datos
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Crear nuevo plato
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (categoria_id, nombre, descripcion, precio, imagen_url, disponible) 
                  VALUES (:categoria_id, :nombre, :descripcion, :precio, :imagen_url, :disponible)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->imagen_url = htmlspecialchars(strip_tags($this->imagen_url ?? ''));

        // Bind de parámetros
        $stmt->bindParam(":categoria_id", $this->categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":imagen_url", $this->imagen_url);
        $stmt->bindParam(":disponible", $this->disponible, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear plato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener plato por ID
     * @return array|false Array con datos del plato o false
     */
    public function obtenerPorId()
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.categoria_id = c.id 
                  WHERE p.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todos los platos
     * @param bool $solo_disponibles Si es true, solo devuelve platos disponibles
     * @return array Array de platos
     */
    public function listar($solo_disponibles = false)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.categoria_id = c.id";

        if ($solo_disponibles) {
            $query .= " WHERE p.disponible = TRUE";
        }

        $query .= " ORDER BY c.orden ASC, p.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar platos por categoría
     * @param int $categoria_id ID de la categoría
     * @param bool $solo_disponibles Si es true, solo devuelve platos disponibles
     * @return array Array de platos
     */
    public function listarPorCategoria($categoria_id, $solo_disponibles = false)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.categoria_id = c.id 
                  WHERE p.categoria_id = :categoria_id";

        if ($solo_disponibles) {
            $query .= " AND p.disponible = TRUE";
        }

        $query .= " ORDER BY p.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":categoria_id", $categoria_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar plato
     * @return boolean True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET categoria_id = :categoria_id,
                      nombre = :nombre, 
                      descripcion = :descripcion,
                      precio = :precio,
                      imagen_url = :imagen_url,
                      disponible = :disponible
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->imagen_url = htmlspecialchars(strip_tags($this->imagen_url ?? ''));

        // Bind de parámetros
        $stmt->bindParam(":categoria_id", $this->categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":imagen_url", $this->imagen_url);
        $stmt->bindParam(":disponible", $this->disponible, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar plato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado (disponible/no disponible)
     * @param int $id ID del plato
     * @param boolean $estado Nuevo estado
     * @return boolean True si se cambió correctamente
     */
    public function cambiarEstado($id, $estado)
    {
        $query = "UPDATE " . $this->table . " 
                  SET disponible = :disponible 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":disponible", $estado, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de plato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar plato (DELETE real)
     * @return boolean True si se eliminó correctamente
     */
    public function eliminar()
    {
        // Primero eliminar la imagen si existe
        if ($this->imagen_url && file_exists($this->imagen_url)) {
            unlink($this->imagen_url);
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar plato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar platos
     * @param string $termino Término de búsqueda
     * @return array Array de platos
     */
    public function buscar($termino)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                  FROM " . $this->table . " p 
                  LEFT JOIN categorias c ON p.categoria_id = c.id 
                  WHERE p.nombre LIKE :termino 
                  OR p.descripcion LIKE :termino
                  OR c.nombre LIKE :termino
                  ORDER BY p.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $termino_like = "%{$termino}%";
        $stmt->bindParam(":termino", $termino_like);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar platos totales
     * @return int Cantidad de platos
     */
    public function contar()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Obtener platos más vendidos
     * @param int $limit Cantidad de platos a devolver
     * @return array Array de platos
     */
    public function obtenerMasVendidos($limit = 10)
    {
        $query = "SELECT p.*, c.nombre as categoria_nombre, 
                  COUNT(pi.id) as veces_vendido,
                  SUM(pi.cantidad) as cantidad_total
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  LEFT JOIN pedido_items pi ON p.id = pi.plato_id
                  LEFT JOIN pedidos ped ON pi.pedido_id = ped.id
                  WHERE ped.estado != 'cancelado' OR ped.id IS NULL
                  GROUP BY p.id
                  ORDER BY cantidad_total DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
