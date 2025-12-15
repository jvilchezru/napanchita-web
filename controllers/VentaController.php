<?php

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Pedido.php';

class VentaController
{
    private $venta;
    private $pedido;
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

        $this->venta = new Venta();
        $this->pedido = new Pedido($this->db);
    }

    public function index()
    {
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? date('Y-m-d'),
            'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d'),
            'metodo_pago_id' => $_GET['metodo_pago_id'] ?? ''
        ];

        $ventas = $this->venta->listar($filtros);
        $estadisticas = $this->venta->obtenerEstadisticas($filtros['fecha_desde'], $filtros['fecha_hasta']);
        $totalesPorMetodo = $this->venta->obtenerTotalesPorMetodoPago($filtros['fecha_desde'], $filtros['fecha_hasta']);

        // Obtener métodos de pago para el filtro
        require_once __DIR__ . '/../models/MetodoPago.php';
        $metodoPagoModel = new MetodoPago();
        $metodosPago = $metodoPagoModel->listar();

        include __DIR__ . '/../views/ventas/index.php';
    }

    public function registrar()
    {
        $pedido_id = $_GET['pedido_id'] ?? null;
        $pedido = null;

        if ($pedido_id) {
            $pedido = $this->pedido->obtenerPorId($pedido_id);
        }

        // Obtener métodos de pago
        require_once __DIR__ . '/../models/MetodoPago.php';
        $metodoPagoModel = new MetodoPago();
        $metodosPago = $metodoPagoModel->listar();

        include __DIR__ . '/../views/ventas/registrar.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=ventas');
            return;
        }

        $this->venta->pedido_id = $_POST['pedido_id'] ?? null;
        $this->venta->total = $_POST['total'] ?? 0;
        $this->venta->metodo_pago_id = $_POST['metodo_pago_id'] ?? null;
        $this->venta->monto_recibido = $_POST['monto_recibido'] ?? 0;
        $this->venta->monto_cambio = $_POST['monto_cambio'] ?? 0;
        $this->venta->descuento_aplicado = $_POST['descuento'] ?? 0;
        $this->venta->codigo_descuento = $_POST['codigo_descuento'] ?? null;
        $this->venta->usuario_id = $_SESSION['user_id'];

        $resultado = $this->venta->crear();

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=ventas');
    }

    public function cierreCaja()
    {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');

        $ventas = $this->venta->obtenerVentasDelDia($fecha);
        $totalesPorMetodo = $this->venta->obtenerTotalesPorMetodoPago($fecha, $fecha);
        $estadisticas = $this->venta->obtenerEstadisticas($fecha, $fecha);

        include __DIR__ . '/../views/ventas/cierre_caja.php';
    }

    public function ver()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            redirect('index.php?action=ventas');
            return;
        }

        // Obtener información de la venta
        $venta = $this->venta->obtenerPorId($id);

        if (!$venta) {
            $_SESSION['error'] = 'Venta no encontrada';
            redirect('index.php?action=ventas');
            return;
        }

        // Obtener el pedido asociado con sus items
        $pedido = $this->pedido->obtenerPorId($venta['pedido_id']);

        include __DIR__ . '/../views/ventas/ver.php';
    }

    public function imprimir()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            redirect('index.php?action=ventas');
            return;
        }

        // Obtener información de la venta
        $venta = $this->venta->obtenerPorId($id);

        if (!$venta) {
            echo '<script>alert("Venta no encontrada"); window.close();</script>';
            return;
        }

        // Obtener el pedido asociado con sus items
        $pedido = $this->pedido->obtenerPorId($venta['pedido_id']);

        include __DIR__ . '/../views/ventas/imprimir.php';
    }
}
