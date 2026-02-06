<?php
/**
 * Script de prueba - Verificar zonas de delivery
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ZonaDelivery.php';

echo "<h1>Prueba de Zonas de Delivery</h1>";

try {
    $zona = new ZonaDelivery();
    
    echo "<h2>Zonas Activas:</h2>";
    $zonasActivas = $zona->listar(true);
    
    if (empty($zonasActivas)) {
        echo "<p style='color: red;'>❌ NO hay zonas activas en la base de datos</p>";
    } else {
        echo "<p style='color: green;'>✓ Se encontraron " . count($zonasActivas) . " zonas activas</p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Costo</th><th>Tiempo (min)</th><th>Activo</th><th>Orden</th></tr>";
        foreach ($zonasActivas as $z) {
            echo "<tr>";
            echo "<td>" . $z['id'] . "</td>";
            echo "<td>" . $z['nombre'] . "</td>";
            echo "<td>" . $z['descripcion'] . "</td>";
            echo "<td>S/ " . number_format($z['costo_envio'], 2) . "</td>";
            echo "<td>" . $z['tiempo_estimado'] . "</td>";
            echo "<td>" . ($z['activo'] ? '✓' : '✗') . "</td>";
            echo "<td>" . $z['orden'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Todas las Zonas:</h2>";
    $todasZonas = $zona->listar(false);
    echo "<p>Total de zonas: " . count($todasZonas) . "</p>";
    
    echo "<hr>";
    echo "<h2>Test de Métodos de Pago:</h2>";
    require_once __DIR__ . '/../../models/MetodoPago.php';
    $metodoPago = new MetodoPago();
    $metodos = $metodoPago->listar();
    
    if (empty($metodos)) {
        echo "<p style='color: red;'>❌ NO hay métodos de pago en la base de datos</p>";
    } else {
        echo "<p style='color: green;'>✓ Se encontraron " . count($metodos) . " métodos de pago</p>";
        echo "<ul>";
        foreach ($metodos as $m) {
            echo "<li>" . $m['nombre'] . " - " . ($m['activo'] ? 'Activo' : 'Inactivo') . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='../../index.php?controller=Portal&action=checkout'>← Ir al Checkout</a></p>";
?>
