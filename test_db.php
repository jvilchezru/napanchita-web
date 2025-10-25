<?php
echo "<h2>Test de Conexión a Base de Datos</h2>";

try {
    $conn = new PDO('mysql:host=localhost;dbname=napanchita_db', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión exitosa a la base de datos<br>";
    
    // Probar consulta
    $stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Usuarios en DB: " . $result['total'] . "<br>";
    
    // Probar login
    $email = 'admin@napanchita.com';
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Usuario admin encontrado<br>";
        echo "Nombre: " . $user['nombre'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Rol: " . $user['rol'] . "<br>";
    } else {
        echo "❌ Usuario admin NO encontrado<br>";
    }
    
} catch(PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>
