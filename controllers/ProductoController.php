<?php
/**
 * Controlador de Productos
 * Gestiona las operaciones del menú de productos
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    private $db;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->producto = new Producto($this->db);
    }

    // Listar todos los productos (API JSON)
    public function listar() {
        $stmt = $this->producto->listar();
        $productos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($productos);
    }

    // Obtener producto por ID (API JSON)
    public function obtener($id) {
        $this->producto->id = $id;
        $producto = $this->producto->obtenerPorId();
        
        header('Content-Type: application/json');
        echo json_encode($producto);
    }

    // Crear producto (admin)
    public function crear() {
        AuthController::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->producto->nombre = $_POST['nombre'] ?? '';
            $this->producto->descripcion = $_POST['descripcion'] ?? '';
            $this->producto->precio = $_POST['precio'] ?? 0;
            $this->producto->categoria_id = $_POST['categoria_id'] ?? null;
            $this->producto->imagen = $_POST['imagen'] ?? 'default.jpg';

            if ($this->producto->crear()) {
                echo json_encode(['success' => true, 'message' => 'Producto creado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear producto']);
            }
        }
    }

    // Actualizar producto (admin)
    public function actualizar() {
        AuthController::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->producto->id = $_POST['id'] ?? 0;
            $this->producto->nombre = $_POST['nombre'] ?? '';
            $this->producto->descripcion = $_POST['descripcion'] ?? '';
            $this->producto->precio = $_POST['precio'] ?? 0;
            $this->producto->categoria_id = $_POST['categoria_id'] ?? null;
            $this->producto->disponible = $_POST['disponible'] ?? 1;

            if ($this->producto->actualizar()) {
                echo json_encode(['success' => true, 'message' => 'Producto actualizado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar producto']);
            }
        }
    }

    // Buscar productos
    public function buscar() {
        $termino = $_GET['q'] ?? '';
        $stmt = $this->producto->buscar($termino);
        $productos = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productos[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($productos);
    }
}
?>
