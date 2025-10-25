<?php
/**
 * Script de prueba de conexión a la base de datos
 * Ejecutar desde el navegador: http://localhost:8000/test_conexion.php
 */

echo "<h1>🔧 Prueba de Conexión - Napanchita</h1>";
echo "<hr>";

// Test 1: Versión de PHP
echo "<h2>1. Versión de PHP</h2>";
echo "Versión actual: <strong>" . phpversion() . "</strong><br>";
if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "✅ PHP versión compatible<br>";
} else {
    echo "❌ Se requiere PHP 7.4 o superior<br>";
}
echo "<hr>";

// Test 2: Extensiones requeridas
echo "<h2>2. Extensiones PHP</h2>";
$extensiones = ['pdo', 'pdo_mysql', 'json'];
foreach ($extensiones as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extensión $ext: <strong>Habilitada</strong><br>";
    } else {
        echo "❌ Extensión $ext: <strong>NO habilitada</strong><br>";
    }
}
echo "<hr>";

// Test 3: Conexión a la base de datos
echo "<h2>3. Conexión a Base de Datos</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "✅ <strong>Conexión exitosa a la base de datos</strong><br><br>";
        
        // Test 4: Verificar tablas
        echo "<h2>4. Verificación de Tablas</h2>";
        $tablas = ['usuarios', 'productos', 'categorias', 'pedidos', 'detalles_pedidos'];
        
        foreach ($tablas as $tabla) {
            $query = "SHOW TABLES LIKE :tabla";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':tabla', $tabla);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Contar registros
                $count_query = "SELECT COUNT(*) as total FROM " . $tabla;
                $count_stmt = $conn->prepare($count_query);
                $count_stmt->execute();
                $count = $count_stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "✅ Tabla <strong>$tabla</strong>: Existe (" . $count['total'] . " registros)<br>";
            } else {
                echo "❌ Tabla <strong>$tabla</strong>: NO existe<br>";
            }
        }
        
        echo "<hr>";
        
        // Test 5: Verificar datos de prueba
        echo "<h2>5. Datos de Prueba</h2>";
        
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $admins = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Administradores: <strong>" . $admins['total'] . "</strong><br>";
        
        $query = "SELECT COUNT(*) as total FROM productos WHERE disponible = TRUE";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $productos = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Productos disponibles: <strong>" . $productos['total'] . "</strong><br>";
        
        if ($admins['total'] > 0 && $productos['total'] > 0) {
            echo "<br>✅ <strong>Sistema listo para usar</strong><br>";
        } else {
            echo "<br>⚠️ Faltan datos de prueba. Ejecuta el script database/schema.sql<br>";
        }
        
    } else {
        echo "❌ <strong>Error al conectar con la base de datos</strong><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
    echo "<br>Verifica:<br>";
    echo "1. MySQL está ejecutándose<br>";
    echo "2. Las credenciales en config/database.php son correctas<br>";
    echo "3. La base de datos 'napanchita_db' existe<br>";
}

echo "<hr>";
echo "<h2>6. Siguiente Paso</h2>";
echo "<p>Si todas las pruebas pasaron correctamente, puedes acceder a:</p>";
echo "<ul>";
echo "<li><a href='index.php'><strong>Página Principal</strong></a></li>";
echo "<li><a href='index.php?action=login'><strong>Iniciar Sesión</strong></a></li>";
echo "<li><a href='index.php?action=registro'><strong>Registrarse</strong></a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Script de prueba - Puede eliminar este archivo en producción</small></p>";
?>
