<?php

require_once __DIR__ . '/../models/Cliente.php';

/**
 * Controlador de Clientes
 * Gestiona operaciones CRUD de clientes externos
 */
class ClienteController
{
    private $cliente;

    public function __construct()
    {
        $this->cliente = new Cliente();
    }

    /**
     * Mostrar listado de clientes
     */
    public function index()
    {
        $clientes = $this->cliente->listar(false); // Mostrar todos
        require_once __DIR__ . '/../views/clientes/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear()
    {
        require_once __DIR__ . '/../views/clientes/crear.php';
    }

    /**
     * Guardar nuevo cliente
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=clientes');
            return;
        }

        // Validar datos
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $notas = sanitize_input($_POST['notas'] ?? '');
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;

        // Validaciones
        if (empty($nombre) || empty($telefono)) {
            set_flash_message('El nombre y teléfono son obligatorios', 'error');
            redirect('index.php?action=clientes_crear');
            return;
        }

        // Validar que el teléfono no exista
        if ($this->cliente->obtenerPorTelefono($telefono)) {
            set_flash_message('Ya existe un cliente con ese número de teléfono', 'error');
            redirect('index.php?action=clientes_crear');
            return;
        }

        // Procesar direcciones si se enviaron
        $direcciones = [];
        if (isset($_POST['direccion']) && !empty($_POST['direccion'])) {
            $direcciones[] = [
                'id' => 1,
                'direccion' => sanitize_input($_POST['direccion']),
                'referencia' => sanitize_input($_POST['referencia'] ?? ''),
                'principal' => true
            ];
        }

        // Asignar valores
        $this->cliente->nombre = $nombre;
        $this->cliente->telefono = $telefono;
        $this->cliente->email = $email ?: null;
        $this->cliente->notas = $notas ?: null;
        $this->cliente->direcciones = $direcciones;
        $this->cliente->activo = $activo;

        // Crear cliente
        if ($this->cliente->crear()) {
            set_flash_message('Cliente creado exitosamente', 'success');
            redirect('index.php?action=clientes');
        } else {
            set_flash_message('Error al crear el cliente', 'error');
            redirect('index.php?action=clientes_crear');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de cliente no válido', 'error');
            redirect('index.php?action=clientes');
            return;
        }

        $this->cliente->id = $id;
        $cliente = $this->cliente->obtenerPorId();

        if (!$cliente) {
            set_flash_message('Cliente no encontrado', 'error');
            redirect('index.php?action=clientes');
            return;
        }

        require_once __DIR__ . '/../views/clientes/editar.php';
    }

    /**
     * Actualizar cliente existente
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=clientes');
            return;
        }

        $id = $_POST['id'] ?? 0;
        $nombre = sanitize_input($_POST['nombre'] ?? '');
        $telefono = sanitize_input($_POST['telefono'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $notas = sanitize_input($_POST['notas'] ?? '');
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;

        if (!$id || empty($nombre) || empty($telefono)) {
            set_flash_message('Datos incompletos o inválidos', 'error');
            redirect('index.php?action=clientes');
            return;
        }

        // Verificar que el cliente existe
        $this->cliente->id = $id;
        $cliente_actual = $this->cliente->obtenerPorId();

        if (!$cliente_actual) {
            set_flash_message('Cliente no encontrado', 'error');
            redirect('index.php?action=clientes');
            return;
        }

        // Verificar que el teléfono no esté en uso por otro cliente
        $cliente_con_telefono = $this->cliente->obtenerPorTelefono($telefono);
        if ($cliente_con_telefono && $cliente_con_telefono['id'] != $id) {
            set_flash_message('Ya existe otro cliente con ese número de teléfono', 'error');
            redirect('index.php?action=clientes_editar&id=' . $id);
            return;
        }

        // Procesar direcciones
        $direcciones = $cliente_actual['direcciones'] ?? [];

        // Si se envió una nueva dirección o actualización
        if (isset($_POST['direccion']) && !empty($_POST['direccion'])) {
            if (empty($direcciones)) {
                $direcciones[] = [
                    'id' => 1,
                    'direccion' => sanitize_input($_POST['direccion']),
                    'referencia' => sanitize_input($_POST['referencia'] ?? ''),
                    'principal' => true
                ];
            } else {
                // Actualizar la dirección principal
                foreach ($direcciones as &$dir) {
                    if (isset($dir['principal']) && $dir['principal']) {
                        $dir['direccion'] = sanitize_input($_POST['direccion']);
                        $dir['referencia'] = sanitize_input($_POST['referencia'] ?? '');
                        break;
                    }
                }
            }
        }

        // Asignar valores
        $this->cliente->id = $id;
        $this->cliente->nombre = $nombre;
        $this->cliente->telefono = $telefono;
        $this->cliente->email = $email ?: null;
        $this->cliente->notas = $notas ?: null;
        $this->cliente->direcciones = $direcciones;
        $this->cliente->activo = $activo;

        // Actualizar cliente
        if ($this->cliente->actualizar()) {
            set_flash_message('Cliente actualizado exitosamente', 'success');
            redirect('index.php?action=clientes');
        } else {
            set_flash_message('Error al actualizar el cliente', 'error');
            redirect('index.php?action=clientes_editar&id=' . $id);
        }
    }

    /**
     * Eliminar cliente (desactivar)
     */
    public function eliminar()
    {
        $id = $_GET['id'] ?? 0;

        if (!$id) {
            set_flash_message('ID de cliente no válido', 'error');
            redirect('index.php?action=clientes');
            return;
        }

        $this->cliente->id = $id;

        if ($this->cliente->eliminar()) {
            set_flash_message('Cliente desactivado correctamente', 'success');
        } else {
            set_flash_message('Error al desactivar el cliente', 'error');
        }

        redirect('index.php?action=clientes');
    }

    /**
     * Buscar clientes (AJAX)
     */
    public function buscar()
    {
        header('Content-Type: application/json');

        $termino = $_GET['q'] ?? '';

        if (empty($termino)) {
            echo json_encode([]);
            return;
        }

        $stmt = $this->cliente->buscar($termino);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($resultados);
    }
}
