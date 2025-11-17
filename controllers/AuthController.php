<?php

/**
 * Controlador de Autenticación
 * Maneja login, logout y sesiones
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    private $db;
    private $usuario;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin()
    {
        // Si ya hay sesión, redirigir al dashboard
        if (is_logged_in()) {
            $this->redirigirDashboard();
        }

        require_once __DIR__ . '/../views/login.php';
    }

    /**
     * Procesar login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
            return;
        }

        // Obtener datos del formulario
        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validar campos
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Por favor complete todos los campos';
            redirect('login');
            return;
        }

        // Validar email
        if (!validate_email($email)) {
            $_SESSION['login_error'] = 'El email no es válido';
            redirect('login');
            return;
        }

        // Intentar login
        $this->usuario->email = $email;
        $this->usuario->password = $password;

        if ($this->usuario->login()) {
            // Login exitoso - crear sesión
            $_SESSION['usuario_id'] = $this->usuario->id;
            $_SESSION['usuario_nombre'] = $this->usuario->nombre;
            $_SESSION['usuario_email'] = $this->usuario->email;
            $_SESSION['usuario_rol'] = $this->usuario->rol;
            $_SESSION['login_time'] = time();

            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);

            // Registrar log
            log_actividad($this->db, $this->usuario->id, 'LOGIN', 'usuarios', $this->usuario->id, 'Inicio de sesión exitoso');

            // Redirigir al dashboard según rol
            $this->redirigirDashboard();
        } else {
            // Login fallido
            $_SESSION['login_error'] = 'Credenciales incorrectas. Intente nuevamente.';
            redirect('login');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        if (is_logged_in()) {
            $usuario_id = $_SESSION['usuario_id'];

            // Registrar log
            log_actividad($this->db, $usuario_id, 'LOGOUT', 'usuarios', $usuario_id, 'Cierre de sesión');

            // Destruir sesión
            session_unset();
            session_destroy();
        }

        redirect('login');
    }

    /**
     * Redirigir al dashboard según rol
     */
    private function redirigirDashboard()
    {
        $rol = $_SESSION['usuario_rol'] ?? null;

        switch ($rol) {
            case ROL_ADMIN:
                redirect('dashboard/admin');
                break;
            case ROL_MESERO:
                redirect('dashboard/mesero');
                break;
            case ROL_REPARTIDOR:
                redirect('dashboard/repartidor');
                break;
            default:
                redirect('login');
                break;
        }
    }

    /**
     * Verificar si hay sesión activa (método estático)
     */
    public static function verificarSesion()
    {
        if (!is_logged_in()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '';
            redirect('login');
            exit();
        }

        // Verificar timeout de sesión
        if (isset($_SESSION['login_time'])) {
            $tiempo_transcurrido = time() - $_SESSION['login_time'];
            if ($tiempo_transcurrido > SESSION_TIMEOUT) {
                session_unset();
                session_destroy();
                $_SESSION['login_error'] = 'Su sesión ha expirado. Por favor inicie sesión nuevamente.';
                redirect('login');
                exit();
            }
            // Actualizar tiempo de actividad
            $_SESSION['login_time'] = time();
        }
    }

    /**
     * Verificar si es administrador (método estático)
     */
    public static function verificarAdmin()
    {
        self::verificarSesion();

        if (!is_admin()) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            redirect('dashboard/' . $_SESSION['usuario_rol']);
            exit();
        }
    }

    /**
     * Verificar rol específico (método estático)
     * @param string|array $roles Rol o array de roles permitidos
     */
    public static function verificarRol($roles)
    {
        self::verificarSesion();

        $roles = is_array($roles) ? $roles : [$roles];
        $usuario_rol = $_SESSION['usuario_rol'] ?? null;

        if (!in_array($usuario_rol, $roles)) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            redirect('dashboard/' . $usuario_rol);
            exit();
        }
    }

    /**
     * Cambiar contraseña (para usuario logueado)
     */
    public function cambiarPassword()
    {
        self::verificarSesion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $password_actual = $_POST['password_actual'] ?? '';
        $password_nueva = $_POST['password_nueva'] ?? '';
        $password_confirmar = $_POST['password_confirmar'] ?? '';

        // Validaciones
        if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
            json_response(['success' => false, 'message' => 'Complete todos los campos']);
            return;
        }

        if ($password_nueva !== $password_confirmar) {
            json_response(['success' => false, 'message' => 'Las contraseñas no coinciden']);
            return;
        }

        if (strlen($password_nueva) < 6) {
            json_response(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }

        // Verificar contraseña actual
        $this->usuario->id = $_SESSION['usuario_id'];
        $usuario_actual = $this->usuario->obtenerPorId();

        if (!password_verify($password_actual, $usuario_actual['password'])) {
            json_response(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
            return;
        }

        // Cambiar contraseña
        if ($this->usuario->cambiarPassword($password_nueva)) {
            log_actividad($this->db, $_SESSION['usuario_id'], 'CAMBIAR_PASSWORD', 'usuarios', $_SESSION['usuario_id']);
            json_response(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        } else {
            json_response(['success' => false, 'message' => 'Error al actualizar la contraseña']);
        }
    }
}
