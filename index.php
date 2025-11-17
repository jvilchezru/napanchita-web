<?php

/**
 * Front Controller - Punto de entrada único
 * Sistema Napanchita
 * Version 1.0
 */

// Iniciar sesión
session_start();

// Cargar configuración
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';

// Cargar AuthController (necesario para verificaciones globales)
require_once __DIR__ . '/controllers/AuthController.php';

// Obtener la acción desde la URL
$action = $_GET['action'] ?? 'home';
$subaction = $_GET['subaction'] ?? '';
$id = $_GET['id'] ?? '';

// Enrutamiento
try {
    switch ($action) {
        // ===== AUTENTICACIÓN =====
        case 'login':
            $controller = new AuthController();
            $controller->mostrarLogin();
            break;

        case 'procesarLogin':
            $controller = new AuthController();
            $controller->login();
            break;

        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;

        case 'cambiar-password':
            $controller = new AuthController();
            $controller->cambiarPassword();
            break;

        // ===== DASHBOARD =====
        case 'dashboard':
            AuthController::verificarSesion();
            $rol = $_SESSION['usuario_rol'] ?? '';

            switch ($rol) {
                case ROL_ADMIN:
                    require_once __DIR__ . '/views/dashboard/admin.php';
                    break;
                case ROL_MESERO:
                    require_once __DIR__ . '/views/dashboard/mesero.php';
                    break;
                case ROL_REPARTIDOR:
                    require_once __DIR__ . '/views/dashboard/repartidor.php';
                    break;
                default:
                    redirect('login');
                    break;
            }
            break;

        // ===== USUARIOS (Solo Admin) =====
        case 'usuarios':
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $controller->index();
            break;

        case 'usuarios_crear':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $controller->crear();
            break;

        case 'usuarios_guardar':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $controller->guardar();
            break;

        case 'usuarios_editar':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->editar($id);
            } else {
                redirect('usuarios');
            }
            break;

        case 'usuarios_actualizar':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $controller->actualizar();
            break;

        case 'usuarios_cambiar_estado':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->cambiarEstado($id);
            } else {
                redirect('usuarios');
            }
            break;

        case 'usuarios_eliminar':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->eliminar($id);
            } else {
                redirect('usuarios');
            }
            break;

        case 'usuarios_buscar':
            AuthController::verificarAdmin();
            require_once __DIR__ . '/controllers/UsuarioController.php';
            $controller = new UsuarioController();
            $controller->buscar();
            break;

        // ===== HOME / PÁGINA PÚBLICA =====
        case 'home':
        default:
            // Si ya está logueado, redirigir al dashboard
            if (is_logged_in()) {
                redirect('dashboard');
            } else {
                redirect('login');
            }
            break;
    }
} catch (Exception $e) {
    // En producción, mostrar página de error genérica
    if (ENVIRONMENT === 'development') {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        error_log("Error en index.php: " . $e->getMessage());
        require_once __DIR__ . '/views/error.php';
    }
}
