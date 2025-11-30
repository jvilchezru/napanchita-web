<?php
require_once __DIR__ . '/../models/MetodoPago.php';
require_once __DIR__ . '/../config/helpers.php';

class MetodoPagoController {
    private $metodoPago;

    public function __construct() {
        $this->metodoPago = new MetodoPago();
    }

    /**
     * Listar métodos de pago
     */
    public function index() {
        $filtros = [];
        
        if (isset($_GET['activo']) && $_GET['activo'] !== '') {
            $filtros['activo'] = $_GET['activo'];
        }
        
        if (isset($_GET['buscar']) && $_GET['buscar']) {
            $filtros['buscar'] = $_GET['buscar'];
        }

        $metodosPago = $this->metodoPago->listar($filtros);

        include __DIR__ . '/../views/metodos_pago/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear() {
        include __DIR__ . '/../views/metodos_pago/crear.php';
    }

    /**
     * Guardar nuevo método de pago
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=metodos_pago');
            return;
        }

        $this->metodoPago->nombre = trim($_POST['nombre'] ?? '');
        $this->metodoPago->descripcion = trim($_POST['descripcion'] ?? '');
        $this->metodoPago->activo = isset($_POST['activo']) ? 1 : 0;

        // Validaciones
        if (empty($this->metodoPago->nombre)) {
            set_flash_message('El nombre es requerido', 'error');
            redirect('index.php?action=metodos_pago_crear');
            return;
        }

        $resultado = $this->metodoPago->crear();

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=metodos_pago');
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('index.php?action=metodos_pago');
            return;
        }

        $metodoPago = $this->metodoPago->obtenerPorId($id);
        if (!$metodoPago) {
            set_flash_message('Método de pago no encontrado', 'error');
            redirect('index.php?action=metodos_pago');
            return;
        }

        include __DIR__ . '/../views/metodos_pago/editar.php';
    }

    /**
     * Actualizar método de pago
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=metodos_pago');
            return;
        }

        $this->metodoPago->id = $_POST['id'] ?? null;
        $this->metodoPago->nombre = trim($_POST['nombre'] ?? '');
        $this->metodoPago->descripcion = trim($_POST['descripcion'] ?? '');
        $this->metodoPago->activo = isset($_POST['activo']) ? 1 : 0;

        // Validaciones
        if (empty($this->metodoPago->nombre)) {
            set_flash_message('El nombre es requerido', 'error');
            redirect('index.php?action=metodos_pago_editar&id=' . $this->metodoPago->id);
            return;
        }

        $resultado = $this->metodoPago->actualizar();

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=metodos_pago');
    }

    /**
     * Eliminar método de pago
     */
    public function eliminar() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('index.php?action=metodos_pago');
            return;
        }

        $resultado = $this->metodoPago->eliminar($id);

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=metodos_pago');
    }

    /**
     * Cambiar estado (AJAX)
     */
    public function cambiarEstado() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $activo = $_POST['activo'] ?? null;

        if (!$id || $activo === null) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $resultado = $this->metodoPago->cambiarEstado($id, $activo);
        echo json_encode($resultado);
    }
}
