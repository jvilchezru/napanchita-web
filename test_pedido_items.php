<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Debug: Estructura y datos de pedido_items</h2>";

// Ver la estructura de pedido_items
echo "<h3>Estructura de la tabla pedido_items:</h3>";
$stmt = $db->query("DESCRIBE pedido_items");
$estructura = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($estructura);
echo "</pre>";

// Ver algunos registros de pedido_items
echo "<h3>Primeros 10 registros de pedido_items:</h3>";
$stmt = $db->query("SELECT * FROM pedido_items LIMIT 10");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($items);
echo "</pre>";

// Verificar si hay plato_id o combo_id
echo "<h3>Análisis de items:</h3>";
$stmt = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN plato_id IS NOT NULL THEN 1 ELSE 0 END) as con_plato,
        SUM(CASE WHEN combo_id IS NOT NULL THEN 1 ELSE 0 END) as con_combo
    FROM pedido_items
");
$analisis = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($analisis);
echo "</pre>";

// Ver qué platos están en pedido_items
echo "<h3>Platos referenciados en pedido_items:</h3>";
$stmt = $db->query("
    SELECT DISTINCT pi.plato_id, pl.nombre as plato_nombre, pl.categoria_id, c.nombre as categoria_nombre
    FROM pedido_items pi
    LEFT JOIN platos pl ON pi.plato_id = pl.id
    LEFT JOIN categorias c ON pl.categoria_id = c.id
    WHERE pi.plato_id IS NOT NULL
");
$platosRef = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($platosRef);
echo "</pre>";

// Ver si hay combos en pedido_items
echo "<h3>Combos referenciados en pedido_items:</h3>";
$stmt = $db->query("
    SELECT DISTINCT pi.combo_id, co.nombre as combo_nombre
    FROM pedido_items pi
    LEFT JOIN combos co ON pi.combo_id = co.id
    WHERE pi.combo_id IS NOT NULL
");
$combosRef = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($combosRef);
echo "</pre>";

// Hacer el JOIN completo para ver dónde falla
echo "<h3>JOIN completo de pedido_items con pedidos:</h3>";
$stmt = $db->query("
    SELECT 
        pi.id,
        pi.pedido_id,
        pi.plato_id,
        pi.combo_id,
        pi.cantidad,
        pi.subtotal,
        p.fecha_pedido,
        p.estado,
        pl.nombre as plato_nombre,
        pl.categoria_id,
        c.nombre as categoria_nombre
    FROM pedido_items pi
    INNER JOIN pedidos p ON pi.pedido_id = p.id
    LEFT JOIN platos pl ON pi.plato_id = pl.id
    LEFT JOIN categorias c ON pl.categoria_id = c.id
    WHERE p.estado != 'Cancelado'
    LIMIT 10
");
$joinCompleto = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($joinCompleto);
echo "</pre>";
