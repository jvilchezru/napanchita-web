<?php

/**
 * Controlador de Pedidos
 * Maneja el sistema completo de pedidos multi-canal (mesa, delivery, para llevar)
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Combo.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/MetodoPago.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class PedidoController
{
    private $db;
    private $pedido;
    private $cliente;
    private $plato;
    private $combo;
    private $mesa;
    private $categoria;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pedido = new Pedido($this->db);
        $this->cliente = new Cliente();
        $this->plato = new Plato($this->db);
        $this->combo = new Combo($this->db);
        $this->mesa = new Mesa($this->db);
        $this->categoria = new Categoria($this->db);
    }

    /**
     * Listar todos los pedidos
     */
    public function index()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        // Obtener filtros
        $filtros = [];
        if (isset($_GET['tipo'])) $filtros['tipo'] = $_GET['tipo'];
        if (isset($_GET['estado'])) $filtros['estado'] = $_GET['estado'];
        if (isset($_GET['fecha'])) $filtros['fecha'] = $_GET['fecha'];

        $pedidos = $this->pedido->listar($filtros);

        require_once __DIR__ . '/../views/pedidos/index.php';
    }

    /**
     * Mostrar formulario POS para crear pedido
     */
    public function crear()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        // Capturar parámetros si vienen desde una reserva completada
        $mesa_id_reserva = isset($_GET['mesa_id']) ? intval($_GET['mesa_id']) : null;
        $reserva_id = isset($_GET['reserva_id']) ? intval($_GET['reserva_id']) : null;
        $reserva_data = null;

        // Si viene de una reserva, obtener sus datos
        if ($reserva_id) {
            require_once __DIR__ . '/../models/Reserva.php';
            $reservaModel = new Reserva();
            $reserva_data = $reservaModel->obtenerPorId($reserva_id);
        }

        // Obtener datos necesarios
        $platos = $this->plato->listar(true); // Solo disponibles
        $combos = $this->combo->listar(true); // Solo activos
        $mesas = $this->mesa->listarDisponibles(); // Solo mesas disponibles
        $clientes = $this->cliente->listar(true);
        $categorias = $this->categoria->listar(true); // Solo activas

        require_once __DIR__ . '/../views/pedidos/crear.php';
    }

    /**
     * Guardar nuevo pedido
     */
    public function guardar()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('pedidos');
            return;
        }

        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // Obtener datos del formulario
            $tipo = $_POST['tipo'] ?? 'mesa';
            $cliente_id = !empty($_POST['cliente_id']) ? intval($_POST['cliente_id']) : null;
            $mesa_id = !empty($_POST['mesa_id']) ? intval($_POST['mesa_id']) : null;
            $notas = sanitize_input($_POST['notas'] ?? '');
            $costo_envio = floatval($_POST['costo_envio'] ?? 0);
            $descuento = floatval($_POST['descuento'] ?? 0);
            $items = json_decode($_POST['items'] ?? '[]', true);

            // Validaciones
            if (empty($items)) {
                throw new Exception('Debe agregar al menos un producto al pedido');
            }

            if ($tipo === 'mesa' && !$mesa_id) {
                throw new Exception('Debe seleccionar una mesa para pedidos de tipo mesa');
            }

            if ($tipo === 'delivery' && !$cliente_id) {
                throw new Exception('Debe seleccionar un cliente para pedidos de delivery');
            }

            // Calcular subtotal
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += floatval($item['subtotal']);
            }

            $total = $subtotal + $costo_envio - $descuento;

            // Crear pedido
            $this->pedido->cliente_id = $cliente_id;
            $this->pedido->mesa_id = $mesa_id;
            $this->pedido->usuario_id = $_SESSION['usuario_id'];
            $this->pedido->tipo = $tipo;
            $this->pedido->estado = 'pendiente';
            $this->pedido->subtotal = $subtotal;
            $this->pedido->costo_envio = $costo_envio;
            $this->pedido->descuento = $descuento;
            $this->pedido->total = $total;
            $this->pedido->notas = $notas;

            if (!$this->pedido->crear()) {
                // Obtener error de PDO si existe
                $errorInfo = $this->db->errorInfo();
                $errorMsg = 'Error al crear el pedido';
                if (!empty($errorInfo[2])) {
                    $errorMsg .= ': ' . $errorInfo[2];
                }
                throw new Exception($errorMsg);
            }

            // Agregar items al pedido
            foreach ($items as $item) {
                $item_data = [
                    'plato_id' => $item['tipo'] === 'producto' ? $item['id'] : null,
                    'combo_id' => $item['tipo'] === 'combo' ? $item['id'] : null,
                    'tipo' => $item['tipo'],
                    'nombre' => $item['nombre'],
                    'cantidad' => intval($item['cantidad']),
                    'precio_unitario' => floatval($item['precio_unitario']),
                    'subtotal' => floatval($item['subtotal']),
                    'notas' => $item['notas'] ?? null
                ];

                if (!$this->pedido->agregarItem($item_data)) {
                    throw new Exception('Error al agregar items al pedido');
                }
            }

            // Confirmar transacción
            $this->db->commit();

            // Log de actividad
            log_actividad($this->db, $_SESSION['usuario_id'], 'CREAR_PEDIDO', 'pedidos', $this->pedido->id, "Pedido tipo: $tipo, Total: S/ $total");

            $_SESSION['success'] = 'Pedido creado exitosamente';
            
            // Redirigir según el tipo
            if ($tipo === 'mesa') {
                redirect('pedidos_ver&id=' . $this->pedido->id);
            } else {
                redirect('pedidos');
            }

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
            redirect('pedidos_crear');
        }
    }

    /**
     * Ver detalle del pedido
     */
    public function ver($id)
    {
        // Prevenir caché
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        
        // TIMESTAMP: 2025-11-30 10:50:00
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        $this->pedido->id = $id;
        $pedido = $this->pedido->obtenerPorId();

        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado';
            redirect('pedidos');
            return;
        }

        // FORZAR carga de items desde el controlador
        $pedido['items'] = [];
        $pedido['debug_controller'] = 'Ejecutando desde controlador';
        
        $queryItems = "SELECT * FROM pedido_items WHERE pedido_id = " . intval($id);
        
        try {
            $resultItems = $this->db->query($queryItems);
            if ($resultItems) {
                $items = $resultItems->fetchAll(PDO::FETCH_ASSOC);
                $pedido['items'] = $items;
                $pedido['debug_count'] = count($items);
            }
        } catch (PDOException $e) {
            $pedido['debug_error'] = $e->getMessage();
        }

        // Obtener información de venta si el pedido está finalizado
        $venta = null;
        if ($pedido['estado'] === 'finalizado') {
            $stmt = $this->db->prepare("
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

        require_once __DIR__ . '/../views/pedidos/ver.php';
    }

    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $id = intval($_POST['id'] ?? 0);
        $nuevo_estado = $_POST['estado'] ?? '';

        // Validar estado
        $estados_validos = ['pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado'];
        if (!in_array($nuevo_estado, $estados_validos)) {
            json_response(['success' => false, 'message' => 'Estado no válido']);
            return;
        }

        if ($this->pedido->cambiarEstado($id, $nuevo_estado)) {
            log_actividad($this->db, $_SESSION['usuario_id'], 'CAMBIAR_ESTADO_PEDIDO', 'pedidos', $id, "Nuevo estado: $nuevo_estado");
            json_response(['success' => true, 'message' => 'Estado actualizado correctamente', 'nuevo_estado' => $nuevo_estado]);
        } else {
            json_response(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    /**
     * Cancelar pedido
     */
    public function cancelar($id)
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $this->pedido->id = $id;

        if ($this->pedido->cancelar()) {
            log_actividad($this->db, $_SESSION['usuario_id'], 'CANCELAR_PEDIDO', 'pedidos', $id);
            json_response(['success' => true, 'message' => 'Pedido cancelado correctamente']);
        } else {
            json_response(['success' => false, 'message' => 'Error al cancelar el pedido']);
        }
    }

    /**
     * Finalizar pedido (cobrar y liberar mesa)
     */
    public function finalizar()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $pedido_id = $_POST['id'] ?? null;
        $metodo_pago_id = $_POST['metodo_pago_id'] ?? null;
        $monto_recibido = $_POST['monto_recibido'] ?? null;
        $monto_cambio = $_POST['monto_cambio'] ?? 0;

        if (!$pedido_id) {
            json_response(['success' => false, 'message' => 'ID de pedido no proporcionado']);
            return;
        }

        if (!$metodo_pago_id) {
            json_response(['success' => false, 'message' => 'Método de pago no proporcionado']);
            return;
        }

        try {
            // Obtener información del pedido
            $this->pedido->id = $pedido_id;
            $pedido = $this->pedido->obtenerPorId();
            
            if (!$pedido) {
                json_response(['success' => false, 'message' => 'Pedido no encontrado']);
                return;
            }

            // Verificar que esté en estado entregado
            if ($pedido['estado'] !== 'entregado') {
                json_response(['success' => false, 'message' => 'Solo se pueden finalizar pedidos entregados']);
                return;
            }

            // Validar monto recibido
            if ($monto_recibido === null || $monto_recibido < $pedido['total']) {
                json_response(['success' => false, 'message' => 'Monto recibido inválido']);
                return;
            }

            // Iniciar transacción
            $this->db->beginTransaction();

            // Crear registro de venta
            $stmt = $this->db->prepare("
                INSERT INTO ventas (pedido_id, metodo_pago_id, total, monto_recibido, monto_cambio, fecha_venta, usuario_id) 
                VALUES (:pedido_id, :metodo_pago_id, :total, :monto_recibido, :monto_cambio, NOW(), :usuario_id)
            ");
            
            $stmt->bindParam(':pedido_id', $pedido_id);
            $stmt->bindParam(':metodo_pago_id', $metodo_pago_id);
            $stmt->bindParam(':total', $pedido['total']);
            $stmt->bindParam(':monto_recibido', $monto_recibido);
            $stmt->bindParam(':monto_cambio', $monto_cambio);
            $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al registrar la venta');
            }

            // Cambiar estado a finalizado (el trigger se encarga de liberar la mesa automáticamente)
            if (!$this->pedido->cambiarEstado($pedido_id, 'finalizado')) {
                throw new Exception('Error al finalizar el pedido');
            }

            // Confirmar transacción
            $this->db->commit();

            // Log de actividad
            log_actividad($this->db, $_SESSION['usuario_id'], 'FINALIZAR_PEDIDO', 'pedidos', $pedido_id);

            json_response([
                'success' => true, 
                'message' => 'Pedido cobrado y finalizado correctamente' . ($pedido['tipo'] === 'mesa' ? ' - Mesa liberada' : '')
            ]);

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();
            json_response(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Vista de cocina en tiempo real
     */
    public function cocina()
    {
        AuthController::verificarAdmin();

        $pedidos = $this->pedido->obtenerPendientesCocina();

        require_once __DIR__ . '/../views/pedidos/cocina.php';
    }

    /**
     * Obtener pedidos pendientes (AJAX para actualización en tiempo real)
     */
    public function obtenerPendientes()
    {
        AuthController::verificarAdmin();

        $pedidos = $this->pedido->obtenerPendientesCocina();
        json_response(['success' => true, 'data' => $pedidos]);
    }

    /**
     * Obtener estadísticas del día (AJAX)
     */
    public function obtenerEstadisticas()
    {
        AuthController::verificarAdmin();

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $estadisticas = $this->pedido->obtenerEstadisticas($fecha);

        json_response(['success' => true, 'data' => $estadisticas]);
    }

    /**
     * Buscar cliente por teléfono (AJAX)
     */
    public function buscarCliente()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        $telefono = $_GET['telefono'] ?? '';
        $nombre = $_GET['nombre'] ?? '';

        if (empty($telefono) && empty($nombre)) {
            json_response(['success' => false, 'message' => 'Teléfono o nombre requerido']);
            return;
        }

        // Buscar por nombre o teléfono
        if (!empty($nombre)) {
            $cliente = $this->cliente->obtenerPorNombre($nombre);
        } else {
            $cliente = $this->cliente->obtenerPorTelefono($telefono);
        }

        if ($cliente) {
            json_response(['success' => true, 'data' => $cliente]);
        } else {
            json_response(['success' => false, 'message' => 'Cliente no encontrado']);
        }
    }

    /**
     * Buscar clientes para autocompletado (AJAX)
     */
    public function buscarClientesAutocomplete()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        $telefono = $_GET['telefono'] ?? '';
        $nombre = $_GET['nombre'] ?? '';

        if (empty($telefono) && empty($nombre)) {
            json_response(['success' => false, 'message' => 'Búsqueda requerida']);
            return;
        }

        try {
            // Buscar múltiples clientes por nombre o teléfono
            if (!empty($nombre)) {
                $clientes = $this->cliente->buscarPorNombre($nombre);
            } else {
                $clientes = $this->cliente->buscarPorTelefono($telefono);
            }

            json_response(['success' => true, 'data' => $clientes]);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => 'Error en la búsqueda']);
        }
    }

    /**
     * Crear cliente rápido desde POS (AJAX)
     */
    public function crearClienteRapido()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        $direccion = sanitize_input($_POST['direccion'] ?? '');
        $referencia = sanitize_input($_POST['referencia'] ?? '');

        // Validaciones
        if (empty($nombre) || empty($telefono)) {
            json_response(['success' => false, 'message' => 'Nombre y teléfono son obligatorios']);
            return;
        }

        // Verificar si ya existe
        if ($this->cliente->obtenerPorTelefono($telefono)) {
            json_response(['success' => false, 'message' => 'Ya existe un cliente con ese teléfono']);
            return;
        }

        // Crear cliente
        $direcciones = [];
        if (!empty($direccion)) {
            $direcciones[] = [
                'id' => 1,
                'direccion' => $direccion,
                'referencia' => $referencia,
                'principal' => true
            ];
        }

        $this->cliente->nombre = $nombre;
        $this->cliente->telefono = $telefono;
        $this->cliente->email = null;
        $this->cliente->direcciones = $direcciones;
        $this->cliente->notas = null;
        $this->cliente->activo = true;

        if ($this->cliente->crear()) {
            $cliente_data = $this->cliente->obtenerPorId();
            log_actividad($this->db, $_SESSION['usuario_id'], 'CREAR_CLIENTE_RAPIDO', 'clientes', $this->cliente->id);
            json_response(['success' => true, 'message' => 'Cliente creado', 'data' => $cliente_data]);
        } else {
            json_response(['success' => false, 'message' => 'Error al crear el cliente']);
        }
    }

    /**
     * Imprimir ticket/comanda del pedido
     */
    public function imprimirTicket($id)
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        $this->pedido->id = $id;
        $pedido = $this->pedido->obtenerPorId();

        if (!$pedido) {
            $_SESSION['error'] = 'Pedido no encontrado';
            redirect('pedidos');
            return;
        }

        // Vista para imprimir (sin layout)
        require_once __DIR__ . '/../views/pedidos/ticket.php';
    }

    /**
     * Obtener platos y combos para el POS (AJAX)
     */
    public function obtenerMenu()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        $productos = $this->producto->listar(true);
        $combos = $this->combo->listar(true);

        json_response([
            'success' => true,
            'data' => [
                'productos' => $productos,
                'combos' => $combos
            ]
        ]);
    }

    /**
     * Obtener métodos de pago activos (AJAX)
     */
    public function obtenerMetodosPago()
    {
        AuthController::verificarRol([ROL_ADMIN, ROL_MESERO]);

        try {
            $metodoPago = new MetodoPago($this->db);
            $metodos = $metodoPago->listarActivos();

            json_response([
                'success' => true,
                'data' => $metodos
            ]);
        } catch (Exception $e) {
            json_response([
                'success' => false,
                'message' => 'Error al obtener métodos de pago: ' . $e->getMessage()
            ]);
        }
    }
}
