<?php

/**
 * Modelo Mesa
 * Gestiona operaciones CRUD de mesas del restaurante
 */
class Mesa
{
    private $db;
    private $table = 'mesas';

    public $id;
    public $numero;
    public $capacidad;
    public $estado;
    public $posicion_x;
    public $posicion_y;
    public $activo;

    /**
     * Constructor
     */
    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    /**
     * Listar todas las mesas con filtros opcionales
     * 
     * @param bool $soloActivos Solo mesas activas
     * @param string $estado Filtrar por estado
     * @param int $capacidadMin Capacidad mínima
     * @return array Lista de mesas
     */
    public function listar($soloActivos = false, $estado = null, $capacidadMin = null)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";

        if ($soloActivos) {
            $query .= " AND activo = 1";
        }

        if ($estado) {
            $query .= " AND estado = :estado";
        }

        if ($capacidadMin !== null) {
            $query .= " AND capacidad >= :capacidad_min";
        }

        $query .= " ORDER BY numero ASC";

        $stmt = $this->db->prepare($query);

        if ($estado) {
            $stmt->bindParam(':estado', $estado);
        }

        if ($capacidadMin !== null) {
            $stmt->bindParam(':capacidad_min', $capacidadMin);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar mesas disponibles para pedidos
     * Solo mesas activas y en estado 'disponible'
     * 
     * @return array Lista de mesas disponibles
     */
    public function listarDisponibles()
    {
        $query = "SELECT * FROM " . $this->table . " 
                    WHERE activo = 1 AND estado = 'disponible'
                    ORDER BY numero ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar mesas activas (todas las activas sin importar estado)
     * 
     * @return array Lista de mesas activas
     */
    public function listarActivas()
    {
        $query = "SELECT * FROM " . $this->table . " 
                    WHERE activo = 1 
                    ORDER BY numero ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar mesas disponibles para reservas
     * Solo mesas activas y que no estén ocupadas o reservadas
     * 
     * @param int|null $mesaActualId ID de mesa actual (para edición) que debe incluirse aunque esté reservada
     * @return array Lista de mesas disponibles para reservar
     */
    public function listarDisponiblesParaReserva($mesaActualId = null)
    {
        $query = "SELECT m.* FROM " . $this->table . " m
                    WHERE m.activo = 1 
                    AND (m.estado = 'disponible' OR m.id = :mesa_actual_id1)
                    AND m.id NOT IN (
                        SELECT mesa_id FROM reservas 
                        WHERE estado IN ('pendiente', 'confirmada')
                        AND fecha = CURDATE()
                        AND mesa_id != :mesa_actual_id2
                    )
                    ORDER BY m.numero ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':mesa_actual_id1', $mesaActualId, PDO::PARAM_INT);
        $stmt->bindParam(':mesa_actual_id2', $mesaActualId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener mesa por ID
     * 
     * @return array|null Datos de la mesa
     */
    public function obtenerPorId()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->numero = $row['numero'];
            $this->capacidad = $row['capacidad'];
            $this->estado = $row['estado'];
            $this->posicion_x = $row['posicion_x'];
            $this->posicion_y = $row['posicion_y'];
            $this->activo = $row['activo'];
        }

        return $row;
    }

    /**
     * Obtener mesa por número
     * 
     * @param string $numero Número de mesa
     * @return array|null Datos de la mesa
     */
    public function obtenerPorNumero($numero)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE numero = :numero LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numero', $numero);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nueva mesa
     * 
     * @return bool True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                    (numero, capacidad, estado, posicion_x, posicion_y, activo) 
                    VALUES 
                    (:numero, :capacidad, :estado, :posicion_x, :posicion_y, :activo)";

        $stmt = $this->db->prepare($query);

        // Sanitizar datos
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));
        $this->estado = htmlspecialchars(strip_tags($this->estado));

        // Bind de parámetros
        $stmt->bindParam(':numero', $this->numero);
        $stmt->bindParam(':capacidad', $this->capacidad);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':posicion_x', $this->posicion_x);
        $stmt->bindParam(':posicion_y', $this->posicion_y);
        $stmt->bindParam(':activo', $this->activo);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Actualizar mesa existente
     * 
     * @return bool True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                    SET numero = :numero,
                        capacidad = :capacidad,
                        estado = :estado,
                        posicion_x = :posicion_x,
                        posicion_y = :posicion_y,
                        activo = :activo
                    WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Sanitizar datos
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));
        $this->estado = htmlspecialchars(strip_tags($this->estado));

        // Bind de parámetros
        $stmt->bindParam(':numero', $this->numero);
        $stmt->bindParam(':capacidad', $this->capacidad);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':posicion_x', $this->posicion_x);
        $stmt->bindParam(':posicion_y', $this->posicion_y);
        $stmt->bindParam(':activo', $this->activo);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Cambiar estado de una mesa
     * 
     * @param int $id ID de la mesa
     * @param string $nuevoEstado Nuevo estado
     * @return bool True si se cambió correctamente
     */
    public function cambiarEstado($id, $nuevoEstado)
    {
        $query = "UPDATE " . $this->table . " 
                    SET estado = :estado 
                    WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Actualizar posición de una mesa en el layout
     * 
     * @param int $id ID de la mesa
     * @param int $x Posición X
     * @param int $y Posición Y
     * @return bool True si se actualizó correctamente
     */
    public function actualizarPosicion($id, $x, $y)
    {
        $query = "UPDATE " . $this->table . " 
                    SET posicion_x = :x, posicion_y = :y 
                    WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':x', $x);
        $stmt->bindParam(':y', $y);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Eliminar mesa (soft delete - desactivar)
     * 
     * @return bool True si se eliminó correctamente
     */
    public function eliminar()
    {
        $query = "UPDATE " . $this->table . " 
                    SET activo = 0, estado = 'inactiva' 
                    WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Contar mesas por estado
     * 
     * @return array Contadores por estado
     */
    public function contarPorEstado()
    {
        $query = "SELECT estado, COUNT(*) as total 
                    FROM " . $this->table . " 
                    WHERE activo = 1 
                    GROUP BY estado";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $resultado = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultado[$row['estado']] = $row['total'];
        }

        return $resultado;
    }

    /**
     * Obtener estadísticas de mesas
     * 
     * @return array Estadísticas
     */
    public function obtenerEstadisticas()
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN estado = 'ocupada' THEN 1 ELSE 0 END) as ocupadas,
                    SUM(CASE WHEN estado = 'reservada' THEN 1 ELSE 0 END) as reservadas,
                    SUM(CASE WHEN capacidad <= 4 THEN 1 ELSE 0 END) as pequenas,
                    SUM(CASE WHEN capacidad > 4 THEN 1 ELSE 0 END) as grandes
                    FROM " . $this->table . " 
                    WHERE activo = 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
