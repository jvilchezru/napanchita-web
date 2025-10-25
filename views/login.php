<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Napanchita</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>🍽️ Napanchita</h1>
                <h2>Iniciar Sesión</h2>
            </div>
            
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="error-message" style="display: block;">
                    <?php 
                    echo $_SESSION['login_error']; 
                    unset($_SESSION['login_error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" class="auth-form" method="POST" action="index.php?action=login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="tu@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                
                <div id="errorMessage" class="error-message" style="display: none;"></div>
                
                <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
            </form>
            
            <div class="auth-footer">
                <p>¿No tienes cuenta? <a href="index.php?action=registro">Regístrate aquí</a></p>
                <p><a href="index.php">Volver al inicio</a></p>
            </div>
            
            <div class="demo-credentials">
                <small><strong>Credenciales de prueba:</strong></small><br>
                <small>Admin: admin@napanchita.com / password</small><br>
                <small>Cliente: juan@email.com / password</small>
            </div>
        </div>
    </div>

    <script src="public/js/auth.js"></script>
</body>
</html>
