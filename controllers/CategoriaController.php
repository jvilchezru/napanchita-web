<?php

/**
 * Controlador de Categorías
 * Maneja el CRUD de categorías de productos
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController
{
    private $db;
    private $categoria;

    public function __construct()
    {
        // Verificar sesión
        verificar_sesion();

        // Verificar permisos (solo admin)
        if (!verificar_rol(ROL_ADMIN)) {
            set_flash_message('No tienes permisos para acceder a esta sección', 'error');
            redirect('dashboard');
            return;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->categoria = new Categoria($this->db);
    }

    /**
     * Listar categorías
     */
    public function index()
    {
        $categorias = $this->categoria->listar();
        require_once __DIR__ . '/../views/categorias/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear()
    {
        require_once __DIR__ . '/../views/categorias/crear.php';
    }

    /**
     * Guardar nueva categoría
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=categorias');
            return;
        }

        // Validar datos
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $descripcion = sanitize_input($_POST['descripcion'] ?? '');
        $orden = $_POST['orden'] ?? 0;
        // El campo oculto envía 0, el checkbox envía 1 si está marcado
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;

        if (empty($nombre)) {
            set_flash_message('El nombre es obligatorio', 'error');
            redirect('index.php?action=categorias_crear');
            return;
        }

        // Verificar si ya existe
        if ($this->categoria->nombreExiste($nombre)) {
            set_flash_message('Ya existe una categoría con ese nombre', 'error');
            redirect('index.php?action=categorias_crear');
            return;
        }

        // Asignar valores
        $this->categoria->nombre = $nombre;
        $this->categoria->descripcion = $descripcion;
        $this->categoria->activo = $activo;
        $this->categoria->orden = $orden > 0 ? $orden : $this->categoria->obtenerSiguienteOrden();

        // Crear categoría
        if ($this->categoria->crear()) {
            set_flash_message('Categoría creada exitosamente', 'success');
            redirect('index.php?action=categorias');
        } else {
            set_flash_message('Error al crear la categoría', 'error');
            redirect('index.php?action=categorias_crear');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de categoría no válido', 'error');
            redirect('index.php?action=categorias');
            return;
        }

        $this->categoria->id = $id;
        $categoria = $this->categoria->obtenerPorId();

        if (!$categoria) {
            set_flash_message('Categoría no encontrada', 'error');
            redirect('index.php?action=categorias');
            return;
        }

        require_once __DIR__ . '/../views/categorias/editar.php';
    }

    /**
     * Actualizar categoría
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=categorias');
            return;
        }

        $id = $_POST['id'] ?? 0;
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $descripcion = sanitize_input($_POST['descripcion'] ?? '');
        $orden = $_POST['orden'] ?? 0;
        // El campo oculto envía 0, el checkbox envía 1 si está marcado
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;

        if (!$id || empty($nombre)) {
            set_flash_message('Datos incompletos', 'error');
            redirect('index.php?action=categorias');
            return;
        }

        // Verificar si el nombre ya existe (excepto para esta categoría)
        if ($this->categoria->nombreExiste($nombre, $id)) {
            set_flash_message('Ya existe otra categoría con ese nombre', 'error');
            redirect('index.php?action=categorias_editar&id=' . $id);
            return;
        }

        // Asignar valores
        $this->categoria->id = $id;
        $this->categoria->nombre = $nombre;
        $this->categoria->descripcion = $descripcion;
        $this->categoria->orden = $orden;
        $this->categoria->activo = $activo;

        // Actualizar
        if ($this->categoria->actualizar()) {
            set_flash_message('Categoría actualizada exitosamente', 'success');
            redirect('index.php?action=categorias');
        } else {
            set_flash_message('Error al actualizar la categoría', 'error');
            redirect('index.php?action=categorias_editar&id=' . $id);
        }
    }

    /**
     * Cambiar estado de categoría (AJAX)
     */
    public function cambiarEstado()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? 0;
        $estado = $_POST['estado'] ?? 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            return;
        }

        if ($this->categoria->cambiarEstado($id, $estado)) {
            echo json_encode([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ]);
        }
    }

    /**
     * Eliminar categoría (AJAX)
     */
    public function eliminar()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            return;
        }

        // Verificar si la categoría tiene productos
        $cantidadProductos = $this->categoria->contarProductos($id);

        if ($cantidadProductos > 0) {
            echo json_encode([
                'success' => false,
                'message' => "No se puede eliminar. La categoría tiene {$cantidadProductos} producto(s) asociado(s)"
            ]);
            return;
        }

        $this->categoria->id = $id;

        if ($this->categoria->eliminar()) {
            echo json_encode([
                'success' => true,
                'message' => 'Categoría eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la categoría'
            ]);
        }
    }

    /**
     * Buscar categorías (AJAX)
     */
    public function buscar()
    {
        header('Content-Type: application/json');

        $termino = sanitize_input($_GET['termino'] ?? '');

        if (empty($termino)) {
            echo json_encode([]);
            return;
        }

        $resultados = $this->categoria->buscar($termino);
        echo json_encode($resultados);
    }
}
