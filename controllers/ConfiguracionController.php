<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Configuracion.php';
require_once __DIR__ . '/AuthController.php';

class ConfiguracionController
{
    private $db;
    private $configuracion;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->configuracion = new Configuracion($this->db);
    }

    /**
     * Mostrar página de configuración
     */
    public function index()
    {
        AuthController::verificarRol([ROL_ADMIN]);

        // Obtener todas las configuraciones
        $configuraciones = $this->configuracion->obtenerTodas();

        // Convertir array de configuraciones a array asociativo para facilitar acceso
        $config = [];
        foreach ($configuraciones as $conf) {
            $config[$conf['clave']] = $conf['valor'];
        }

        require_once __DIR__ . '/../views/configuracion/index.php';
    }

    /**
     * Guardar configuraciones
     */
    public function guardar()
    {
        AuthController::verificarRol([ROL_ADMIN]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'index.php?action=configuracion');
            exit;
        }

        try {
            $this->db->beginTransaction();

            // Configuraciones generales
            if (isset($_POST['nombre_restaurante'])) {
                $this->configuracion->actualizar('nombre_restaurante', $_POST['nombre_restaurante']);
            }
            if (isset($_POST['telefono'])) {
                $this->configuracion->actualizar('telefono', $_POST['telefono']);
            }
            if (isset($_POST['direccion'])) {
                $this->configuracion->actualizar('direccion', $_POST['direccion']);
            }
            if (isset($_POST['email'])) {
                $this->configuracion->actualizar('email', $_POST['email']);
            }
            if (isset($_POST['ruc'])) {
                $this->configuracion->actualizar('ruc', $_POST['ruc']);
            }

            // Configuraciones de delivery
            if (isset($_POST['costo_delivery'])) {
                $this->configuracion->actualizar('costo_delivery', $_POST['costo_delivery']);
            }
            if (isset($_POST['monto_minimo_delivery'])) {
                $this->configuracion->actualizar('monto_minimo_delivery', $_POST['monto_minimo_delivery']);
            }
            if (isset($_POST['tiempo_preparacion'])) {
                $this->configuracion->actualizar('tiempo_preparacion', $_POST['tiempo_preparacion']);
            }

            // Configuraciones de reservas
            if (isset($_POST['tiempo_max_reserva'])) {
                $this->configuracion->actualizar('tiempo_max_reserva', $_POST['tiempo_max_reserva']);
            }
            if (isset($_POST['anticipacion_minima_reserva'])) {
                $this->configuracion->actualizar('anticipacion_minima_reserva', $_POST['anticipacion_minima_reserva']);
            }

            // Configuraciones de impuestos
            if (isset($_POST['igv'])) {
                $this->configuracion->actualizar('igv', $_POST['igv']);
            }
            if (isset($_POST['aplicar_igv'])) {
                $this->configuracion->actualizar('aplicar_igv', $_POST['aplicar_igv']);
            } else {
                $this->configuracion->actualizar('aplicar_igv', '0');
            }

            // Configuraciones del sistema
            if (isset($_POST['modo_mantenimiento'])) {
                $this->configuracion->actualizar('modo_mantenimiento', $_POST['modo_mantenimiento']);
            } else {
                $this->configuracion->actualizar('modo_mantenimiento', '0');
            }

            $this->db->commit();

            $_SESSION['mensaje'] = 'Configuración actualizada correctamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['mensaje'] = 'Error al actualizar configuración: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'error';
        }

        header('Location: ' . BASE_URL . 'index.php?action=configuracion');
        exit;
    }

    /**
     * Subir logo
     */
    public function subirLogo()
    {
        AuthController::verificarRol([ROL_ADMIN]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No se recibió el archivo']);
            exit;
        }

        try {
            $file = $_FILES['logo'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            // Validar tipo de archivo
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Solo se permiten imágenes JPG, PNG o GIF');
            }

            // Validar tamaño
            if ($file['size'] > $maxSize) {
                throw new Exception('El archivo no debe superar 2MB');
            }

            // Crear directorio si no existe
            $uploadDir = __DIR__ . '/../public/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'logo.' . $extension;
            $filepath = $uploadDir . $filename;

            // Eliminar logo anterior si existe
            $oldFiles = glob($uploadDir . 'logo.*');
            foreach ($oldFiles as $oldFile) {
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Mover archivo
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Error al guardar el archivo');
            }

            // Actualizar configuración
            $this->configuracion->actualizar('logo', $filename);

            echo json_encode([
                'success' => true,
                'message' => 'Logo actualizado correctamente',
                'filename' => $filename
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
