<?php
require_once 'config/database.php';
require_once 'models/Reporte.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Debug: Ventas por Categoría</h2>";

// Verificar si hay categorías
$stmtCat = $db->query("SELECT * FROM categorias");
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Categorías en BD: " . count($categorias) . "</h3>";
echo "<pre>";
print_r($categorias);
echo "</pre>";

// Verificar si hay platos
$stmtPlatos = $db->query("SELECT COUNT(*) as total FROM platos");
$totalPlatos = $stmtPlatos->fetch(PDO::FETCH_ASSOC);
echo "<h3>Total de platos: " . $totalPlatos['total'] . "</h3>";

// Verificar si hay pedidos
$stmtPedidos = $db->query("SELECT COUNT(*) as total FROM pedidos WHERE estado != 'Cancelado'");
$totalPedidos = $stmtPedidos->fetch(PDO::FETCH_ASSOC);
echo "<h3>Total de pedidos activos: " . $totalPedidos['total'] . "</h3>";

// Verificar pedidos con fechas
$stmtPedidosFechas = $db->query("SELECT DATE(fecha_pedido) as fecha, COUNT(*) as total FROM pedidos WHERE estado != 'Cancelado' GROUP BY DATE(fecha_pedido) ORDER BY fecha DESC LIMIT 5");
$pedidosFechas = $stmtPedidosFechas->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Últimos 5 días con pedidos:</h3>";
echo "<pre>";
print_r($pedidosFechas);
echo "</pre>";

// Verificar pedido_items
$stmtItems = $db->query("SELECT COUNT(*) as total FROM pedido_items");
$totalItems = $stmtItems->fetch(PDO::FETCH_ASSOC);
echo "<h3>Total de items en pedidos: " . $totalItems['total'] . "</h3>";

// Probar la consulta original con diferentes fechas
echo "<hr>";
echo "<h3>Prueba con diferentes rangos de fechas:</h3>";

$reporte = new Reporte($db);

// Probar con el mes actual
$desde = date('Y-m-01');
$hasta = date('Y-m-t');
echo "<h4>Mes actual ($desde a $hasta):</h4>";
$resultado = $reporte->obtenerVentasPorCategoria($desde, $hasta);
echo "<pre>";
print_r($resultado);
echo "</pre>";

// Probar con todo el año
$desde = date('Y-01-01');
$hasta = date('Y-12-31');
echo "<h4>Todo el año ($desde a $hasta):</h4>";
$resultado = $reporte->obtenerVentasPorCategoria($desde, $hasta);
echo "<pre>";
print_r($resultado);
echo "</pre>";

// Probar con un rango amplio
$desde = '2020-01-01';
$hasta = '2030-12-31';
echo "<h4>Rango amplio ($desde a $hasta):</h4>";
$resultado = $reporte->obtenerVentasPorCategoria($desde, $hasta);
echo "<pre>";
print_r($resultado);
echo "</pre>";

// Prueba de consulta directa sin filtros
echo "<hr>";
echo "<h3>Consulta directa de ventas por categoría (sin filtros de fecha):</h3>";
$query = "SELECT 
            c.id,
            c.nombre as categoria,
            COUNT(DISTINCT pi.id) as cantidad_vendida,
            COALESCE(SUM(pi.subtotal), 0) as total_ingresos
        FROM categorias c
        LEFT JOIN platos pl ON c.id = pl.categoria_id
        LEFT JOIN pedido_items pi ON pl.id = pi.plato_id
        LEFT JOIN pedidos p ON pi.pedido_id = p.id AND p.estado != 'Cancelado'
        GROUP BY c.id, c.nombre
        ORDER BY total_ingresos DESC";
$stmt = $db->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($resultado);
echo "</pre>";
