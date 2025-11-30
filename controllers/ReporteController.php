<?php

require_once __DIR__ . '/../models/Reporte.php';

class ReporteController
{
    private $reporte;

    public function __construct()
    {
        $this->reporte = new Reporte();
    }

    public function index()
    {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $metricas = $this->reporte->obtenerMetricasDashboard($fecha);
        
        include __DIR__ . '/../views/reportes/dashboard.php';
    }

    public function ventas()
    {
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-t');
        
        $ventasPorPeriodo = $this->reporte->obtenerVentasPorPeriodo($fecha_desde, $fecha_hasta, 'dia');
        $ventasPorCategoria = $this->reporte->obtenerVentasPorCategoria($fecha_desde, $fecha_hasta);

        include __DIR__ . '/../views/reportes/ventas.php';
    }

    public function platos()
    {
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-t');
        
        $platosMasVendidos = $this->reporte->obtenerPlatosMasVendidos($fecha_desde, $fecha_hasta, 20);

        include __DIR__ . '/../views/reportes/platos.php';
    }
}
