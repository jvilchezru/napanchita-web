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

// Obtener parámetros desde la URL
$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? 'home';
$subaction = $_GET['subaction'] ?? '';
$id = $_GET['id'] ?? '';

// Enrutamiento
try {
    // Enrutamiento basado en controller (nuevo estilo)
    if (!empty($controller)) {
        $controllerFile = __DIR__ . '/controllers/' . ucfirst($controller) . 'Controller.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = ucfirst($controller) . 'Controller';
            $controllerInstance = new $controllerClass();

            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action();
            } else {
                throw new Exception("Acción '$action' no encontrada en controlador '$controller'");
            }
        } else {
            throw new Exception("Controlador '$controller' no encontrado");
        }
    }
    // Enrutamiento basado en action (estilo original)
    else {
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

            // ===== CLIENTES (Solo Admin) =====
            case 'clientes':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->index();
                break;

            case 'clientes_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->crear();
                break;

            case 'clientes_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->guardar();
                break;

            case 'clientes_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->editar();
                break;

            case 'clientes_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->actualizar();
                break;

            case 'clientes_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->eliminar();
                break;

            case 'clientes_buscar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ClienteController.php';
                $controller = new ClienteController();
                $controller->buscar();
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

            // ===== CATEGORÍAS (Solo Admin) =====
            case 'categorias':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->index();
                break;

            case 'categorias_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->crear();
                break;

            case 'categorias_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->guardar();
                break;

            case 'categorias_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->editar();
                break;

            case 'categorias_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->actualizar();
                break;

            case 'categorias_cambiar_estado':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->cambiarEstado();
                break;

            case 'categorias_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/CategoriaController.php';
                $controller = new CategoriaController();
                $controller->eliminar();
                break;

            // ===== PRODUCTOS (Solo Admin) =====
            case 'productos':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->index();
                break;

            case 'productos_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->crear();
                break;

            case 'productos_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->guardar();
                break;

            case 'productos_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->editar();
                break;

            case 'productos_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->actualizar();
                break;

            case 'productos_cambiar_estado':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->cambiarEstado();
                break;

            case 'productos_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ProductoController.php';
                $controller = new ProductoController();
                $controller->eliminar();
                break;

            // ===== COMBOS (Solo Admin) =====
            case 'combos':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->index();
                break;

            case 'combos_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->crear();
                break;

            case 'combos_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->guardar();
                break;

            case 'combos_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->editar();
                break;

            case 'combos_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->actualizar();
                break;

            case 'combos_cambiar_estado':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->cambiarEstado();
                break;

            // ===== MESAS (Solo Admin) =====
            case 'mesas':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->index();
                break;

            case 'mesas_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->crear();
                break;

            case 'mesas_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->guardar();
                break;

            case 'mesas_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->editar();
                break;

            case 'mesas_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->actualizar();
                break;

            case 'mesas_cambiarEstado':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->cambiarEstado();
                break;

            case 'mesas_actualizarPosicion':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->actualizarPosicion();
                break;

            case 'mesas_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->eliminar();
                break;

            case 'mesas_listarJson':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MesaController.php';
                $controller = new MesaController();
                $controller->listarJson();
                break;

            case 'combos_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ComboController.php';
                $controller = new ComboController();
                $controller->eliminar();
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
