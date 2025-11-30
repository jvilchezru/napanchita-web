<?php

/**
 * Modelo Reserva
 * Gestiona operaciones CRUD de reservas de mesas
 */
class Reserva
{
    private $db;
    private $table = 'reservas';

    public $id;
    public $cliente_id;
    public $mesa_id;
    public $fecha;
    public $hora;
    public $personas;
    public $codigo_confirmacion;
    public $estado;
    public $notas;
    public $creado_por_usuario_id;
    public $fecha_creacion;

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
     * Listar todas las reservas con información relacionada
     */
    public function listar($filtros = [])
    {
        $query = "SELECT 
                    r.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    m.numero as mesa_numero,
                    m.capacidad as mesa_capacidad,
                    u.nombre as creado_por_nombre
                FROM " . $this->table . " r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN mesas m ON r.mesa_id = m.id
                LEFT JOIN usuarios u ON r.creado_por_usuario_id = u.id
                WHERE 1=1";

        $params = [];

        // Filtro por estado
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $query .= " AND r.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        // Filtro por fecha
        if (isset($filtros['fecha']) && $filtros['fecha'] !== '') {
            $query .= " AND r.fecha = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }

        // Filtro por cliente
        if (isset($filtros['cliente_id']) && $filtros['cliente_id'] !== '') {
            $query .= " AND r.cliente_id = :cliente_id";
            $params[':cliente_id'] = $filtros['cliente_id'];
        }

        // Filtro por rango de fechas
        if (isset($filtros['fecha_desde']) && $filtros['fecha_desde'] !== '') {
            $query .= " AND r.fecha >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }

        if (isset($filtros['fecha_hasta']) && $filtros['fecha_hasta'] !== '') {
            $query .= " AND r.fecha <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }

        $query .= " ORDER BY r.fecha DESC, r.hora DESC";

        $stmt = $this->db->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener reserva por ID
     */
    public function obtenerPorId($id)
    {
        $query = "SELECT 
                    r.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email,
                    m.numero as mesa_numero,
                    m.capacidad as mesa_capacidad,
                    u.nombre as creado_por_nombre
                FROM " . $this->table . " r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN mesas m ON r.mesa_id = m.id
                LEFT JOIN usuarios u ON r.creado_por_usuario_id = u.id
                WHERE r.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener reserva por código de confirmación
     */
    public function obtenerPorCodigo($codigo)
    {
        $query = "SELECT 
                    r.*,
                    c.nombre as cliente_nombre,
                    c.telefono as cliente_telefono,
                    m.numero as mesa_numero,
                    m.capacidad as mesa_capacidad
                FROM " . $this->table . " r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN mesas m ON r.mesa_id = m.id
                WHERE r.codigo_confirmacion = :codigo
                LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar disponibilidad de mesa
     */
    public function verificarDisponibilidad($mesa_id, $fecha, $hora, $reserva_id = null)
    {
        $query = "SELECT COUNT(*) as count 
                FROM " . $this->table . " 
                WHERE mesa_id = :mesa_id 
                AND fecha = :fecha 
                AND hora = :hora
                AND estado IN ('pendiente', 'confirmada')";

        if ($reserva_id) {
            $query .= " AND id != :reserva_id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':mesa_id', $mesa_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        
        if ($reserva_id) {
            $stmt->bindParam(':reserva_id', $reserva_id);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] == 0;
    }

    /**
     * Generar código de confirmación único
     */
    private function generarCodigoConfirmacion()
    {
        do {
            $codigo = 'RES-' . strtoupper(substr(uniqid(), -6));
            
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE codigo_confirmacion = :codigo";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } while ($result['count'] > 0);

        return $codigo;
    }

    /**
     * Verificar si un cliente ya tiene una reserva activa
     */
    public function clienteTieneReservaActiva($cliente_id, $fecha)
    {
        $query = "SELECT COUNT(*) as count 
                FROM " . $this->table . " 
                WHERE cliente_id = :cliente_id 
                AND fecha = :fecha
                AND estado IN ('pendiente', 'confirmada')";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * Crear nueva reserva
     */
    public function crear()
    {
        // Validar que personas sea mayor a 0
        if (!$this->personas || $this->personas < 1) {
            return ['success' => false, 'message' => 'La cantidad de personas debe ser mayor a 0'];
        }

        // Verificar que el cliente no tenga ya una reserva activa para esa fecha
        if ($this->clienteTieneReservaActiva($this->cliente_id, $this->fecha)) {
            return ['success' => false, 'message' => 'El cliente ya tiene una reserva activa para esta fecha'];
        }

        // Verificar disponibilidad de la mesa
        if (!$this->verificarDisponibilidad($this->mesa_id, $this->fecha, $this->hora)) {
            return ['success' => false, 'message' => 'La mesa no está disponible para esa fecha y hora'];
        }

        // Verificar que la mesa esté activa y disponible
        $queryMesa = "SELECT estado, activo FROM mesas WHERE id = :mesa_id";
        $stmtMesa = $this->db->prepare($queryMesa);
        $stmtMesa->bindParam(':mesa_id', $this->mesa_id);
        $stmtMesa->execute();
        $mesa = $stmtMesa->fetch(PDO::FETCH_ASSOC);

        if (!$mesa || !$mesa['activo']) {
            return ['success' => false, 'message' => 'La mesa seleccionada no está activa'];
        }

        if ($mesa['estado'] !== 'disponible') {
            return ['success' => false, 'message' => 'La mesa seleccionada no está disponible'];
        }

        // Generar código de confirmación
        if (!$this->codigo_confirmacion) {
            $this->codigo_confirmacion = $this->generarCodigoConfirmacion();
        }

        $query = "INSERT INTO " . $this->table . " 
                    (cliente_id, mesa_id, fecha, hora, personas, codigo_confirmacion, 
                     estado, notas, creado_por_usuario_id) 
                    VALUES 
                    (:cliente_id, :mesa_id, :fecha, :hora, :personas, :codigo_confirmacion,
                     :estado, :notas, :creado_por_usuario_id)";

        $stmt = $this->db->prepare($query);

        // Sanitizar datos
        $this->notas = htmlspecialchars(strip_tags($this->notas ?? ''));
        
        // Asegurar que personas sea un entero válido
        $this->personas = (int)$this->personas;

        // Bind de parámetros
        $stmt->bindParam(':cliente_id', $this->cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(':mesa_id', $this->mesa_id, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora', $this->hora);
        $stmt->bindParam(':personas', $this->personas, PDO::PARAM_INT);
        $stmt->bindParam(':codigo_confirmacion', $this->codigo_confirmacion);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':creado_por_usuario_id', $this->creado_por_usuario_id);

        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            
            // Actualizar estado de la mesa si la reserva es para hoy
            if ($this->fecha == date('Y-m-d') && $this->estado == 'confirmada') {
                $mesaModel = new Mesa($this->db);
                $mesaModel->cambiarEstado($this->mesa_id, 'reservada');
            }
            
            return [
                'success' => true, 
                'message' => 'Reserva creada exitosamente',
                'codigo' => $this->codigo_confirmacion,
                'id' => $this->id
            ];
        }

        return ['success' => false, 'message' => 'Error al crear la reserva'];
    }

    /**
     * Actualizar reserva
     */
    public function actualizar()
    {
        // Validar que personas sea mayor a 0
        if (!$this->personas || $this->personas < 1) {
            return ['success' => false, 'message' => 'La cantidad de personas debe ser mayor a 0'];
        }

        // Obtener la reserva actual para verificar si cambió la mesa
        $reservaActual = $this->obtenerPorId($this->id);
        if (!$reservaActual) {
            return ['success' => false, 'message' => 'Reserva no encontrada'];
        }

        $mesaAnterior = $reservaActual['mesa_id'];
        $cambioDeMesa = ($mesaAnterior != $this->mesa_id);

        // Verificar disponibilidad (excluyendo esta reserva)
        if (!$this->verificarDisponibilidad($this->mesa_id, $this->fecha, $this->hora, $this->id)) {
            return ['success' => false, 'message' => 'La mesa no está disponible para esa fecha y hora'];
        }

        $query = "UPDATE " . $this->table . " 
                    SET cliente_id = :cliente_id,
                        mesa_id = :mesa_id,
                        fecha = :fecha,
                        hora = :hora,
                        personas = :personas,
                        estado = :estado,
                        notas = :notas
                    WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Sanitizar datos
        $this->notas = htmlspecialchars(strip_tags($this->notas ?? ''));
        
        // Asegurar que personas sea un entero válido
        $this->personas = (int)$this->personas;

        // Bind de parámetros
        $stmt->bindParam(':cliente_id', $this->cliente_id, PDO::PARAM_INT);
        $stmt->bindParam(':mesa_id', $this->mesa_id, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':hora', $this->hora);
        $stmt->bindParam(':personas', $this->personas, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Si cambió de mesa y la reserva está confirmada, actualizar estado de las mesas
            if ($cambioDeMesa && $reservaActual['estado'] == 'confirmada' && $this->fecha == date('Y-m-d')) {
                $mesaModel = new Mesa($this->db);
                
                // Liberar la mesa anterior
                $mesaModel->cambiarEstado($mesaAnterior, 'disponible');
                
                // Reservar la nueva mesa
                $mesaModel->cambiarEstado($this->mesa_id, 'reservada');
            }

            // Si cambió el estado a completada, actualizar la mesa a ocupada
            if ($this->estado == 'completada' && $reservaActual['estado'] != 'completada') {
                $mesaModel = new Mesa($this->db);
                $mesaModel->cambiarEstado($this->mesa_id, 'ocupada');
            }

            return ['success' => true, 'message' => 'Reserva actualizada exitosamente'];
        }

        return ['success' => false, 'message' => 'Error al actualizar la reserva'];
    }

    /**
     * Cambiar estado de reserva
     */
    public function cambiarEstado($id, $nuevoEstado)
    {
        $estadosValidos = ['pendiente', 'confirmada', 'cancelada', 'completada', 'no_show'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return ['success' => false, 'message' => 'Estado no válido'];
        }

        // Obtener información de la reserva
        $reserva = $this->obtenerPorId($id);
        if (!$reserva) {
            return ['success' => false, 'message' => 'Reserva no encontrada'];
        }

        $query = "UPDATE " . $this->table . " 
                    SET estado = :estado 
                    WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            // Actualizar estado de la mesa según el nuevo estado
            $mesaModel = new Mesa($this->db);
            
            if ($nuevoEstado == 'confirmada' && $reserva['fecha'] == date('Y-m-d')) {
                $mesaModel->cambiarEstado($reserva['mesa_id'], 'reservada');
            } elseif ($nuevoEstado == 'completada') {
                // Cuando se completa la reserva, la mesa pasa a ocupada para crear el pedido
                $mesaModel->cambiarEstado($reserva['mesa_id'], 'ocupada');
            } elseif (in_array($nuevoEstado, ['cancelada', 'no_show'])) {
                // Liberar la mesa
                $mesaModel->cambiarEstado($reserva['mesa_id'], 'disponible');
            }

            return ['success' => true, 'message' => 'Estado actualizado exitosamente'];
        }

        return ['success' => false, 'message' => 'Error al cambiar el estado'];
    }

    /**
     * Cancelar reserva
     */
    public function cancelar($id)
    {
        return $this->cambiarEstado($id, 'cancelada');
    }

    /**
     * Confirmar reserva
     */
    public function confirmar($id)
    {
        return $this->cambiarEstado($id, 'confirmada');
    }

    /**
     * Completar reserva (cliente llegó)
     */
    public function completar($id)
    {
        return $this->cambiarEstado($id, 'completada');
    }

    /**
     * Obtener reservas del día
     */
    public function obtenerReservasDelDia($fecha = null)
    {
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }

        return $this->listar(['fecha' => $fecha]);
    }

    /**
     * Obtener reservas próximas (siguientes 7 días)
     */
    public function obtenerReservasProximas()
    {
        $hoy = date('Y-m-d');
        $finSemana = date('Y-m-d', strtotime('+7 days'));

        return $this->listar([
            'fecha_desde' => $hoy,
            'fecha_hasta' => $finSemana
        ]);
    }

    /**
     * Obtener estadísticas de reservas
     */
    public function obtenerEstadisticas($fecha_desde = null, $fecha_hasta = null)
    {
        if (!$fecha_desde) {
            $fecha_desde = date('Y-m-01'); // Primer día del mes
        }
        if (!$fecha_hasta) {
            $fecha_hasta = date('Y-m-t'); // Último día del mes
        }

        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) as confirmadas,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as cancelada,
                    SUM(CASE WHEN estado = 'no_show' THEN 1 ELSE 0 END) as no_show,
                    SUM(personas) as total_personas
                FROM " . $this->table . " 
                WHERE fecha BETWEEN :fecha_desde AND :fecha_hasta";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha_desde', $fecha_desde);
        $stmt->bindParam(':fecha_hasta', $fecha_hasta);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar reserva (soft delete - cancelar)
     */
    public function eliminar($id)
    {
        return $this->cancelar($id);
    }
}
