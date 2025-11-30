<?php

/**
 * Controlador de Usuarios
 * Maneja CRUD de usuarios del sistema
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class UsuarioController
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
     * Listar todos los usuarios
     */
    public function index()
    {
        AuthController::verificarAdmin();

        $stmt = $this->usuario->listar();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    /**
     * Mostrar formulario para crear usuario
     */
    public function crear()
    {
        AuthController::verificarAdmin();

        require_once __DIR__ . '/../views/usuarios/crear.php';
    }

    /**
     * Guardar nuevo usuario
     */
    public function guardar()
    {
        AuthController::verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios');
            return;
        }

        // Obtener datos del formulario
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        $rol = $_POST['rol'] ?? ROL_MESERO;

        // Validar campos requeridos
        if (empty($nombre) || empty($email) || empty($password) || empty($telefono)) {
            $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
            $_SESSION['form_data'] = $_POST;
            redirect('usuarios_crear');
            return;
        }

        // Validar email
        if (!validate_email($email)) {
            $_SESSION['error'] = 'El email no es válido';
            $_SESSION['form_data'] = $_POST;
            redirect('usuarios_crear');
            return;
        }

        // Verificar si el email ya existe
        if ($this->usuario->emailExiste($email)) {
            $_SESSION['error'] = 'El email ya está registrado';
            $_SESSION['form_data'] = $_POST;
            redirect('usuarios_crear');
            return;
        }

        // Validar contraseñas
        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden';
            $_SESSION['form_data'] = $_POST;
            redirect('usuarios_crear');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
            $_SESSION['form_data'] = $_POST;
            redirect('usuarios_crear');
            return;
        }

        // Validar rol
        $roles_validos = [ROL_ADMIN, ROL_MESERO, ROL_REPARTIDOR];
        if (!in_array($rol, $roles_validos)) {
            $_SESSION['error'] = 'Rol no válido';
            redirect('usuarios_crear');
            return;
        }

        // Crear usuario
        $this->usuario->nombre = $nombre;
        $this->usuario->email = $email;
        $this->usuario->password = $password;
        $this->usuario->telefono = $telefono;
        $this->usuario->rol = $rol;
        $this->usuario->activo = true;

        if ($this->usuario->crear()) {
            log_actividad($this->db, $_SESSION['usuario_id'], 'CREAR_USUARIO', 'usuarios', $this->usuario->id, "Usuario: $nombre");
            $_SESSION['success'] = 'Usuario creado correctamente';
            unset($_SESSION['form_data']);
            redirect('usuarios');
        } else {
            $_SESSION['error'] = 'Error al crear el usuario';
            $_SESSION['form_data'] = $_POST;
            redirect('usuarios/crear');
        }
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function editar($id)
    {
        AuthController::verificarAdmin();

        $this->usuario->id = $id;
        $usuario = $this->usuario->obtenerPorId();

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            redirect('usuarios');
            return;
        }

        require_once __DIR__ . '/../views/usuarios/editar.php';
    }

    /**
     * Actualizar usuario
     */
    public function actualizar()
    {
        AuthController::verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios');
            return;
        }

        $id = intval($_POST['id'] ?? 0);
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        $rol = $_POST['rol'] ?? ROL_MESERO;
        $activo = isset($_POST['activo']) ? 1 : 0;
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validar campos requeridos
        if (empty($nombre) || empty($email) || empty($telefono)) {
            $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
            redirect('usuarios_editar&id=' . $id);
            return;
        }

        // Validar contraseña si se proporcionó
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
                redirect('usuarios_editar&id=' . $id);
                return;
            }

            if ($password !== $password_confirm) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                redirect('usuarios_editar&id=' . $id);
                return;
            }
        }

        // Validar email
        if (!validate_email($email)) {
            $_SESSION['error'] = 'El email no es válido';
            redirect('usuarios_editar&id=' . $id);
            return;
        }

        // Obtener usuario actual para comparar email
        $this->usuario->id = $id;
        $usuario_actual = $this->usuario->obtenerPorId();

        // Solo verificar email si cambió
        if ($usuario_actual && $usuario_actual['email'] !== $email) {
            if ($this->usuario->emailExiste($email, $id)) {
                $_SESSION['error'] = 'El email ya está registrado por otro usuario';
                redirect('usuarios_editar&id=' . $id);
                return;
            }
        }

        // Actualizar usuario
        $this->usuario->id = $id;
        $this->usuario->nombre = $nombre;
        $this->usuario->email = $email;
        $this->usuario->telefono = $telefono;
        $this->usuario->rol = $rol;
        $this->usuario->activo = $activo;

        $resultado = $this->usuario->actualizar();

        // Si se proporcionó una nueva contraseña, actualizarla
        if ($resultado && !empty($password)) {
            $resultado = $this->usuario->cambiarPassword($password);
            if (!$resultado) {
                $_SESSION['error'] = 'Usuario actualizado pero error al cambiar la contraseña';
                redirect('usuarios_editar&id=' . $id);
                return;
            }
        }

        if ($resultado) {
            $mensaje = !empty($password) ? "Usuario y contraseña actualizados" : "Usuario actualizado";
            log_actividad($this->db, $_SESSION['usuario_id'], 'ACTUALIZAR_USUARIO', 'usuarios', $id, "Usuario: $nombre");
            $_SESSION['success'] = $mensaje . ' correctamente';
            redirect('usuarios');
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario';
            redirect('usuarios_editar&id=' . $id);
        }
    }

    /**
     * Cambiar estado de usuario (activar/desactivar)
     */
    public function cambiarEstado($id)
    {
        AuthController::verificarAdmin();

        // Verificar que sea petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        // No permitir desactivar el propio usuario
        if ($id == $_SESSION['usuario_id']) {
            json_response(['success' => false, 'message' => 'No puede desactivar su propio usuario']);
            return;
        }

        $this->usuario->id = $id;
        $usuario = $this->usuario->obtenerPorId();

        if (!$usuario) {
            json_response(['success' => false, 'message' => 'Usuario no encontrado']);
            return;
        }

        $nuevo_estado = !$usuario['activo'];

        if ($this->usuario->cambiarEstado($id, $nuevo_estado)) {
            $accion = $nuevo_estado ? 'activado' : 'desactivado';
            log_actividad($this->db, $_SESSION['usuario_id'], 'CAMBIAR_ESTADO_USUARIO', 'usuarios', $id, "Usuario $accion");
            json_response(['success' => true, 'message' => "Usuario $accion correctamente", 'nuevo_estado' => $nuevo_estado]);
        } else {
            json_response(['success' => false, 'message' => 'Error al cambiar estado del usuario']);
        }
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function eliminar($id)
    {
        AuthController::verificarAdmin();

        // Verificar que sea petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        // No permitir eliminar el propio usuario
        if ($id == $_SESSION['usuario_id']) {
            json_response(['success' => false, 'message' => 'No puede eliminar su propio usuario']);
            return;
        }

        $this->usuario->id = $id;
        $usuario = $this->usuario->obtenerPorId();

        if (!$usuario) {
            json_response(['success' => false, 'message' => 'Usuario no encontrado']);
            return;
        }

        if ($this->usuario->eliminar()) {
            log_actividad($this->db, $_SESSION['usuario_id'], 'ELIMINAR_USUARIO', 'usuarios', $id, "Usuario: " . $usuario['nombre']);
            json_response(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } else {
            json_response(['success' => false, 'message' => 'Error al eliminar el usuario']);
        }
    }

    /**
     * Buscar usuarios (AJAX)
     */
    public function buscar()
    {
        AuthController::verificarAdmin();

        $termino = sanitize_input($_GET['q'] ?? '');

        if (empty($termino)) {
            json_response(['success' => false, 'data' => []]);
            return;
        }

        $resultado = $this->usuario->buscar($termino);
        $usuarios = $resultado->fetchAll(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'data' => $usuarios]);
    }

    /**
     * Ver y editar perfil del usuario actual
     */
    public function perfil()
    {
        AuthController::verificarSesion();
        
        $usuario_id = $_SESSION['usuario_id'];
        $this->usuario->id = $usuario_id;
        $usuario = $this->usuario->obtenerPorId();
        
        if (!$usuario) {
            set_flash_message('Usuario no encontrado', 'error');
            redirect('index.php?action=dashboard');
            return;
        }
        
        require_once __DIR__ . '/../views/perfil/ver.php';
    }

    /**
     * Actualizar datos del perfil
     */
    public function actualizarPerfil()
    {
        AuthController::verificarSesion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=perfil');
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        
        // Validar datos
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        
        if (empty($nombre)) {
            set_flash_message('El nombre es requerido', 'error');
            redirect('index.php?action=perfil');
            return;
        }
        
        // Verificar si el email ya existe en otro usuario
        if (!empty($email)) {
            $usuarioExistente = $this->usuario->obtenerPorEmail($email);
            if ($usuarioExistente && $usuarioExistente['id'] != $usuario_id) {
                set_flash_message('El email ya está en uso por otro usuario', 'error');
                redirect('index.php?action=perfil');
                return;
            }
        }
        
        // Actualizar datos
        $this->usuario->id = $usuario_id;
        $this->usuario->nombre = $nombre;
        $this->usuario->email = $email;
        $this->usuario->telefono = $telefono;
        
        if ($this->usuario->actualizarPerfil()) {
            $_SESSION['usuario_nombre'] = $nombre;
            set_flash_message('Perfil actualizado correctamente', 'success');
            log_actividad($this->db, $usuario_id, 'ACTUALIZAR_PERFIL', 'usuarios', $usuario_id);
        } else {
            set_flash_message('Error al actualizar el perfil', 'error');
        }
        
        redirect('index.php?action=perfil');
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword()
    {
        AuthController::verificarSesion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nueva = $_POST['password_nueva'] ?? '';
        $password_confirmar = $_POST['password_confirmar'] ?? '';
        
        // Validaciones
        if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
            json_response(['success' => false, 'message' => 'Todos los campos son requeridos']);
            return;
        }
        
        if ($password_nueva !== $password_confirmar) {
            json_response(['success' => false, 'message' => 'Las contraseñas nuevas no coinciden']);
            return;
        }
        
        if (strlen($password_nueva) < 6) {
            json_response(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }
        
        // Verificar contraseña actual
        $this->usuario->id = $usuario_id;
        $usuario = $this->usuario->obtenerPorId();
        if (!password_verify($password_actual, $usuario['password'])) {
            json_response(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
            return;
        }
        
        // Actualizar contraseña usando el método existente que recibe el password como parámetro
        $this->usuario->id = $usuario_id;
        
        if ($this->usuario->cambiarPassword($password_nueva)) {
            log_actividad($this->db, $usuario_id, 'CAMBIAR_PASSWORD', 'usuarios', $usuario_id);
            json_response(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        } else {
            json_response(['success' => false, 'message' => 'Error al actualizar la contraseña']);
        }
    }
}
