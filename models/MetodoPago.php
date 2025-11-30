<?php
require_once __DIR__ . '/../config/database.php';

class MetodoPago {
    private $conn;
    private $table = 'metodos_pago';

    public $id;
    public $nombre;
    public $descripcion;
    public $activo;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Listar todos los métodos de pago
     */
    public function listar($filtros = []) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (isset($filtros['activo'])) {
            $query .= " AND activo = :activo";
            $params[':activo'] = $filtros['activo'];
        }

        if (isset($filtros['buscar']) && $filtros['buscar']) {
            $query .= " AND nombre LIKE :buscar";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }

        $query .= " ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar solo métodos de pago activos
     */
    public function listarActivos() {
        return $this->listar(['activo' => 1]);
    }

    /**
     * Obtener método de pago por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo método de pago
     */
    public function crear() {
        try {
            // Verificar si ya existe
            $query = "SELECT id FROM {$this->table} WHERE nombre = :nombre";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un método de pago con ese nombre'
                ];
            }

            $query = "INSERT INTO {$this->table} 
                      (nombre, descripcion, activo) 
                      VALUES (:nombre, :descripcion, :activo)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':activo', $this->activo);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Método de pago creado correctamente',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear el método de pago'
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar método de pago
     */
    public function actualizar() {
        try {
            // Verificar si ya existe otro con el mismo nombre
            $query = "SELECT id FROM {$this->table} WHERE nombre = :nombre AND id != :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Ya existe otro método de pago con ese nombre'
                ];
            }

            $query = "UPDATE {$this->table} 
                      SET nombre = :nombre,
                          descripcion = :descripcion,
                          activo = :activo
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':activo', $this->activo);
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Método de pago actualizado correctamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar el método de pago'
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar método de pago permanentemente
     */
    public function eliminar($id) {
        try {
            // Verificar si está siendo usado en ventas
            $query = "SELECT COUNT(*) as total FROM ventas WHERE metodo_pago_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado['total'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar. Este método de pago está siendo usado en ' . $resultado['total'] . ' venta(s)'
                ];
            }

            // Eliminar permanentemente de la base de datos
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Método de pago eliminado correctamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al eliminar el método de pago'
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar estado (activo/inactivo)
     */
    public function cambiarEstado($id, $activo) {
        try {
            $query = "UPDATE {$this->table} SET activo = :activo WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':activo', $activo);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Estado actualizado correctamente'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al cambiar el estado'
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
