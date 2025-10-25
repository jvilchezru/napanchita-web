<?php
/**
 * Controlador de Pedidos
 * Gestiona la creación y seguimiento de pedidos
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pedido.php';

class PedidoController {
    private $db;
    private $pedido;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pedido = new Pedido($this->db);
    }

    // Crear pedido
    public function crear() {
        AuthController::verificarSesion();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            
            $this->pedido->usuario_id = $_SESSION['usuario_id'];
            $this->pedido->total = $data['total'] ?? 0;
            $this->pedido->direccion_entrega = $data['direccion'] ?? '';
            $this->pedido->telefono_contacto = $data['telefono'] ?? '';
            $this->pedido->notas = $data['notas'] ?? '';
            
            $items = $data['items'] ?? [];
            
            $pedido_id = $this->pedido->crear($items);
            
            if ($pedido_id) {
                echo json_encode(['success' => true, 'pedido_id' => $pedido_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear pedido']);
            }
        }
    }

    // Listar pedidos del usuario
    public function misPedidos() {
        AuthController::verificarSesion();
        
        $this->pedido->usuario_id = $_SESSION['usuario_id'];
        $stmt = $this->pedido->listarPorUsuario();
        $pedidos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($pedidos);
    }

    // Listar todos los pedidos (admin)
    public function listarTodos() {
        AuthController::verificarAdmin();
        
        $stmt = $this->pedido->listarTodos();
        $pedidos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($pedidos);
    }

    // Obtener detalles del pedido
    public function detalles($id) {
        AuthController::verificarSesion();
        
        $this->pedido->id = $id;
        $pedido = $this->pedido->obtenerPorId();
        
        // Verificar que el usuario tenga permiso
        if ($_SESSION['usuario_rol'] !== 'admin' && $pedido['usuario_id'] != $_SESSION['usuario_id']) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }
        
        $stmt = $this->pedido->obtenerDetalles();
        $detalles = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $detalles[] = $row;
        }
        
        $pedido['detalles'] = $detalles;
        
        header('Content-Type: application/json');
        echo json_encode($pedido);
    }

    // Actualizar estado del pedido (admin o cliente para cancelar)
    public function actualizarEstado() {
        AuthController::verificarSesion();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->pedido->id = $_POST['id'] ?? 0;
            $nuevoEstado = $_POST['estado'] ?? '';

            // Obtener información del pedido
            $pedidoActual = $this->pedido->obtenerPorId();

            // Si no es admin, verificar que sea el dueño del pedido y que solo cancele pedidos pendientes
            if ($_SESSION['usuario_rol'] !== 'admin') {
                if ($pedidoActual['usuario_id'] != $_SESSION['usuario_id']) {
                    echo json_encode(['success' => false, 'message' => 'No autorizado']);
                    return;
                }
                
                if ($nuevoEstado !== 'cancelado' || $pedidoActual['estado'] !== 'pendiente') {
                    echo json_encode(['success' => false, 'message' => 'Solo puedes cancelar pedidos pendientes']);
                    return;
                }
            }

            $this->pedido->estado = $nuevoEstado;

            if ($this->pedido->actualizarEstado()) {
                // TODO: Aquí se podría enviar notificación
                echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
            }
        }
    }
}
?>
