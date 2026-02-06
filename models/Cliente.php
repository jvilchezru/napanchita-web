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
     */
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Crear nuevo cliente
     * @return boolean True si se creó correctamente
     */
    public function crear()
    {
        // Si tiene password, es una cuenta web
        if (isset($this->password) && !empty($this->password)) {
            $query = "INSERT INTO " . $this->table . " 
                      (nombre, telefono, email, password, tiene_cuenta, email_verificado, direcciones, notas, activo) 
                      VALUES (:nombre, :telefono, :email, :password, :tiene_cuenta, :email_verificado, :direcciones, :notas, :activo)";
        } else {
            $query = "INSERT INTO " . $this->table . " 
                      (nombre, telefono, email, direcciones, notas, activo) 
                      VALUES (:nombre, :telefono, :email, :direcciones, :notas, :activo)";
        }

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

        // Bind de parámetros básicos
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":direcciones", $this->direcciones);
        $stmt->bindParam(":notas", $this->notas);
        $stmt->bindParam(":activo", $this->activo);

        // Si tiene password, agregar parámetros adicionales
        if (isset($this->password) && !empty($this->password)) {
            $stmt->bindParam(":password", $this->password);
            $tiene_cuenta = isset($this->tiene_cuenta) ? $this->tiene_cuenta : 1;
            $email_verificado = isset($this->email_verificado) ? $this->email_verificado : 0;
            $stmt->bindParam(":tiene_cuenta", $tiene_cuenta);
            $stmt->bindParam(":email_verificado", $email_verificado);
        }

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
     * Obtener cliente por nombre
     * @param string $nombre Nombre del cliente
     * @return array|false Datos del cliente o false si no existe
     */
    public function obtenerPorNombre($nombre)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE nombre LIKE :nombre AND activo = TRUE LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $nombreBusqueda = "%{$nombre}%";
        $stmt->bindParam(":nombre", $nombreBusqueda);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['direcciones']) {
            $row['direcciones'] = json_decode($row['direcciones'], true);
        }

        return $row;
    }

    /**
     * Buscar múltiples clientes por nombre (para autocompletado)
     * @param string $nombre Nombre del cliente
     * @return array Array de clientes
     */
    public function buscarPorNombre($nombre)
    {
        $query = "SELECT id, nombre, telefono, email FROM " . $this->table . " 
                  WHERE nombre LIKE :nombre AND activo = TRUE 
                  ORDER BY nombre ASC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $nombreBusqueda = "%{$nombre}%";
        $stmt->bindParam(":nombre", $nombreBusqueda);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar múltiples clientes por teléfono (para autocompletado)
     * @param string $telefono Teléfono del cliente
     * @return array Array de clientes
     */
    public function buscarPorTelefono($telefono)
    {
        $query = "SELECT id, nombre, telefono, email FROM " . $this->table . " 
                  WHERE telefono LIKE :telefono AND activo = TRUE 
                  ORDER BY nombre ASC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $telefonoBusqueda = "%{$telefono}%";
        $stmt->bindParam(":telefono", $telefonoBusqueda);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar clientes
     * @param bool $soloActivos Si es true, solo retorna clientes activos
     * @return array Array de clientes
     */
    public function listar($soloActivos = true)
    {
        $query = "SELECT id, nombre, telefono, email, fecha_registro, activo 
                  FROM " . $this->table . " ";

        if ($soloActivos) {
            $query .= "WHERE activo = TRUE ";
        }

        $query .= "ORDER BY nombre ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    /**
     * Contar clientes nuevos desde una fecha
     * @param string $desde Fecha desde la cual contar (Y-m-d)
     * @return int Cantidad de clientes nuevos
     */
    public function contarNuevos($desde)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE DATE(fecha_registro) >= :desde";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":desde", $desde);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // ============================================
    // MÉTODOS PARA AUTENTICACIÓN WEB (PORTAL)
    // ============================================

    /**
     * Propiedades adicionales para cuenta web
     */
    public $password;
    public $tiene_cuenta;
    public $email_verificado;
    public $token_verificacion;
    public $ultimo_acceso;
    public $token_recuperacion;
    public $token_expira;

    /**
     * Crear cliente con cuenta web
     */
    public function crearConCuenta()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, telefono, email, password, tiene_cuenta, email_verificado, direcciones, activo) 
                  VALUES (:nombre, :telefono, :email, :password, :tiene_cuenta, :email_verificado, :direcciones, :activo)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Convertir direcciones a JSON si es array
        if (is_array($this->direcciones)) {
            $this->direcciones = json_encode($this->direcciones, JSON_UNESCAPED_UNICODE);
        }

        // Bind de parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":tiene_cuenta", $this->tiene_cuenta);
        $stmt->bindParam(":email_verificado", $this->email_verificado);
        $stmt->bindParam(":direcciones", $this->direcciones);
        $stmt->bindParam(":activo", $this->activo);

        try {
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al crear cliente con cuenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener cliente por email (para login)
     */
    public function obtenerPorEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['direcciones']) {
            $row['direcciones'] = json_decode($row['direcciones'], true);
        }

        return $row;
    }

    /**
     * Actualizar último acceso del cliente
     */
    public function actualizarUltimoAcceso($cliente_id)
    {
        $query = "UPDATE " . $this->table . " 
                  SET ultimo_acceso = CURRENT_TIMESTAMP 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $cliente_id);

        return $stmt->execute();
    }

    /**
     * Guardar token de recuperación de contraseña
     */
    public function guardarTokenRecuperacion($cliente_id, $token, $expira)
    {
        $query = "UPDATE " . $this->table . " 
                  SET token_recuperacion = :token, 
                      token_expira = :expira 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expira", $expira);
        $stmt->bindParam(":id", $cliente_id);

        return $stmt->execute();
    }

    /**
     * Verificar token de recuperación
     */
    public function verificarTokenRecuperacion($token)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE token_recuperacion = :token 
                  AND token_expira > NOW() 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar contraseña del cliente
     */
    public function actualizarPassword($cliente_id, $nuevo_password)
    {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password, 
                      token_recuperacion = NULL, 
                      token_expira = NULL 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($nuevo_password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $cliente_id);

        return $stmt->execute();
    }

    /**
     * Agregar dirección a cliente
     */
    public function agregarDireccion($cliente_id, $direccion_data)
    {
        $clienteData = $this->obtenerPorId();
        
        if (!$clienteData) {
            return false;
        }

        $direcciones = $clienteData['direcciones'] ?? [];
        
        // Generar ID para la nueva dirección
        $max_id = 0;
        foreach ($direcciones as $dir) {
            if (isset($dir['id']) && $dir['id'] > $max_id) {
                $max_id = $dir['id'];
            }
        }
        
        $direccion_data['id'] = $max_id + 1;
        
        // Si es principal, quitar principal de las demás
        if (isset($direccion_data['principal']) && $direccion_data['principal']) {
            foreach ($direcciones as &$dir) {
                $dir['principal'] = false;
            }
        }
        
        $direcciones[] = $direccion_data;

        $query = "UPDATE " . $this->table . " 
                  SET direcciones = :direcciones 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $direcciones_json = json_encode($direcciones, JSON_UNESCAPED_UNICODE);
        $stmt->bindParam(":direcciones", $direcciones_json);
        $stmt->bindParam(":id", $cliente_id);

        return $stmt->execute();
    }

    /**
     * Obtener direcciones del cliente
     */
    public function obtenerDirecciones($cliente_id)
    {
        $query = "SELECT direcciones FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $cliente_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['direcciones']) {
            return json_decode($row['direcciones'], true);
        }

        return [];
    }

    /**
     * Actualizar perfil del cliente
     */
    public function actualizarPerfil($cliente_id, $datos)
    {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      telefono = :telefono 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $nombre = htmlspecialchars(strip_tags($datos['nombre']));
        $telefono = htmlspecialchars(strip_tags($datos['telefono']));
        
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":id", $cliente_id);

        return $stmt->execute();
    }
}

