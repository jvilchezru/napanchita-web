<?php

/**
 * Funciones auxiliares del sistema
 * Sistema Napanchita
 */

/**
 * Sanitizar entrada de texto
 * @param string $data Dato a sanitizar
 * @return string Dato sanitizado
 */
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirigir a una URL
 * @param string $url URL de destino
 */
function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Verificar si hay sesión activa
 * @return boolean
 */
function is_logged_in()
{
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Obtener usuario actual de la sesión
 * @return array|null Array con datos del usuario o null
 */
function get_session_user()
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? null,
        'email' => $_SESSION['usuario_email'] ?? null,
        'rol' => $_SESSION['usuario_rol'] ?? null
    ];
}

/**
 * Verificar si el usuario tiene un rol específico
 * @param string|array $rol Rol o array de roles a verificar
 * @return boolean
 */
function has_role($rol)
{
    if (!is_logged_in()) {
        return false;
    }

    if (is_array($rol)) {
        return in_array($_SESSION['usuario_rol'], $rol);
    }

    return $_SESSION['usuario_rol'] === $rol;
}

/**
 * Verificar si el usuario es administrador
 * @return boolean
 */
function is_admin()
{
    return has_role(ROL_ADMIN);
}

/**
 * Verificar si el usuario es mesero
 * @return boolean
 */
function is_mesero()
{
    return has_role(ROL_MESERO);
}

/**
 * Verificar si el usuario es repartidor
 * @return boolean
 */
function is_repartidor()
{
    return has_role(ROL_REPARTIDOR);
}

/**
 * Formatear precio
 * @param float $precio Precio a formatear
 * @return string Precio formateado
 */
function format_precio($precio)
{
    return 'S/ ' . number_format($precio, 2, '.', ',');
}

/**
 * Formatear fecha
 * @param string $fecha Fecha a formatear
 * @param string $formato Formato de salida
 * @return string Fecha formateada
 */
function format_fecha($fecha, $formato = 'd/m/Y')
{
    return date($formato, strtotime($fecha));
}

/**
 * Formatear fecha y hora
 * @param string $fecha Fecha y hora a formatear
 * @return string Fecha y hora formateadas
 */
function format_fecha_hora($fecha)
{
    return date('d/m/Y H:i', strtotime($fecha));
}

/**
 * Generar token CSRF
 * @return string Token generado
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 * @param string $token Token a verificar
 * @return boolean
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Mostrar alerta
 * @param string $message Mensaje a mostrar
 * @param string $type Tipo de alerta (success, danger, warning, info)
 * @return string HTML de la alerta
 */
function show_alert($message, $type = 'info')
{
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">' .
        $message .
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' .
        '</div>';
}

/**
 * Registrar log de actividad
 * @param PDO $db Conexión a base de datos
 * @param int $usuario_id ID del usuario
 * @param string $accion Acción realizada
 * @param string $tabla Tabla afectada
 * @param int $registro_id ID del registro afectado
 * @param string $detalles Detalles adicionales
 */
function log_actividad($db, $usuario_id, $accion, $tabla = null, $registro_id = null, $detalles = null)
{
    try {
        $query = "INSERT INTO logs (usuario_id, accion, tabla, registro_id, detalles, ip) 
                  VALUES (:usuario_id, :accion, :tabla, :registro_id, :detalles, :ip)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            'usuario_id' => $usuario_id,
            'accion' => $accion,
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'detalles' => $detalles,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (PDOException $e) {
        // Silenciar error de log para no interrumpir el flujo
        error_log("Error al registrar log: " . $e->getMessage());
    }
}

/**
 * Generar código aleatorio
 * @param int $length Longitud del código
 * @return string Código generado
 */
function generate_code($length = 6)
{
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, $length));
}

/**
 * Validar email
 * @param string $email Email a validar
 * @return boolean
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar teléfono peruano
 * @param string $telefono Teléfono a validar
 * @return boolean
 */
function validate_telefono($telefono)
{
    // Formato: 9 dígitos que inicia con 9
    return preg_match('/^9\d{8}$/', $telefono);
}

/**
 * Convertir array a JSON response
 * @param array $data Datos a convertir
 * @param int $status_code Código HTTP
 */
function json_response($data, $status_code = 200)
{
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Subir archivo
 * @param array $file Archivo de $_FILES
 * @param string $destination Directorio de destino
 * @param array $allowed_types Tipos MIME permitidos
 * @return string|false Nombre del archivo guardado o false en error
 */
function upload_file($file, $destination, $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'])
{
    // Verificar errores
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Verificar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    // Verificar tipo
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }

    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $destination . $filename;

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return false;
}

/**
 * Verificar sesión activa y redirigir si no hay sesión
 */
function verificar_sesion()
{
    if (!is_logged_in()) {
        set_flash_message('Debes iniciar sesión para acceder', 'error');
        redirect('login');
        exit();
    }
}

/**
 * Verificar rol de usuario
 * @param string $rol Rol requerido
 * @return boolean
 */
function verificar_rol($rol)
{
    if (!is_logged_in()) {
        return false;
    }
    return $_SESSION['usuario_rol'] === $rol;
}

/**
 * Establecer mensaje flash
 * @param string $message Mensaje
 * @param string $type Tipo (success, error, warning, info)
 */
function set_flash_message($message, $type = 'info')
{
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Verificar si hay mensaje flash
 * @return boolean
 */
function has_flash_message()
{
    return isset($_SESSION['flash_message']);
}

/**
 * Obtener mensaje flash y eliminarlo
 * @return array|null
 */
function get_flash_message()
{
    if (has_flash_message()) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
