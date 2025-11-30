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
                        // Cargar datos para el dashboard del mesero
                        require_once __DIR__ . '/config/database.php';
                        require_once __DIR__ . '/models/Mesa.php';
                        require_once __DIR__ . '/models/Pedido.php';
                        require_once __DIR__ . '/models/Reserva.php';
                        
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        $mesaModel = new Mesa($db);
                        $pedidoModel = new Pedido($db);
                        $reservaModel = new Reserva($db);
                        
                        // Obtener todas las mesas activas
                        $mesas = $mesaModel->listar(true);
                        
                        // Obtener pedidos activos del mesero
                        $pedidos_activos = $pedidoModel->listarPorUsuario($_SESSION['usuario_id']);
                        
                        // Obtener reservas del día
                        $reservas_hoy = $reservaModel->listarPorFecha(date('Y-m-d'));
                        
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

            // ===== MI PERFIL =====
            case 'perfil':
                AuthController::verificarSesion();
                require_once __DIR__ . '/controllers/UsuarioController.php';
                $controller = new UsuarioController();
                $controller->perfil();
                break;

            case 'perfil_actualizar':
                AuthController::verificarSesion();
                require_once __DIR__ . '/controllers/UsuarioController.php';
                $controller = new UsuarioController();
                $controller->actualizarPerfil();
                break;

            case 'perfil_cambiar_password':
                AuthController::verificarSesion();
                require_once __DIR__ . '/controllers/UsuarioController.php';
                $controller = new UsuarioController();
                $controller->cambiarPassword();
                break;

            // ===== CONFIGURACIÓN (Solo Admin) =====
            case 'configuracion':
                AuthController::verificarRol([ROL_ADMIN]);
                require_once __DIR__ . '/controllers/ConfiguracionController.php';
                $controller = new ConfiguracionController();
                $controller->index();
                break;

            case 'configuracion_guardar':
                AuthController::verificarRol([ROL_ADMIN]);
                require_once __DIR__ . '/controllers/ConfiguracionController.php';
                $controller = new ConfiguracionController();
                $controller->guardar();
                break;

            case 'configuracion_subir_logo':
                AuthController::verificarRol([ROL_ADMIN]);
                require_once __DIR__ . '/controllers/ConfiguracionController.php';
                $controller = new ConfiguracionController();
                $controller->subirLogo();
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

            // ===== PLATOS (Solo Admin) =====
            case 'platos':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
                $controller->index();
                break;

            case 'platos_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
                $controller->crear();
                break;

            case 'platos_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
                $controller->guardar();
                break;

            case 'platos_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
                $controller->editar();
                break;

            case 'platos_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
                $controller->actualizar();
                break;

            case 'platos_cambiar_estado':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
                $controller->cambiarEstado();
                break;

            case 'platos_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/PlatoController.php';
                $controller = new PlatoController();
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

            // ===== MÉTODOS DE PAGO (Admin) =====
            case 'metodos_pago':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->index();
                break;

            case 'metodos_pago_crear':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->crear();
                break;

            case 'metodos_pago_guardar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->guardar();
                break;

            case 'metodos_pago_editar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->editar();
                break;

            case 'metodos_pago_actualizar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->actualizar();
                break;

            case 'metodos_pago_eliminar':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->eliminar();
                break;

            case 'metodos_pago_cambiarEstado':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/MetodoPagoController.php';
                $controller = new MetodoPagoController();
                $controller->cambiarEstado();
                break;

            // ===== PEDIDOS (Admin y Mesero) =====
            case 'pedidos':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->index();
                break;

            case 'pedidos_crear':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->crear();
                break;

            case 'pedidos_guardar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->guardar();
                break;

            case 'pedidos_ver':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $id = intval($_GET['id'] ?? 0);
                if ($id > 0) {
                    // Cargar pedido directamente aquí
                    require_once __DIR__ . '/models/Pedido.php';
                    $database = new Database();
                    $db = $database->getConnection();
                    $pedidoModel = new Pedido($db);
                    
                    $pedidoModel->id = $id;
                    $pedido = $pedidoModel->obtenerPorId();
                    
                    if (!$pedido) {
                        $_SESSION['error'] = 'Pedido no encontrado';
                        redirect('pedidos');
                        exit;
                    }
                    
                    // Cargar venta si está finalizado
                    $venta = null;
                    if ($pedido['estado'] === 'finalizado') {
                        $stmt = $db->prepare("
                            SELECT v.*, mp.nombre as metodo_pago, u.nombre as cajero
                            FROM ventas v
                            LEFT JOIN metodos_pago mp ON v.metodo_pago_id = mp.id
                            LEFT JOIN usuarios u ON v.usuario_id = u.id
                            WHERE v.pedido_id = :pedido_id
                        ");
                        $stmt->bindParam(':pedido_id', $id);
                        $stmt->execute();
                        $venta = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    
                    require_once __DIR__ . '/views/pedidos/ver.php';
                } else {
                    redirect('pedidos');
                }
                break;

            case 'pedidos_cambiarEstado':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->cambiarEstado();
                break;

            case 'pedidos_cancelar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $controller->cancelar($id);
                } else {
                    json_response(['success' => false, 'message' => 'ID no proporcionado']);
                }
                break;

            case 'pedidos_finalizar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->finalizar();
                break;

            case 'pedidos_obtenerMetodosPago':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->obtenerMetodosPago();
                break;

            case 'pedidos_cocina':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->cocina();
                break;

            case 'pedidos_obtenerPendientes':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->obtenerPendientes();
                break;

            case 'pedidos_buscarCliente':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->buscarCliente();
                break;

            case 'pedidos_buscarClientesAutocomplete':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->buscarClientesAutocomplete();
                break;

            case 'pedidos_crearClienteRapido':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/PedidoController.php';
                $controller = new PedidoController();
                $controller->crearClienteRapido();
                break;

            // ===== RESERVAS (Admin y Mesero) =====
            case 'reservas':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->index();
                break;

            case 'reservas_crear':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->crear();
                break;

            case 'reservas_guardar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->guardar();
                break;

            case 'reservas_editar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->editar();
                break;

            case 'reservas_actualizar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->actualizar();
                break;

            case 'reservas_cambiarEstado':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->cambiarEstado();
                break;

            case 'reservas_verificarDisponibilidad':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->verificarDisponibilidad();
                break;

            case 'reservas_calendario':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->calendario();
                break;

            case 'reservas_buscarPorCodigo':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->buscarPorCodigo();
                break;

            case 'reservas_obtenerReservasDelDia':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->obtenerReservasDelDia();
                break;

            case 'reservas_marcarNoShow':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/ReservaController.php';
                $controller = new ReservaController();
                $controller->marcarNoShow();
                break;

            // ===== VENTAS (Admin) =====
            case 'ventas':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/VentaController.php';
                $controller = new VentaController();
                $controller->index();
                break;

            case 'ventas_registrar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/VentaController.php';
                $controller = new VentaController();
                $controller->registrar();
                break;

            case 'ventas_guardar':
                AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);
                require_once __DIR__ . '/controllers/VentaController.php';
                $controller = new VentaController();
                $controller->guardar();
                break;

            case 'cierre_caja':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/VentaController.php';
                $controller = new VentaController();
                $controller->cierreCaja();
                break;

            // ===== REPORTES (Admin) =====
            case 'reportes':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ReporteController.php';
                $controller = new ReporteController();
                $controller->index();
                break;

            case 'reportes_ventas':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ReporteController.php';
                $controller = new ReporteController();
                $controller->ventas();
                break;

            case 'reportes_platos':
                AuthController::verificarAdmin();
                require_once __DIR__ . '/controllers/ReporteController.php';
                $controller = new ReporteController();
                $controller->platos();
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
