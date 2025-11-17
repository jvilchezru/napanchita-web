<?php

/**
 * Configuración General del Sistema
 * Sistema Napanchita
 */

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Configuración de entorno
define('ENVIRONMENT', 'development'); // 'development' o 'production'

// Configuración de errores
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// Configuración de la aplicación
define('APP_NAME', 'Sistema Napanchita');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/napanchita-web/');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de archivos
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes

// Roles del sistema
define('ROL_ADMIN', 'admin');
define('ROL_MESERO', 'mesero');
define('ROL_REPARTIDOR', 'repartidor');

// Estados de pedidos
define('PEDIDO_PENDIENTE', 'pendiente');
define('PEDIDO_EN_PREPARACION', 'en_preparacion');
define('PEDIDO_LISTO', 'listo');
define('PEDIDO_ENTREGADO', 'entregado');
define('PEDIDO_CANCELADO', 'cancelado');

// Tipos de pedido
define('PEDIDO_MESA', 'mesa');
define('PEDIDO_DELIVERY', 'delivery');
define('PEDIDO_PARA_LLEVAR', 'para_llevar');

// Estados de mesas
define('MESA_DISPONIBLE', 'disponible');
define('MESA_OCUPADA', 'ocupada');
define('MESA_RESERVADA', 'reservada');
define('MESA_INACTIVA', 'inactiva');

// Estados de reservas
define('RESERVA_PENDIENTE', 'pendiente');
define('RESERVA_CONFIRMADA', 'confirmada');
define('RESERVA_COMPLETADA', 'completada');
define('RESERVA_CANCELADA', 'cancelada');
define('RESERVA_NO_SHOW', 'no_show');

// Estados de delivery
define('DELIVERY_PENDIENTE', 'pendiente');
define('DELIVERY_ASIGNADO', 'asignado');
define('DELIVERY_EN_CAMINO', 'en_camino');
define('DELIVERY_ENTREGADO', 'entregado');
define('DELIVERY_FALLIDO', 'fallido');
