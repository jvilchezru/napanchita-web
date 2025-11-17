<?php

/**
 * Modelo Combo
 * Gestiona las operaciones CRUD de combos y su relación con productos
 * Sistema Napanchita
 */
class Combo
{
    private $conn;
    private $table = "combos";
    private $table_productos = "combo_productos";

    // Propiedades del combo
    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $imagen_url;
    public $activo;
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
     * Crear nuevo combo
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, descripcion, precio, imagen_url, activo) 
                  VALUES (:nombre, :descripcion, :precio, :imagen_url, :activo)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->imagen_url = htmlspecialchars(strip_tags($this->imagen_url ?? ''));

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":imagen_url", $this->imagen_url);
        $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener combo por ID con sus productos
     * @return array|false Array con datos del combo y productos o false
     */
    public function obtenerPorId()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $combo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($combo) {
            // Obtener productos del combo
            $combo['productos'] = $this->obtenerProductosDelCombo($this->id);
        }

        return $combo;
    }

    /**
     * Obtener productos de un combo
     * @param int $combo_id ID del combo
     * @return array Array de productos con cantidad
     */
    public function obtenerProductosDelCombo($combo_id)
    {
        $query = "SELECT cp.*, p.nombre, p.descripcion, p.precio as precio_unitario, p.imagen_url
                  FROM " . $this->table_productos . " cp
                  INNER JOIN productos p ON cp.producto_id = p.id
                  WHERE cp.combo_id = :combo_id
                  ORDER BY p.nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combo_id", $combo_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todos los combos
     * @param bool $solo_activos Si es true, solo devuelve combos activos
     * @return array Array de combos
     */
    public function listar($solo_activos = false)
    {
        $query = "SELECT * FROM " . $this->table;

        if ($solo_activos) {
            $query .= " WHERE activo = TRUE";
        }

        $query .= " ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $combos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar productos a cada combo
        foreach ($combos as &$combo) {
            $combo['productos'] = $this->obtenerProductosDelCombo($combo['id']);
            $combo['cantidad_productos'] = count($combo['productos']);
        }

        return $combos;
    }

    /**
     * Actualizar combo
     * @return boolean True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      descripcion = :descripcion,
                      precio = :precio,
                      imagen_url = :imagen_url,
                      activo = :activo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->imagen_url = htmlspecialchars(strip_tags($this->imagen_url ?? ''));

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":imagen_url", $this->imagen_url);
        $stmt->bindParam(":activo", $this->activo, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar estado (activo/inactivo)
     * @param int $id ID del combo
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
            error_log("Error al cambiar estado de combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar combo (DELETE real)
     * @return boolean True si se eliminó correctamente
     */
    public function eliminar()
    {
        // Primero eliminar la imagen si existe
        if ($this->imagen_url && file_exists($this->imagen_url)) {
            unlink($this->imagen_url);
        }

        // Los productos del combo se eliminan automáticamente por CASCADE
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Agregar producto a combo
     * @param int $combo_id ID del combo
     * @param int $producto_id ID del producto
     * @param int $cantidad Cantidad del producto
     * @return boolean True si se agregó correctamente
     */
    public function agregarProducto($combo_id, $producto_id, $cantidad = 1)
    {
        $query = "INSERT INTO " . $this->table_productos . " 
                  (combo_id, producto_id, cantidad) 
                  VALUES (:combo_id, :producto_id, :cantidad)
                  ON DUPLICATE KEY UPDATE cantidad = :cantidad2";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combo_id", $combo_id, PDO::PARAM_INT);
        $stmt->bindParam(":producto_id", $producto_id, PDO::PARAM_INT);
        $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(":cantidad2", $cantidad, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al agregar producto a combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar producto de combo
     * @param int $combo_id ID del combo
     * @param int $producto_id ID del producto
     * @return boolean True si se eliminó correctamente
     */
    public function eliminarProducto($combo_id, $producto_id)
    {
        $query = "DELETE FROM " . $this->table_productos . " 
                  WHERE combo_id = :combo_id AND producto_id = :producto_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combo_id", $combo_id, PDO::PARAM_INT);
        $stmt->bindParam(":producto_id", $producto_id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar producto de combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar productos del combo (eliminar todos y agregar nuevos)
     * @param int $combo_id ID del combo
     * @param array $productos Array de productos con formato ['producto_id' => cantidad]
     * @return boolean True si se actualizó correctamente
     */
    public function actualizarProductos($combo_id, $productos)
    {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();

            // Eliminar todos los productos actuales del combo
            $query = "DELETE FROM " . $this->table_productos . " WHERE combo_id = :combo_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":combo_id", $combo_id, PDO::PARAM_INT);
            $stmt->execute();

            // Agregar nuevos productos
            $query = "INSERT INTO " . $this->table_productos . " 
                      (combo_id, producto_id, cantidad) 
                      VALUES (:combo_id, :producto_id, :cantidad)";
            $stmt = $this->conn->prepare($query);

            foreach ($productos as $producto_id => $cantidad) {
                $stmt->bindParam(":combo_id", $combo_id, PDO::PARAM_INT);
                $stmt->bindParam(":producto_id", $producto_id, PDO::PARAM_INT);
                $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Confirmar transacción
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $this->conn->rollBack();
            error_log("Error al actualizar productos del combo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar combos
     * @param string $termino Término de búsqueda
     * @return array Array de combos
     */
    public function buscar($termino)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE nombre LIKE :termino 
                  OR descripcion LIKE :termino
                  ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $termino_like = "%{$termino}%";
        $stmt->bindParam(":termino", $termino_like);
        $stmt->execute();

        $combos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar productos a cada combo
        foreach ($combos as &$combo) {
            $combo['productos'] = $this->obtenerProductosDelCombo($combo['id']);
            $combo['cantidad_productos'] = count($combo['productos']);
        }

        return $combos;
    }

    /**
     * Contar combos totales
     * @return int Cantidad de combos
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
     * Verificar si el combo tiene productos
     * @param int $combo_id ID del combo
     * @return boolean True si tiene productos
     */
    public function tieneProductos($combo_id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_productos . " 
                  WHERE combo_id = :combo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combo_id", $combo_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }
}
