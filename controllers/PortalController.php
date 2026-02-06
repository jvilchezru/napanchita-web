<?php

/**
 * Controlador del Portal de Clientes
 * Maneja el catálogo, carrito y pedidos del portal web
 * Sistema Napanchita - Módulo Delivery
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Combo.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Carrito.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/ZonaDelivery.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Configuracion.php';
require_once __DIR__ . '/../models/Resena.php';
require_once __DIR__ . '/../controllers/ClienteAuthController.php';

class PortalController
{
    private $db;
    private $plato;
    private $combo;
    private $categoria;
    private $carrito;
    private $cliente;
    private $zona;
    private $pedido;
    private $config;
    private $resena;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->plato = new Plato($this->db);
        $this->combo = new Combo($this->db);
        $this->categoria = new Categoria($this->db);
        $this->carrito = new Carrito();
        $this->cliente = new Cliente();
        $this->zona = new ZonaDelivery();
        $this->pedido = new Pedido($this->db);
        $this->config = new Configuracion($this->db);
        $this->resena = new Resena();
    }

    /**
     * Página principal del portal - Catálogo de productos
     */
    public function index()
    {
        // Obtener datos del cliente si está logueado
        $clienteData = null;
        $cliente_id = null;
        $isLoggedIn = ClienteAuthController::isClienteLoggedIn();
        
        if ($isLoggedIn) {
            $clienteData = ClienteAuthController::getClienteSesion();
            $cliente_id = $clienteData['id'];
        }

        // Obtener categorías activas
        $categorias = $this->categoria->listar(true);

        // Obtener platos disponibles
        $platos = $this->plato->listar(true);

        // Obtener combos activos
        $combos = $this->combo->listar(true);

        // Obtener cantidad de items en el carrito
        $session_id = session_id();
        $items_carrito = $this->carrito->contarItems($cliente_id, $session_id);

        // Obtener configuración
        $config_delivery = [
            'habilitado' => $this->config->obtener('delivery_habilitado') ?? '1',
            'costo_base' => $this->config->obtener('costo_delivery') ?? '5.00',
            'monto_minimo' => $this->config->obtener('monto_minimo_delivery') ?? '20.00',
            'tiempo_preparacion' => $this->config->obtener('tiempo_preparacion') ?? '30',
            'horario_inicio' => $this->config->obtener('horario_delivery_inicio') ?? '10:00',
            'horario_fin' => $this->config->obtener('horario_delivery_fin') ?? '22:00'
        ];

        // Obtener reseñas activas (máximo 6 para la home)
        $resenas = $this->resena->listarActivas(6);
        $estadisticasResenas = $this->resena->obtenerEstadisticas();

        require_once __DIR__ . '/../views/portal/index.php';
    }

    /**
     * Ver carrito de compras
     */
    public function verCarrito()
    {
        // Permitir ver carrito sin login
        $clienteData = null;
        $cliente_id = null;
        $isLoggedIn = ClienteAuthController::isClienteLoggedIn();
        
        if ($isLoggedIn) {
            $clienteData = ClienteAuthController::getClienteSesion();
            $cliente_id = $clienteData['id'];
        }

        $session_id = session_id();

        // Obtener items del carrito
        $items = $this->carrito->obtenerItems($cliente_id, $session_id);
        $subtotal = $this->carrito->calcularTotal($cliente_id, $session_id);

        // Obtener zonas de delivery
        $zonas = $this->zona->listar(true);

        require_once __DIR__ . '/../views/portal/carrito.php';
    }

    /**
     * Agregar producto al carrito (AJAX)
     */
    public function agregarAlCarrito()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Permitir agregar al carrito sin login (usando session_id)
        $cliente_id = null;
        if (ClienteAuthController::isClienteLoggedIn()) {
            $clienteData = ClienteAuthController::getClienteSesion();
            $cliente_id = $clienteData['id'];
        }
        
        $session_id = session_id();

        $tipo_producto = $_POST['tipo_producto'] ?? '';
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 1);
        $notas = $_POST['notas'] ?? '';

        // Validar datos
        if (!in_array($tipo_producto, ['plato', 'combo']) || $producto_id <= 0 || $cantidad <= 0) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        // Obtener precio del producto
        if ($tipo_producto === 'plato') {
            $this->plato->id = $producto_id;
            $producto = $this->plato->obtenerPorId();
        } else {
            $this->combo->id = $producto_id;
            $producto = $this->combo->obtenerPorId();
        }

        if (!$producto || !$producto['activo']) {
            echo json_encode(['success' => false, 'message' => 'Producto no disponible']);
            return;
        }

        // Agregar al carrito
        $this->carrito->cliente_id = $cliente_id;
        $this->carrito->session_id = $session_id;
        $this->carrito->tipo_producto = $tipo_producto;
        $this->carrito->producto_id = $producto_id;
        $this->carrito->cantidad = $cantidad;
        $this->carrito->precio_unitario = $producto['precio'];
        $this->carrito->notas = $notas;

        if ($this->carrito->agregar()) {
            $total_items = $this->carrito->contarItems($cliente_id, $session_id);
            echo json_encode([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'total_items' => $total_items
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar al carrito']);
        }
    }

    /**
     * Actualizar cantidad de item en carrito (AJAX)
     */
    public function actualizarCantidad()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $cliente_id = null;
        if (ClienteAuthController::isClienteLoggedIn()) {
            $clienteData = ClienteAuthController::getClienteSesion();
            $cliente_id = $clienteData['id'];
        }

        $item_id = intval($_POST['item_id'] ?? 0);
        $cantidad = intval($_POST['cantidad'] ?? 1);

        if ($item_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Item inválido']);
            return;
        }

        if ($this->carrito->actualizarCantidad($item_id, $cantidad)) {
            $subtotal = $this->carrito->calcularTotal($cliente_id, session_id());
            
            echo json_encode([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'subtotal' => number_format($subtotal, 2)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
    }

    /**
     * Eliminar item del carrito (AJAX)
     */
    public function eliminarItem()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $cliente_id = null;
        if (ClienteAuthController::isClienteLoggedIn()) {
            $clienteData = ClienteAuthController::getClienteSesion();
            $cliente_id = $clienteData['id'];
        }

        $item_id = intval($_POST['item_id'] ?? 0);

        if ($item_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Item inválido']);
            return;
        }

        if ($this->carrito->eliminarItem($item_id)) {
            $subtotal = $this->carrito->calcularTotal($cliente_id, session_id());
            $total_items = $this->carrito->contarItems($cliente_id, session_id());
            
            echo json_encode([
                'success' => true,
                'message' => 'Item eliminado',
                'subtotal' => number_format($subtotal, 2),
                'total_items' => $total_items
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
        }
    }

    /**
     * Página de checkout
     */
    public function checkout()
    {
        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];
        $session_id = session_id();

        // Obtener items del carrito
        $items = $this->carrito->obtenerItems($cliente_id, $session_id);

        if (empty($items)) {
            $_SESSION['mensaje_info'] = 'Tu carrito está vacío';
            redirect('portal');
            return;
        }

        // Calcular totales
        $subtotal = $this->carrito->calcularTotal($cliente_id, $session_id);

        // Obtener zonas de delivery
        $zonas = $this->zona->listar(true);
        
        // Debug temporal
        error_log("DEBUG Checkout - Zonas encontradas: " . count($zonas));
        if (empty($zonas)) {
            error_log("ADVERTENCIA: No hay zonas de delivery disponibles");
        }

        // Obtener direcciones del cliente
        $this->cliente->id = $cliente_id;
        $clienteCompleto = $this->cliente->obtenerPorId();
        $direcciones = $clienteCompleto['direcciones'] ?? [];

        // Obtener métodos de pago
        require_once __DIR__ . '/../models/MetodoPago.php';
        $metodoPagoModel = new MetodoPago();
        $metodos_pago = $metodoPagoModel->listar();
        
        // Debug temporal
        error_log("DEBUG Checkout - Métodos de pago: " . count($metodos_pago));
        error_log("DEBUG Checkout - Direcciones cliente: " . count($direcciones));

        // Configuración
        $monto_minimo = floatval($this->config->obtener('monto_minimo_delivery') ?? 20);

        require_once __DIR__ . '/../views/portal/checkout.php';
    }

    /**
     * Procesar pedido desde el portal
     */
    public function procesarPedido()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('portal/checkout');
            return;
        }

        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];
        $session_id = session_id();

        // Obtener items del carrito
        $items = $this->carrito->obtenerItems($cliente_id, $session_id);

        if (empty($items)) {
            $_SESSION['mensaje_error'] = 'Tu carrito está vacío';
            redirect('portal');
            return;
        }

        // Obtener datos del formulario
        $zona_id = intval($_POST['zona_id'] ?? 0);
        $direccion = sanitize_input($_POST['direccion'] ?? '');
        $referencia = sanitize_input($_POST['referencia'] ?? '');
        $metodo_pago_id = intval($_POST['metodo_pago_id'] ?? 0);
        $notas_pedido = sanitize_input($_POST['notas'] ?? '');

        // Validaciones
        if ($zona_id <= 0 || empty($direccion) || $metodo_pago_id <= 0) {
            $_SESSION['mensaje_error'] = 'Complete todos los campos requeridos';
            redirect('portal/checkout');
            return;
        }

        // Obtener zona para calcular costo de envío
        $this->zona->id = $zona_id;
        $zona = $this->zona->obtenerPorId();

        if (!$zona || !$zona['activo']) {
            $_SESSION['mensaje_error'] = 'Zona de delivery no válida';
            redirect('portal/checkout');
            return;
        }

        // Calcular totales
        $subtotal = $this->carrito->calcularTotal($cliente_id, $session_id);
        $costo_envio = floatval($zona['costo_envio']);
        $total = $subtotal + $costo_envio;

        // Verificar monto mínimo
        $monto_minimo = floatval($this->config->obtener('monto_minimo_delivery') ?? 20);
        if ($subtotal < $monto_minimo) {
            $_SESSION['mensaje_error'] = "El monto mínimo para delivery es S/ " . number_format($monto_minimo, 2);
            redirect('portal/checkout');
            return;
        }

        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // Crear pedido
            $this->pedido->cliente_id = $cliente_id;
            $this->pedido->mesa_id = null;
            $this->pedido->usuario_id = null;
            $this->pedido->tipo = PEDIDO_DELIVERY;
            $this->pedido->estado = PEDIDO_PENDIENTE;
            $this->pedido->subtotal = $subtotal;
            $this->pedido->costo_envio = $costo_envio;
            $this->pedido->descuento = 0;
            $this->pedido->total = $total;
            $this->pedido->notas = $notas_pedido;

            if (!$this->pedido->crear()) {
                throw new Exception('Error al crear el pedido');
            }

            $pedido_id = $this->pedido->id;

            // Agregar items del carrito al pedido
            foreach ($items as $item) {
                $this->pedido->agregarItem(
                    $pedido_id,
                    $item['tipo_producto'],
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['notas']
                );
            }

            // Crear registro de delivery
            require_once __DIR__ . '/../models/Delivery.php';
            $delivery = new Delivery();
            $delivery->pedido_id = $pedido_id;
            $delivery->direccion = $direccion;
            $delivery->referencia = $referencia;
            $delivery->zona_id = $zona_id;
            $delivery->estado = DELIVERY_PENDIENTE;

            if (!$delivery->crear()) {
                throw new Exception('Error al crear el delivery');
            }

            // Vaciar carrito
            $this->carrito->vaciar($cliente_id, $session_id);

            // Registrar log
            log_actividad($this->db, null, 'PEDIDO_CLIENTE', 'pedidos', $pedido_id, 
                "Cliente {$clienteData['nombre']} realizó pedido delivery por S/ {$total}");

            // Commit
            $this->db->commit();

            $_SESSION['mensaje_exito'] = '¡Pedido realizado exitosamente! Recibirás tu pedido en aproximadamente ' . 
                                         $zona['tiempo_estimado'] . ' minutos';
            redirect('portal/mis-pedidos');

        } catch (Exception $e) {
            // Rollback en caso de error
            $this->db->rollBack();
            error_log("Error al procesar pedido: " . $e->getMessage());
            $_SESSION['mensaje_error'] = 'Error al procesar el pedido. Intente nuevamente.';
            redirect('portal/checkout');
        }
    }

    /**
     * Ver mis pedidos
     */
    public function misPedidos()
    {
        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];

        // Obtener pedidos del cliente
        $pedidos = $this->pedido->listarPorCliente($cliente_id);

        require_once __DIR__ . '/../views/portal/mis-pedidos.php';
    }

    /**
     * Ver detalle de un pedido
     */
    public function verPedido()
    {
        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $pedido_id = intval($_GET['id'] ?? 0);

        if ($pedido_id <= 0) {
            redirect('portal/mis-pedidos');
            return;
        }

        // Obtener pedido
        $pedido = $this->pedido->obtenerPorId($pedido_id);

        // Verificar que el pedido pertenece al cliente
        if (!$pedido || $pedido['cliente_id'] != $clienteData['id']) {
            $_SESSION['mensaje_error'] = 'Pedido no encontrado';
            redirect('portal/mis-pedidos');
            return;
        }

        // Obtener items del pedido
        $items = $this->pedido->obtenerItems($pedido_id);

        // Obtener información de delivery si es un pedido delivery
        $delivery = null;
        if ($pedido['tipo'] === PEDIDO_DELIVERY) {
            require_once __DIR__ . '/../models/Delivery.php';
            $deliveryModel = new Delivery();
            $delivery = $deliveryModel->obtenerPorPedidoId($pedido_id);
        }

        require_once __DIR__ . '/../views/portal/ver-pedido.php';
    }

    /**
     * Perfil del cliente
     */
    public function perfil()
    {
        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];

        // Obtener datos completos del cliente
        $this->cliente->id = $cliente_id;
        $cliente = $this->cliente->obtenerPorId();

        // Obtener reseña del cliente si existe
        $resenasCliente = $this->resena->listarPorCliente($cliente_id);
        $miResena = !empty($resenasCliente) ? $resenasCliente[0] : null;

        require_once __DIR__ . '/../views/portal/perfil.php';
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('portal/perfil');
            return;
        }

        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];

        $datos = [
            'nombre' => sanitize_input($_POST['nombre'] ?? ''),
            'telefono' => sanitize_input($_POST['telefono'] ?? '')
        ];

        if ($this->cliente->actualizarPerfil($cliente_id, $datos)) {
            // Actualizar sesión
            $_SESSION['cliente_nombre'] = $datos['nombre'];
            $_SESSION['cliente_telefono'] = $datos['telefono'];

            $_SESSION['mensaje_exito'] = 'Perfil actualizado correctamente';
        } else {
            $_SESSION['mensaje_error'] = 'Error al actualizar el perfil';
        }

        redirect('portal/perfil');
    }

    /**
     * Agregar dirección
     */
    public function agregarDireccion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('portal/perfil');
            return;
        }

        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];

        $direccion_data = [
            'direccion' => sanitize_input($_POST['direccion'] ?? ''),
            'referencia' => sanitize_input($_POST['referencia'] ?? ''),
            'principal' => isset($_POST['principal']) ? true : false
        ];

        $this->cliente->id = $cliente_id;
        if ($this->cliente->agregarDireccion($cliente_id, $direccion_data)) {
            $_SESSION['mensaje_exito'] = 'Dirección agregada correctamente';
        } else {
            $_SESSION['mensaje_error'] = 'Error al agregar dirección';
        }

        redirect('portal/perfil');
    }

    /**
     * Crear nueva reseña
     */
    public function crearResena()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('portal/perfil');
            return;
        }

        ClienteAuthController::verificarSesionCliente();

        $clienteData = ClienteAuthController::getClienteSesion();
        $cliente_id = $clienteData['id'];

        // Verificar que el cliente no tenga ya una reseña
        if ($this->resena->clienteTieneResena($cliente_id)) {
            $_SESSION['mensaje_error'] = 'Ya has dejado una reseña anteriormente';
            redirect('portal/perfil');
            return;
        }

        $calificacion = (int) ($_POST['calificacion'] ?? 0);
        $comentario = sanitize_input($_POST['comentario'] ?? '');

        // Validar datos
        if ($calificacion < 1 || $calificacion > 5) {
            $_SESSION['mensaje_error'] = 'Calificación inválida';
            redirect('portal/perfil');
            return;
        }

        if (empty($comentario) || strlen($comentario) > 500) {
            $_SESSION['mensaje_error'] = 'El comentario debe tener entre 1 y 500 caracteres';
            redirect('portal/perfil');
            return;
        }

        // Crear reseña
        $this->resena->cliente_id = $cliente_id;
        $this->resena->calificacion = $calificacion;
        $this->resena->comentario = $comentario;
        $this->resena->activo = 0; // Pendiente de aprobación

        if ($this->resena->crear()) {
            $_SESSION['mensaje_exito'] = '¡Gracias por tu reseña! Será publicada una vez revisada por nuestro equipo.';
        } else {
            $_SESSION['mensaje_error'] = 'Error al enviar la reseña. Por favor intenta nuevamente.';
        }

        redirect('portal/perfil');
    }
}
