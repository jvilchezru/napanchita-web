<?php

/**
 * Controlador de Combos
 * Maneja el CRUD de combos con gestión de productos asociados
 * Sistema Napanchita
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Combo.php';
require_once __DIR__ . '/../models/Producto.php';

class ComboController
{
    private $db;
    private $combo;
    private $producto;
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
        $this->combo = new Combo($this->db);
        $this->producto = new Producto($this->db);
        $this->upload_dir = __DIR__ . '/../public/images/combos/';

        // Crear directorio si no existe
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    /**
     * Listar combos
     */
    public function index()
    {
        $combos = $this->combo->listar();
        require_once __DIR__ . '/../views/combos/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear()
    {
        $productos = $this->producto->listar(true); // Solo disponibles
        require_once __DIR__ . '/../views/combos/crear.php';
    }

    /**
     * Guardar nuevo combo
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('combos');
            return;
        }

        // Validar datos básicos
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $descripcion = sanitize_input($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? 0;
        $activo = isset($_POST['activo']) ? 1 : 0;
        $productos_combo = $_POST['productos'] ?? [];

        if (empty($nombre) || $precio <= 0) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('combos/crear');
            return;
        }

        if (empty($productos_combo)) {
            set_flash_message('Debe agregar al menos un producto al combo', 'error');
            redirect('combos/crear');
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
                redirect('combos/crear');
                return;
            }
        }

        // Asignar valores al combo
        $this->combo->nombre = $nombre;
        $this->combo->descripcion = $descripcion;
        $this->combo->precio = $precio;
        $this->combo->imagen_url = $imagen_url;
        $this->combo->activo = $activo;

        // Crear combo
        if ($this->combo->crear()) {
            $combo_id = $this->combo->id;

            // Agregar productos al combo
            $productos_agregados = true;
            foreach ($productos_combo as $producto_id => $cantidad) {
                if ($cantidad > 0) {
                    if (!$this->combo->agregarProducto($combo_id, $producto_id, $cantidad)) {
                        $productos_agregados = false;
                        break;
                    }
                }
            }

            if ($productos_agregados) {
                set_flash_message('Combo creado exitosamente', 'success');
                redirect('combos');
            } else {
                // Si falla agregar productos, eliminar combo
                $this->combo->id = $combo_id;
                $this->combo->eliminar();
                set_flash_message('Error al agregar productos al combo', 'error');
                redirect('combos/crear');
            }
        } else {
            // Eliminar imagen si falla la creación
            if ($imagen_url && file_exists($imagen_url)) {
                unlink($imagen_url);
            }
            set_flash_message('Error al crear el combo', 'error');
            redirect('combos/crear');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de combo no válido', 'error');
            redirect('combos');
            return;
        }

        $this->combo->id = $id;
        $combo = $this->combo->obtenerPorId();

        if (!$combo) {
            set_flash_message('Combo no encontrado', 'error');
            redirect('combos');
            return;
        }

        $productos = $this->producto->listar(true);
        require_once __DIR__ . '/../views/combos/editar.php';
    }

    /**
     * Actualizar combo
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('combos');
            return;
        }

        $id = $_POST['id'] ?? 0;
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $descripcion = sanitize_input($_POST['descripcion'] ?? '');
        $precio = $_POST['precio'] ?? 0;
        $activo = isset($_POST['activo']) ? 1 : 0;
        $productos_combo = $_POST['productos'] ?? [];

        if (!$id || empty($nombre) || $precio <= 0) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('combos');
            return;
        }

        if (empty($productos_combo)) {
            set_flash_message('Debe agregar al menos un producto al combo', 'error');
            redirect('combos/editar?id=' . $id);
            return;
        }

        // Obtener combo actual
        $this->combo->id = $id;
        $combo_actual = $this->combo->obtenerPorId();

        if (!$combo_actual) {
            set_flash_message('Combo no encontrado', 'error');
            redirect('combos');
            return;
        }

        $imagen_url = $combo_actual['imagen_url'];

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
                redirect('combos/editar?id=' . $id);
                return;
            }
        }

        // Asignar valores
        $this->combo->id = $id;
        $this->combo->nombre = $nombre;
        $this->combo->descripcion = $descripcion;
        $this->combo->precio = $precio;
        $this->combo->imagen_url = $imagen_url;
        $this->combo->activo = $activo;

        // Actualizar combo
        if ($this->combo->actualizar()) {
            // Actualizar productos del combo
            if ($this->combo->actualizarProductos($id, $productos_combo)) {
                set_flash_message('Combo actualizado exitosamente', 'success');
                redirect('combos');
            } else {
                set_flash_message('Error al actualizar productos del combo', 'error');
                redirect('combos/editar?id=' . $id);
            }
        } else {
            set_flash_message('Error al actualizar el combo', 'error');
            redirect('combos/editar?id=' . $id);
        }
    }

    /**
     * Cambiar estado de combo (AJAX)
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

        if ($this->combo->cambiarEstado($id, $estado)) {
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
     * Eliminar combo (AJAX)
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

        // Obtener combo para eliminar imagen
        $this->combo->id = $id;
        $combo = $this->combo->obtenerPorId();

        if (!$combo) {
            echo json_encode(['success' => false, 'message' => 'Combo no encontrado']);
            return;
        }

        // Eliminar (los productos se eliminan automáticamente por CASCADE)
        if ($this->combo->eliminar()) {
            echo json_encode([
                'success' => true,
                'message' => 'Combo eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el combo'
            ]);
        }
    }

    /**
     * Buscar combos (AJAX)
     */
    public function buscar()
    {
        header('Content-Type: application/json');

        $termino = sanitize_input($_GET['termino'] ?? '');

        if (empty($termino)) {
            echo json_encode([]);
            return;
        }

        $resultados = $this->combo->buscar($termino);
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
        $nombre_nuevo = uniqid('combo_') . '.' . $extension;
        $ruta_completa = $this->upload_dir . $nombre_nuevo;
        $ruta_relativa = 'public/images/combos/' . $nombre_nuevo;

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
