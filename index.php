<?php
/**
 * NAPANCHITA - Sistema de Gestión de Pedidos y Delivery
 * Archivo principal - Enrutador MVC
 * 
 * Este archivo maneja todas las peticiones y las dirige
 * a los controladores correspondientes
 */

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener la acción solicitada
$action = $_GET['action'] ?? 'home';

// Enrutamiento de la aplicación
switch ($action) {
    // ==================== PÁGINAS PRINCIPALES ====================
    case 'home':
        require_once __DIR__ . '/views/home.php';
        break;
    
    // ==================== AUTENTICACIÓN ====================
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $auth = new AuthController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login();
        } else {
            $auth->mostrarLogin();
        }
        break;
    
    case 'registro':
        require_once __DIR__ . '/controllers/AuthController.php';
        $auth = new AuthController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->registro();
        } else {
            $auth->mostrarRegistro();
        }
        break;
    
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
    
    // ==================== DASHBOARD ====================
    case 'dashboard':
        require_once __DIR__ . '/controllers/AuthController.php';
        AuthController::verificarSesion();
        require_once __DIR__ . '/views/dashboard.php';
        break;
    
    // ==================== API DE PRODUCTOS ====================
    case 'api_productos':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $producto = new ProductoController();
        $producto->listar();
        break;
    
    case 'api_producto':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $producto = new ProductoController();
        $id = $_GET['id'] ?? 0;
        $producto->obtener($id);
        break;
    
    case 'api_buscar_producto':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $producto = new ProductoController();
        $producto->buscar();
        break;
    
    case 'api_crear_producto':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $producto = new ProductoController();
        $producto->crear();
        break;
    
    case 'api_actualizar_producto':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $producto = new ProductoController();
        $producto->actualizar();
        break;
    
    // ==================== API DE PEDIDOS ====================
    case 'api_crear_pedido':
        require_once __DIR__ . '/controllers/AuthController.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $pedido = new PedidoController();
        $pedido->crear();
        break;
    
    case 'api_mis_pedidos':
        require_once __DIR__ . '/controllers/AuthController.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $pedido = new PedidoController();
        $pedido->misPedidos();
        break;
    
    case 'api_todos_pedidos':
        require_once __DIR__ . '/controllers/AuthController.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $pedido = new PedidoController();
        $pedido->listarTodos();
        break;
    
    case 'api_detalle_pedido':
        require_once __DIR__ . '/controllers/AuthController.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $pedido = new PedidoController();
        $id = $_GET['id'] ?? 0;
        $pedido->detalles($id);
        break;
    
    case 'api_actualizar_estado':
        require_once __DIR__ . '/controllers/AuthController.php';
        require_once __DIR__ . '/controllers/PedidoController.php';
        $pedido = new PedidoController();
        $pedido->actualizarEstado();
        break;
    
    // ==================== PÁGINA NO ENCONTRADA ====================
    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p><a href='index.php'>Volver al inicio</a></p>";
        break;
}
?>
