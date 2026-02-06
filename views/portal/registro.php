<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            overflow-y: auto;
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #00838f 0%, #00acc1 50%, #667eea 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        /* Elementos decorativos flotantes */
        .decorative-circle {
            position: fixed;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 2;
        }

        .decorative-circle-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
        }

        .decorative-circle-2 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.25) 0%, transparent 70%);
            bottom: -50px;
            left: -50px;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { 
                transform: translate(0, 0) scale(1);
                opacity: 0.6;
            }
            50% { 
                transform: translate(-30px, 30px) scale(1.1);
                opacity: 0.3;
            }
        }

        /* Burbujas decorativas */
        .bubble {
            position: fixed;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            pointer-events: none;
            animation: rise 15s ease-in infinite;
            z-index: 2;
        }

        .bubble:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            bottom: -100px;
            animation-delay: 0s;
        }

        .bubble:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 30%;
            bottom: -100px;
            animation-delay: 3s;
        }

        .bubble:nth-child(3) {
            width: 100px;
            height: 100px;
            left: 50%;
            bottom: -100px;
            animation-delay: 6s;
        }

        .bubble:nth-child(4) {
            width: 70px;
            height: 70px;
            left: 70%;
            bottom: -100px;
            animation-delay: 9s;
        }

        .bubble:nth-child(5) {
            width: 90px;
            height: 90px;
            left: 90%;
            bottom: -100px;
            animation-delay: 12s;
        }

        @keyframes rise {
            0% {
                bottom: -100px;
                opacity: 0;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                bottom: 110vh;
                opacity: 0;
            }
        }

        /* Patrón de olas decorativo */
        .wave-pattern {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%2300acc1" fill-opacity="0.3" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,165.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            animation: wave 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 2;
        }

        .wave-pattern::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23667eea" fill-opacity="0.25" d="M0,160L48,144C96,128,192,96,288,96C384,96,480,128,576,149.3C672,171,768,181,864,165.3C960,149,1056,107,1152,101.3C1248,96,1344,128,1392,144L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            animation: wave 25s ease-in-out infinite reverse;
        }

        @keyframes wave {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(-50px); }
        }

        .register-container {
            max-width: 950px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-height: 90vh;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            padding: 25px 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-header::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -50px;
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 0.5;
            }
            50% { 
                transform: scale(1.2);
                opacity: 0.3;
            }
        }

        .register-header .logo-container {
            background: white;
            padding: 15px 20px;
            border-radius: 15px;
            margin: 0 auto 15px;
            display: inline-block;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
        }

        .register-header .logo-container img {
            max-height: 60px;
            width: auto;
            display: block;
        }

        .register-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 40px;
            background: inherit;
            clip-path: ellipse(50% 100% at 50% 0%);
        }

        .register-header i {
            font-size: 40px;
            margin-bottom: 0;
        }

        .register-header h2 {
            margin: 10px 0 5px;
            font-weight: 700;
            font-size: 1.6rem;
        }

        .register-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 0.95rem;
        }

        .register-body {
            padding: 25px 20px;
        }

        /* Layout de dos columnas */
        .form-columns {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .form-fields {
            padding-right: 15px;
        }

        .form-actions {
            padding-left: 15px;
            border-left: 2px solid rgba(0, 172, 193, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 0;
        }

        .form-actions hr {
            border-color: rgba(0, 172, 193, 0.15);
            margin: 5px 0;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating label {
            color: #666;
            font-weight: 500;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 13px 20px;
            font-size: 0.95rem;
            transition: all 0.3s;
            height: calc(3.5rem + 2px);
        }

        .form-control:focus {
            border-color: #00acc1;
            box-shadow: 0 0 0 0.25rem rgba(0, 172, 193, 0.15);
        }

        .btn-register {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            border: none;
            padding: 15px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 131, 143, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 172, 193, 0.5);
            background: linear-gradient(135deg, #00acc1 0%, #00838f 100%);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            color: #c62828;
        }

        .alert-success {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #2e7d32;
        }

        .btn-back {
            background: transparent;
            border: 2px solid #00acc1;
            color: #00acc1;
            padding: 12px;
            font-weight: 600;
            border-radius: 12px;
            width: 100%;
            margin-top: 5px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back:hover {
            background: #00acc1;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 172, 193, 0.3);
        }

        .login-link {
            text-align: center;
            margin: 15px 0;
            padding: 12px;
            background: linear-gradient(135deg, rgba(0, 172, 193, 0.08) 0%, rgba(0, 131, 143, 0.08) 100%);
            border-radius: 12px;
            border: 2px dashed #00acc1;
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: -10px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
        }

        .fish-icon {
            animation: swim 4s ease-in-out infinite;
        }

        @keyframes swim {
            0%, 100% { transform: translateX(0) rotate(0deg); }
            25% { transform: translateX(10px) rotate(5deg); }
            75% { transform: translateX(-10px) rotate(-5deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-columns {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .form-actions {
                padding-left: 0;
                border-left: none;
                border-top: 2px solid rgba(0, 172, 193, 0.2);
                padding-top: 20px;
            }

            .form-fields {
                padding-right: 0;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .register-header {
                padding: 20px 15px;
            }

            .register-body {
                padding: 20px 15px;
            }

            .register-header h2 {
                font-size: 1.3rem;
            }

            .register-header p {
                font-size: 0.85rem;
            }

            .btn-register {
                font-size: 0.9rem;
                padding: 13px;
            }
        }

        @media (max-height: 750px) {
            body {
                padding: 10px;
            }

            .register-header {
                padding: 20px 15px;
            }

            .register-header .logo-container {
                padding: 12px 15px;
                margin-bottom: 10px;
            }

            .register-header .logo-container img {
                max-height: 50px;
            }
        }
    </style>
</head>
<body>
    <!-- Elementos decorativos flotantes -->
    <div class="decorative-circle decorative-circle-1"></div>
    <div class="decorative-circle decorative-circle-2"></div>

    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-container">
                    <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="<?php echo APP_NAME; ?>">
                </div>
                <h2>Crear Cuenta</h2>
                <p>Únete y disfruta de los mejores ceviches</p>
            </div>

            <div class="register-body">
                <?php if (isset($_SESSION['registro_errores'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Errores:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($_SESSION['registro_errores'] as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['registro_errores']); ?>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=registrar" method="POST" id="formRegistro">
                    <div class="form-columns">
                        <div class="form-fields">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Nombre completo" required
                                       value="<?php echo isset($_SESSION['registro_datos']['nombre']) ? htmlspecialchars($_SESSION['registro_datos']['nombre']) : ''; ?>">
                                <label for="nombre"><i class="fas fa-user me-2"></i>Nombre Completo</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       placeholder="Teléfono" required pattern="[0-9]{9}" maxlength="9"
                                       value="<?php echo isset($_SESSION['registro_datos']['telefono']) ? htmlspecialchars($_SESSION['registro_datos']['telefono']) : ''; ?>">
                                <label for="telefono"><i class="fas fa-phone me-2"></i>Teléfono (9 dígitos)</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Email" required
                                       value="<?php echo isset($_SESSION['registro_datos']['email']) ? htmlspecialchars($_SESSION['registro_datos']['email']) : ''; ?>">
                                <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Contraseña" required minlength="6">
                                <label for="password"><i class="fas fa-lock me-2"></i>Contraseña (mín. 6 caracteres)</label>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>

                        <div class="form-actions">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                       placeholder="Confirmar contraseña" required minlength="6">
                                <label for="password_confirm"><i class="fas fa-lock me-2"></i>Confirmar Contraseña</label>
                            </div>

                           


                            <button type="submit" class="btn btn-register">
                                <i class="fas fa-user-plus me-2"></i> Crear Mi Cuenta
                            </button>

                            <div class="login-link">
                                ¿Ya tienes cuenta? 
                                <a href="<?php echo BASE_URL; ?>login" class="text-decoration-none fw-bold">
                                    Inicia Sesión
                                </a>
                            </div>

                            <a href="<?php echo BASE_URL; ?>" class="btn btn-back">
                                <i class="fas fa-arrow-left me-2"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Elementos decorativos -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="wave-pattern"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Indicador de fuerza de contraseña
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('passwordStrength');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            strengthBar.style.width = (strength * 20) + '%';
            
            if (strength <= 1) {
                strengthBar.style.backgroundColor = '#dc3545';
            } else if (strength <= 3) {
                strengthBar.style.backgroundColor = '#ffc107';
            } else {
                strengthBar.style.backgroundColor = '#28a745';
            }
        });

        // Validar que las contraseñas coincidan
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
        });

        // Formatear teléfono a solo números
        document.getElementById('telefono').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>

    <?php 
    // Limpiar datos de sesión después de mostrarlos
    unset($_SESSION['registro_datos']); 
    ?>
</body>
</html>
