<?php

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Pedido.php';

class VentaController
{
    private $venta;
    private $pedido;

    public function __construct()
    {
        $this->venta = new Venta();
        $this->pedido = new Pedido();
    }

    public function index()
    {
        $filtros = [
            'fecha_desde' => $_GET['fecha_desde'] ?? date('Y-m-d'),
            'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d'),
            'metodo_pago' => $_GET['metodo_pago'] ?? ''
        ];

        $ventas = $this->venta->listar($filtros);
        $estadisticas = $this->venta->obtenerEstadisticas($filtros['fecha_desde'], $filtros['fecha_hasta']);
        $totalesPorMetodo = $this->venta->obtenerTotalesPorMetodoPago($filtros['fecha_desde'], $filtros['fecha_hasta']);

        include __DIR__ . '/../views/ventas/index.php';
    }

    public function registrar()
    {
        $pedido_id = $_GET['pedido_id'] ?? null;
        $pedido = null;

        if ($pedido_id) {
            $pedido = $this->pedido->obtenerPorId($pedido_id);
        }

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
        $this->venta->metodo_pago = $_POST['metodo_pago'] ?? '';
        $this->venta->monto_recibido = $_POST['monto_recibido'] ?? 0;
        $this->venta->monto_cambio = $_POST['monto_cambio'] ?? 0;
        $this->venta->descuento = $_POST['descuento'] ?? 0;
        $this->venta->comprobante_tipo = $_POST['comprobante_tipo'] ?? 'Boleta';
        $this->venta->comprobante_numero = $_POST['comprobante_numero'] ?? '';
        $this->venta->notas = $_POST['notas'] ?? '';
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
}
