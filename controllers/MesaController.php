<?php

require_once __DIR__ . '/../models/Mesa.php';

/**
 * Controlador de Mesas
 * Gestiona las operaciones CRUD y visualización de mesas
 */
class MesaController
{
    private $mesa;

    public function __construct()
    {
        $this->mesa = new Mesa();
    }

    /**
     * Mostrar listado de mesas con visualización gráfica
     */
    public function index()
    {
        $mesas = $this->mesa->listar();
        $estadisticas = $this->mesa->obtenerEstadisticas();
        require_once __DIR__ . '/../views/mesas/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear()
    {
        require_once __DIR__ . '/../views/mesas/crear.php';
    }

    /**
     * Guardar nueva mesa
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=mesas');
            return;
        }

        // Validar datos
        $numero = sanitize_input($_POST['numero'] ?? '');
        $capacidad = $_POST['capacidad'] ?? 0;
        $estado = $_POST['estado'] ?? 'disponible';
        $posicion_x = $_POST['posicion_x'] ?? 0;
        $posicion_y = $_POST['posicion_y'] ?? 0;
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;

        if (empty($numero) || $capacidad <= 0) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('index.php?action=mesas_crear');
            return;
        }

        // Verificar que el número no exista
        if ($this->mesa->obtenerPorNumero($numero)) {
            set_flash_message('Ya existe una mesa con ese número', 'error');
            redirect('index.php?action=mesas_crear');
            return;
        }

        // Asignar valores
        $this->mesa->numero = $numero;
        $this->mesa->capacidad = $capacidad;
        $this->mesa->estado = $estado;
        $this->mesa->posicion_x = $posicion_x;
        $this->mesa->posicion_y = $posicion_y;
        $this->mesa->activo = $activo;

        // Crear mesa
        if ($this->mesa->crear()) {
            set_flash_message('Mesa creada exitosamente', 'success');
            redirect('index.php?action=mesas');
        } else {
            set_flash_message('Error al crear la mesa', 'error');
            redirect('index.php?action=mesas_crear');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de mesa no válido', 'error');
            redirect('index.php?action=mesas');
            return;
        }

        $this->mesa->id = $id;
        $mesa = $this->mesa->obtenerPorId();

        if (!$mesa) {
            set_flash_message('Mesa no encontrada', 'error');
            redirect('index.php?action=mesas');
            return;
        }

        require_once __DIR__ . '/../views/mesas/editar.php';
    }

    /**
     * Actualizar mesa existente
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=mesas');
            return;
        }

        $id = $_POST['id'] ?? 0;
        $numero = sanitize_input($_POST['numero'] ?? '');
        $capacidad = $_POST['capacidad'] ?? 0;
        $estado = $_POST['estado'] ?? 'disponible';
        $posicion_x = $_POST['posicion_x'] ?? 0;
        $posicion_y = $_POST['posicion_y'] ?? 0;
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;

        if (!$id || empty($numero) || $capacidad <= 0) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('index.php?action=mesas');
            return;
        }

        // Verificar que la mesa existe
        $this->mesa->id = $id;
        $mesa_actual = $this->mesa->obtenerPorId();

        if (!$mesa_actual) {
            set_flash_message('Mesa no encontrada', 'error');
            redirect('index.php?action=mesas');
            return;
        }

        // Verificar que el número no esté en uso por otra mesa
        $mesa_con_numero = $this->mesa->obtenerPorNumero($numero);
        if ($mesa_con_numero && $mesa_con_numero['id'] != $id) {
            set_flash_message('Ya existe otra mesa con ese número', 'error');
            redirect('index.php?action=mesas_editar&id=' . $id);
            return;
        }

        // Asignar valores
        $this->mesa->id = $id;
        $this->mesa->numero = $numero;
        $this->mesa->capacidad = $capacidad;
        $this->mesa->estado = $estado;
        $this->mesa->posicion_x = $posicion_x;
        $this->mesa->posicion_y = $posicion_y;
        $this->mesa->activo = $activo;

        // Actualizar mesa
        if ($this->mesa->actualizar()) {
            set_flash_message('Mesa actualizada exitosamente', 'success');
            redirect('index.php?action=mesas');
        } else {
            set_flash_message('Error al actualizar la mesa', 'error');
            redirect('index.php?action=mesas_editar&id=' . $id);
        }
    }

    /**
     * Cambiar estado de una mesa (AJAX)
     */
    public function cambiarEstado()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? 0;
        $estado = $_POST['estado'] ?? '';

        if (!$id || !in_array($estado, ['disponible', 'ocupada', 'reservada', 'inactiva'])) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        if ($this->mesa->cambiarEstado($id, $estado)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    /**
     * Actualizar posición de mesa en el layout (AJAX)
     */
    public function actualizarPosicion()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? 0;
        $x = $_POST['x'] ?? 0;
        $y = $_POST['y'] ?? 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        if ($this->mesa->actualizarPosicion($id, $x, $y)) {
            echo json_encode(['success' => true, 'message' => 'Posición actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la posición']);
        }
    }

    /**
     * Eliminar mesa (desactivar)
     */
    public function eliminar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de mesa no válido', 'error');
            redirect('index.php?action=mesas');
            return;
        }

        $this->mesa->id = $id;

        if ($this->mesa->eliminar()) {
            set_flash_message('Mesa desactivada correctamente', 'success');
        } else {
            set_flash_message('Error al desactivar la mesa', 'error');
        }

        redirect('index.php?action=mesas');
    }

    /**
     * Obtener lista de mesas en formato JSON (AJAX)
     */
    public function listarJson()
    {
        header('Content-Type: application/json');

        $soloActivos = isset($_GET['activos']) ? (bool)$_GET['activos'] : false;
        $estado = $_GET['estado'] ?? null;
        $capacidadMin = isset($_GET['capacidad_min']) ? (int)$_GET['capacidad_min'] : null;

        $mesas = $this->mesa->listar($soloActivos, $estado, $capacidadMin);
        echo json_encode($mesas);
    }
}
