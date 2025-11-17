<?php

/**
 * Modelo Cliente
 * Gestiona las operaciones CRUD de clientes externos (sin acceso al sistema)
 * Sistema Napanchita
 */
class Cliente
{
    private $conn;
    private $table = "clientes";

    // Propiedades del cliente
    public $id;
    public $nombre;
    public $telefono;
    public $email;
    public $direcciones; // JSON
    public $notas;
    public $fecha_registro;
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
     * Crear nuevo cliente
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, telefono, email, direcciones, notas, activo) 
                  VALUES (:nombre, :telefono, :email, :direcciones, :notas, :activo)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = $this->email ? htmlspecialchars(strip_tags($this->email)) : null;
        $this->notas = $this->notas ? htmlspecialchars(strip_tags($this->notas)) : null;

        // Convertir direcciones a JSON si es array
        if (is_array($this->direcciones)) {
            $this->direcciones = json_encode($this->direcciones, JSON_UNESCAPED_UNICODE);
        }

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":direcciones", $this->direcciones);
        $stmt->bindParam(":notas", $this->notas);
        $stmt->bindParam(":activo", $this->activo);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener cliente por ID
     * @return array|false Array con datos del cliente o false
     */
    public function obtenerPorId()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['direcciones']) {
            $row['direcciones'] = json_decode($row['direcciones'], true);
        }

        return $row;
    }

    /**
     * Obtener cliente por teléfono
     * @param string $telefono Teléfono del cliente
     * @return array|false Array con datos del cliente o false
     */
    public function obtenerPorTelefono($telefono)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE telefono = :telefono LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['direcciones']) {
            $row['direcciones'] = json_decode($row['direcciones'], true);
        }

        return $row;
    }

    /**
     * Listar todos los clientes activos
     * @return PDOStatement
     */
    public function listar()
    {
        $query = "SELECT id, nombre, telefono, email, fecha_registro, activo 
                  FROM " . $this->table . " 
                  WHERE activo = TRUE
                  ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Buscar clientes
     * @param string $termino Término de búsqueda
     * @return PDOStatement
     */
    public function buscar($termino)
    {
        $query = "SELECT id, nombre, telefono, email, fecha_registro 
                  FROM " . $this->table . " 
                  WHERE activo = TRUE 
                  AND (nombre LIKE :termino OR telefono LIKE :termino OR email LIKE :termino)
                  ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $termino_like = "%{$termino}%";
        $stmt->bindParam(":termino", $termino_like);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Actualizar cliente
     * @return boolean True si se actualizó correctamente
     */
    public function actualizar()
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      telefono = :telefono, 
                      email = :email,
                      direcciones = :direcciones,
                      notas = :notas,
                      activo = :activo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = $this->email ? htmlspecialchars(strip_tags($this->email)) : null;
        $this->notas = $this->notas ? htmlspecialchars(strip_tags($this->notas)) : null;

        // Convertir direcciones a JSON si es array
        if (is_array($this->direcciones)) {
            $this->direcciones = json_encode($this->direcciones, JSON_UNESCAPED_UNICODE);
        }

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":direcciones", $this->direcciones);
        $stmt->bindParam(":notas", $this->notas);
        $stmt->bindParam(":activo", $this->activo);
        $stmt->bindParam(":id", $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Agregar dirección a cliente
     * @param array $direccion Nueva dirección
     * @return boolean True si se agregó correctamente
     */
    public function agregarDireccion($direccion)
    {
        // Obtener cliente actual
        $cliente = $this->obtenerPorId();
        if (!$cliente) {
            return false;
        }

        $direcciones = $cliente['direcciones'] ?? [];

        // Asignar ID a la nueva dirección
        $direccion['id'] = count($direcciones) + 1;

        // Si es la primera dirección, marcarla como principal
        if (empty($direcciones)) {
            $direccion['principal'] = true;
        }

        $direcciones[] = $direccion;

        // Actualizar
        $this->direcciones = $direcciones;
        return $this->actualizar();
    }

    /**
     * Eliminar dirección
     * @param int $direccion_id ID de la dirección a eliminar
     * @return boolean True si se eliminó correctamente
     */
    public function eliminarDireccion($direccion_id)
    {
        // Obtener cliente actual
        $cliente = $this->obtenerPorId();
        if (!$cliente) {
            return false;
        }

        $direcciones = $cliente['direcciones'] ?? [];

        // Filtrar dirección
        $direcciones = array_filter($direcciones, function ($dir) use ($direccion_id) {
            return $dir['id'] != $direccion_id;
        });

        // Reindexar
        $direcciones = array_values($direcciones);

        // Actualizar
        $this->direcciones = $direcciones;
        return $this->actualizar();
    }

    /**
     * Cambiar estado (activar/desactivar)
     * @param int $id ID del cliente
     * @param boolean $estado Nuevo estado
     * @return boolean True si se cambió correctamente
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
            error_log("Error al cambiar estado de cliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar cliente (soft delete)
     * @return boolean True si se eliminó correctamente
     */
    public function eliminar()
    {
        return $this->cambiarEstado($this->id, false);
    }

    /**
     * Obtener clientes frecuentes
     * @param int $limite Cantidad de clientes a retornar
     * @return PDOStatement
     */
    public function obtenerFrecuentes($limite = 10)
    {
        $query = "SELECT c.id, c.nombre, c.telefono, c.email, 
                         COUNT(p.id) as total_pedidos,
                         SUM(p.total) as total_gastado
                  FROM " . $this->table . " c
                  JOIN pedidos p ON c.id = p.cliente_id
                  WHERE c.activo = TRUE AND p.estado != 'cancelado'
                  GROUP BY c.id
                  ORDER BY total_pedidos DESC
                  LIMIT :limite";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Contar total de clientes activos
     * @return int Cantidad de clientes
     */
    public function contar()
    {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE activo = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
