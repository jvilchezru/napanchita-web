<?php

/**
 * Controlador de Platos
 * Maneja el CRUD de platos con upload de imágenes
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Plato.php';
require_once __DIR__ . '/../models/Categoria.php';

class PlatoController
{
    private $db;
    private $plato;
    private $categoria;
    private $upload_dir;

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
        $this->plato = new Plato($this->db);
        $this->categoria = new Categoria($this->db);
        $this->upload_dir = __DIR__ . '/../public/images/platos/';

        // Crear directorio si no existe
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    /**
     * Listar platos
     */
    public function index()
    {
        $platos = $this->plato->listar();
        $categorias = $this->categoria->listar();
        require_once __DIR__ . '/../views/platos/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear()
    {
        $categorias = $this->categoria->listar(); // Todas las categorías
        require_once __DIR__ . '/../views/platos/crear.php';
    }

    /**
     * Guardar nuevo plato
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=platos');
            return;
        }

        // Validar datos
        $categoria_id = $_POST['categoria_id'] ?? 0;
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $descripcion = sanitize_input($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? 0;
        // El campo oculto envía 0, el checkbox envía 1 si está marcado
        $disponible = isset($_POST['disponible']) ? (int)$_POST['disponible'] : 0;

        if (empty($nombre) || !$categoria_id || $precio <= 0) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('index.php?action=platos_crear');
            return;
        }

        // Procesar imagen
        $imagen_url = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultado_upload = $this->procesarImagen($_FILES['imagen']);

            if ($resultado_upload['success']) {
                $imagen_url = $resultado_upload['url'];
            } else {
                set_flash_message($resultado_upload['message'], 'error');
                redirect('index.php?action=platos_crear');
                return;
            }
        }

        // Asignar valores
        $this->plato->categoria_id = $categoria_id;
        $this->plato->nombre = $nombre;
        $this->plato->descripcion = $descripcion;
        $this->plato->precio = $precio;
        $this->plato->imagen_url = $imagen_url;
        $this->plato->disponible = $disponible;

        // Crear plato
        if ($this->plato->crear()) {
            set_flash_message('Plato creado exitosamente', 'success');
            redirect('index.php?action=platos');
        } else {
            // Eliminar imagen si falla la creación
            if ($imagen_url && file_exists($imagen_url)) {
                unlink($imagen_url);
            }
            set_flash_message('Error al crear el plato', 'error');
            redirect('index.php?action=platos_crear');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de plato no válido', 'error');
            redirect('index.php?action=platos');
            return;
        }

        $this->plato->id = $id;
        $plato = $this->plato->obtenerPorId();

        if (!$plato) {
            set_flash_message('Plato no encontrada', 'error');
            redirect('index.php?action=platos');
            return;
        }

        $categorias = $this->categoria->listar(); // Todas las categorías
        require_once __DIR__ . '/../views/platos/editar.php';
    }

    /**
     * Actualizar plato
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=platos');
            return;
        }

        $id = $_POST['id'] ?? 0;
        $categoria_id = $_POST['categoria_id'] ?? 0;
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $descripcion = sanitize_input($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? 0;
        // El campo oculto envía 0, el checkbox envía 1 si está marcado
        $disponible = isset($_POST['disponible']) ? (int)$_POST['disponible'] : 0;

        if (!$id || empty($nombre) || !$categoria_id || $precio <= 0) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('index.php?action=platos');
            return;
        }

        // Obtener plato actual
        $this->plato->id = $id;
        $plato_actual = $this->plato->obtenerPorId();

        if (!$plato_actual) {
            set_flash_message('Plato no encontrado', 'error');
            redirect('index.php?action=platos');
            return;
        }

        $imagen_url = $plato_actual['imagen_url'];

        // Procesar nueva imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultado_upload = $this->procesarImagen($_FILES['imagen']);

            if ($resultado_upload['success']) {
                // Eliminar imagen anterior
                if ($imagen_url && file_exists($imagen_url)) {
                    unlink($imagen_url);
                }
                $imagen_url = $resultado_upload['url'];
            } else {
                set_flash_message($resultado_upload['message'], 'error');
                redirect('index.php?action=platos_editar&id=' . $id);
                return;
            }
        }

        // Asignar valores
        $this->plato->id = $id;
        $this->plato->categoria_id = $categoria_id;
        $this->plato->nombre = $nombre;
        $this->plato->descripcion = $descripcion;
        $this->plato->precio = $precio;
        $this->plato->imagen_url = $imagen_url;
        $this->plato->disponible = $disponible;

        // Actualizar
        if ($this->plato->actualizar()) {
            set_flash_message('Plato actualizado exitosamente', 'success');
            redirect('index.php?action=platos');
        } else {
            set_flash_message('Error al actualizar el plato', 'error');
            redirect('index.php?action=platos_editar&id=' . $id);
        }
    }

    /**
     * Cambiar estado de plato (AJAX)
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

        if ($this->plato->cambiarEstado($id, $estado)) {
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
     * Eliminar plato (AJAX)
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

        // Obtener plato para eliminar imagen
        $this->plato->id = $id;
        $plato = $this->plato->obtenerPorId();

        if (!$plato) {
            echo json_encode(['success' => false, 'message' => 'Plato no encontrado']);
            return;
        }

        // Eliminar
        if ($this->plato->eliminar()) {
            echo json_encode([
                'success' => true,
                'message' => 'Plato eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el plato'
            ]);
        }
    }

    /**
     * Buscar platos (AJAX)
     */
    public function buscar()
    {
        header('Content-Type: application/json');

        $termino = sanitize_input($_GET['termino'] ?? '');

        if (empty($termino)) {
            echo json_encode([]);
            return;
        }

        $resultados = $this->plato->buscar($termino);
        echo json_encode($resultados);
    }

    /**
     * Procesar imagen subida
     * @param array $file Archivo de $_FILES
     * @return array ['success' => bool, 'url' => string|null, 'message' => string]
     */
    private function procesarImagen($file)
    {
        // Validar tipo de archivo
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $tipo = $file['type'];
        $nombre_original = $file['name'];
        $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

        if (!in_array($tipo, $tipos_permitidos) || !in_array($extension, $extensiones_permitidas)) {
            return [
                'success' => false,
                'url' => null,
                'message' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP'
            ];
        }

        // Validar tamaño (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            return [
                'success' => false,
                'url' => null,
                'message' => 'El archivo es muy grande. Máximo 5MB'
            ];
        }

        // Generar nombre único
        $nombre_nuevo = uniqid('prod_') . '.' . $extension;
        $ruta_completa = $this->upload_dir . $nombre_nuevo;
        $ruta_relativa = 'public/images/platos/' . $nombre_nuevo;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $ruta_completa)) {
            return [
                'success' => true,
                'url' => $ruta_relativa,
                'message' => 'Imagen subida correctamente'
            ];
        } else {
            return [
                'success' => false,
                'url' => null,
                'message' => 'Error al subir la imagen'
            ];
        }
    }
}
