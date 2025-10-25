<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Napanchita</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>🍽️ Napanchita</h1>
                <h2>Crear Cuenta</h2>
            </div>
            
            <form id="registroForm" class="auth-form">
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Juan Pérez">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="tu@email.com">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" required placeholder="555-1234">
                </div>
                
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" required placeholder="Calle, número, zona"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                
                <div class="form-group">
                    <label for="password2">Confirmar Contraseña</label>
                    <input type="password" id="password2" name="password2" required placeholder="••••••••">
                </div>
                
                <div id="errorMessage" class="error-message" style="display: none;"></div>
                <div id="successMessage" class="success-message" style="display: none;"></div>
                
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </form>
            
            <div class="auth-footer">
                <p>¿Ya tienes cuenta? <a href="index.php?action=login">Inicia sesión</a></p>
                <p><a href="index.php">Volver al inicio</a></p>
            </div>
        </div>
    </div>

    <script src="public/js/auth.js"></script>
</body>
</html>
