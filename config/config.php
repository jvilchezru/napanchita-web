<?php
/**
 * Archivo de configuración global
 * Constantes y configuraciones del sistema
 */

// Configuración de zona horaria
date_default_timezone_set('America/La_Paz');

// Configuración de errores (cambiar en producción)
define('ENVIRONMENT', 'development'); // development o production

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configuración de la aplicación
define('APP_NAME', 'Napanchita');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost:8000');

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'napanchita_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de sesión
define('SESSION_LIFETIME', 3600); // 1 hora en segundos

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Configuración de archivos
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Estados de pedidos
define('ESTADO_PENDIENTE', 'pendiente');
define('ESTADO_PREPARANDO', 'preparando');
define('ESTADO_ENVIADO', 'enviado');
define('ESTADO_ENTREGADO', 'entregado');
define('ESTADO_CANCELADO', 'cancelado');

// Roles de usuario
define('ROL_ADMIN', 'admin');
define('ROL_CLIENTE', 'cliente');

// Mensajes del sistema
define('MSG_ERROR_DB', 'Error de conexión a la base de datos');
define('MSG_ERROR_LOGIN', 'Credenciales incorrectas');
define('MSG_ERROR_PERMISO', 'No tienes permisos para realizar esta acción');
define('MSG_SUCCESS_REGISTRO', 'Registro exitoso');
define('MSG_SUCCESS_PEDIDO', 'Pedido realizado con éxito');

// Configuración de email (para futuras implementaciones)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'tu-email@gmail.com');
define('MAIL_PASSWORD', 'tu-password');
define('MAIL_FROM', 'noreply@napanchita.com');
define('MAIL_FROM_NAME', 'Napanchita');

/**
 * Función helper para redireccionar
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Función helper para escapar output HTML
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Función helper para formatear moneda
 */
function formatCurrency($amount) {
    return 'Bs. ' . number_format($amount, 2, '.', ',');
}

/**
 * Función helper para formatear fecha
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}
?>
