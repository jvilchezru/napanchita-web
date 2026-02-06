<?php

/**
 * Controlador de Autenticación para Clientes Web
 * Maneja registro, login y sesiones de clientes del portal
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteAuthController
{
    private $db;
    private $cliente;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->cliente = new Cliente();
    }

    /**
     * Mostrar formulario de login para clientes
     */
    public function mostrarLogin()
    {
        // Si ya hay sesión de cliente, redirigir al portal
        if ($this->isClienteLoggedIn()) {
            redirect('portal');
        }

        // Redirigir al login unificado
        redirect('login');
    }

    /**
     * Mostrar formulario de registro
     */
    public function mostrarRegistro()
    {
        if ($this->isClienteLoggedIn()) {
            redirect('portal');
        }

        require_once __DIR__ . '/../views/portal/registro.php';
    }

    /**
     * Procesar registro de nuevo cliente
     */
    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('portal/registro');
            return;
        }

        // Obtener datos del formulario
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validaciones
        $errores = [];

        if (empty($nombre)) {
            $errores[] = 'El nombre es requerido';
        }

        if (empty($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
            $errores[] = 'El teléfono debe tener 9 dígitos';
        }

        if (empty($email) || !validate_email($email)) {
            $errores[] = 'El email no es válido';
        }

        if (strlen($password) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        if ($password !== $password_confirm) {
            $errores[] = 'Las contraseñas no coinciden';
        }

        // Verificar si el email ya existe
        if ($this->cliente->obtenerPorEmail($email)) {
            $errores[] = 'Este email ya está registrado';
        }

        // Verificar si el teléfono ya existe
        if ($this->cliente->obtenerPorTelefono($telefono)) {
            $errores[] = 'Este teléfono ya está registrado';
        }

        if (!empty($errores)) {
            $_SESSION['registro_errores'] = $errores;
            $_SESSION['registro_datos'] = $_POST;
            redirect('portal/registro');
            return;
        }

        // Crear cliente con cuenta web
        $this->cliente->nombre = $nombre;
        $this->cliente->telefono = $telefono;
        $this->cliente->email = $email;
        $this->cliente->password = password_hash($password, PASSWORD_BCRYPT);
        $this->cliente->tiene_cuenta = 1;
        $this->cliente->email_verificado = 0; // Podrías implementar verificación por email
        $this->cliente->activo = 1;
        
        // Dirección inicial vacía
        $this->cliente->direcciones = json_encode([]);

        if ($this->cliente->crear()) {
            // Iniciar sesión automáticamente
            $this->iniciarSesionCliente($this->cliente->id, $nombre, $email, $telefono);

            $_SESSION['mensaje_exito'] = '¡Bienvenido! Tu cuenta ha sido creada exitosamente';
            
            // Registrar log
            log_actividad($this->db, null, 'REGISTRO_CLIENTE', 'clientes', $this->cliente->id, 'Nuevo cliente registrado: ' . $email);

            redirect('portal');
        } else {
            $_SESSION['registro_errores'] = ['Error al crear la cuenta. Intente nuevamente.'];
            redirect('portal/registro');
        }
    }

    /**
     * Procesar login de cliente
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('portal/login');
            return;
        }

        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar campos
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Complete todos los campos';
            redirect('portal/login');
            return;
        }

        // Obtener cliente por email
        $clienteData = $this->cliente->obtenerPorEmail($email);

        if (!$clienteData) {
            $_SESSION['login_error'] = 'Credenciales incorrectas';
            redirect('portal/login');
            return;
        }

        // Verificar que tenga cuenta web activa
        if (!$clienteData['tiene_cuenta']) {
            $_SESSION['login_error'] = 'Esta cuenta no tiene acceso al portal web';
            redirect('portal/login');
            return;
        }

        // Verificar que esté activo
        if (!$clienteData['activo']) {
            $_SESSION['login_error'] = 'Esta cuenta está inactiva';
            redirect('portal/login');
            return;
        }

        // Verificar password
        if (!password_verify($password, $clienteData['password'])) {
            $_SESSION['login_error'] = 'Credenciales incorrectas';
            redirect('portal/login');
            return;
        }

        // Actualizar último acceso
        $this->cliente->actualizarUltimoAcceso($clienteData['id']);

        // Iniciar sesión
        $this->iniciarSesionCliente(
            $clienteData['id'],
            $clienteData['nombre'],
            $clienteData['email'],
            $clienteData['telefono']
        );

        // Registrar log
        log_actividad($this->db, null, 'LOGIN_CLIENTE', 'clientes', $clienteData['id'], 'Cliente inició sesión: ' . $email);

        redirect('portal');
    }

    /**
     * Cerrar sesión de cliente
     */
    public function logout()
    {
        if ($this->isClienteLoggedIn()) {
            $cliente_id = $_SESSION['cliente_id'];
            
            // Registrar log
            log_actividad($this->db, null, 'LOGOUT_CLIENTE', 'clientes', $cliente_id, 'Cliente cerró sesión');

            // Limpiar sesión de cliente
            unset($_SESSION['cliente_id']);
            unset($_SESSION['cliente_nombre']);
            unset($_SESSION['cliente_email']);
            unset($_SESSION['cliente_telefono']);
            unset($_SESSION['cliente_login_time']);
        }

        $_SESSION['mensaje_info'] = 'Has cerrado sesión correctamente';
        redirect('portal/login');
    }

    /**
     * Iniciar sesión de cliente
     */
    private function iniciarSesionCliente($id, $nombre, $email, $telefono)
    {
        $_SESSION['cliente_id'] = $id;
        $_SESSION['cliente_nombre'] = $nombre;
        $_SESSION['cliente_email'] = $email;
        $_SESSION['cliente_telefono'] = $telefono;
        $_SESSION['cliente_login_time'] = time();

        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
    }

    /**
     * Verificar si hay un cliente logueado
     */
    public static function isClienteLoggedIn()
    {
        return isset($_SESSION['cliente_id']) && !empty($_SESSION['cliente_id']);
    }

    /**
     * Obtener datos del cliente en sesión
     */
    public static function getClienteSesion()
    {
        if (!self::isClienteLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['cliente_id'] ?? null,
            'nombre' => $_SESSION['cliente_nombre'] ?? null,
            'email' => $_SESSION['cliente_email'] ?? null,
            'telefono' => $_SESSION['cliente_telefono'] ?? null
        ];
    }

    /**
     * Verificar que haya sesión de cliente, sino redirigir a login
     */
    public static function verificarSesionCliente()
    {
        if (!self::isClienteLoggedIn()) {
            $_SESSION['mensaje_info'] = 'Debes iniciar sesión para continuar';
            redirect('portal/login');
            exit;
        }
    }

    /**
     * Recuperar contraseña - Enviar email
     */
    public function recuperarPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../views/portal/recuperar.php';
            return;
        }

        $email = sanitize_input($_POST['email'] ?? '');

        if (empty($email) || !validate_email($email)) {
            $_SESSION['recuperar_error'] = 'Ingrese un email válido';
            redirect('portal/recuperar');
            return;
        }

        $clienteData = $this->cliente->obtenerPorEmail($email);

        if (!$clienteData || !$clienteData['tiene_cuenta']) {
            // Por seguridad, siempre mostrar el mismo mensaje
            $_SESSION['recuperar_exito'] = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña';
            redirect('portal/recuperar');
            return;
        }

        // Generar token
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar token
        if ($this->cliente->guardarTokenRecuperacion($clienteData['id'], $token, $expira)) {
            // TODO: Enviar email con el link de recuperación
            // Por ahora solo mostramos el token (en producción debe enviarse por email)
            
            $_SESSION['recuperar_exito'] = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña';
            
            // Log
            log_actividad($this->db, null, 'RECUPERAR_PASSWORD_CLIENTE', 'clientes', $clienteData['id'], 'Solicitud de recuperación de contraseña');
        }

        redirect('portal/login');
    }
}
