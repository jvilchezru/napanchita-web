<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .login-container {
            max-width: 950px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .login-card {
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

        .login-header {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            padding: 25px 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
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

        .logo-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: inline-block;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 1;
        }

        .logo-container img {
            max-width: 180px;
            height: auto;
            display: block;
        }

        .login-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .login-body {
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
            justify-content: center;
            gap: 10px;
        }

        .nav-tabs {
            border-bottom: 3px solid #e0f7fa;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .nav-tabs .nav-link {
            color: #666;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 14px 30px;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #00acc1;
            background: rgba(0, 172, 193, 0.05);
        }

        .nav-tabs .nav-link.active {
            color: #00acc1;
            background: transparent;
            border-color: #00acc1;
        }

        .form-label {
            font-weight: 600;
            color: #00838f;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            border: 2px solid #b2ebf2;
            border-right: none;
            color: #00838f;
            font-size: 1.1rem;
            min-width: 50px;
            justify-content: center;
        }

        .form-control {
            border: 2px solid #b2ebf2;
            border-left: none;
            padding: 12px 15px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #00acc1;
            box-shadow: 0 0 0 0.2rem rgba(0, 172, 193, 0.15);
        }

        .input-group:focus-within .input-group-text {
            border-color: #00acc1;
        }

        .btn-login {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            border: none;
            padding: 15px;
            font-size: 1rem;
            font-weight: 700;
            color: white;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 131, 143, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 172, 193, 0.5);
            background: linear-gradient(135deg, #00acc1 0%, #00838f 100%);
        }

        .btn-login:active {
            transform: translateY(0);
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
        }

        .btn-back:hover {
            background: #00acc1;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 172, 193, 0.3);
        }

        .register-link {
            text-align: center;
            margin-top: 5px;
            padding: 18px;
            background: linear-gradient(135deg, rgba(0, 172, 193, 0.08) 0%, rgba(0, 131, 143, 0.08) 100%);
            border-radius: 15px;
            border: 2px dashed #00acc1;
            color: #555;
            font-weight: 500;
        }

        .register-link a {
            color: #00acc1;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.3s;
            display: inline-block;
        }

        .register-link a:hover {
            color: #00838f;
            text-decoration: none;
            transform: translateX(5px);
        }

        .register-link a i {
            transition: transform 0.3s;
        }

        .register-link a:hover i {
            transform: scale(1.2) rotate(10deg);
        }

        .alert {
            border-radius: 12px;
            margin-bottom: 20px;
            border: none;
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

        .alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1565c0;
        }

        .tab-icon {
            font-size: 1.2rem;
            margin-right: 8px;
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

            .login-header {
                padding: 20px 15px;
            }

            .login-body {
                padding: 20px 15px;
            }

            .logo-container img {
                max-width: 150px;
            }

            .nav-tabs .nav-link {
                padding: 12px 20px;
                font-size: 0.9rem;
            }

            .btn-login {
                font-size: 0.9rem;
                padding: 13px;
            }
        }

        @media (max-height: 750px) {
            body {
                padding: 10px;
            }

            .login-header {
                padding: 20px 15px;
            }

            .logo-container {
                padding: 15px;
                margin-bottom: 15px;
            }

            .logo-container img {
                max-width: 140px;
            }
        }
    </style>
</head>
<body>
    <!-- Elementos decorativos flotantes -->
    <div class="decorative-circle decorative-circle-1"></div>
    <div class="decorative-circle decorative-circle-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="<?php echo APP_NAME; ?>">
                </div>
                <h4>Bienvenido</h4>
            </div>

            <div class="login-body">
                <!-- Mensajes -->
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php 
                        echo $_SESSION['login_error']; 
                        unset($_SESSION['login_error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['mensaje_exito'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php 
                        echo $_SESSION['mensaje_exito']; 
                        unset($_SESSION['mensaje_exito']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['mensaje_info'])): ?>
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php 
                        echo $_SESSION['mensaje_info']; 
                        unset($_SESSION['mensaje_info']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cliente-tab" data-bs-toggle="tab" data-bs-target="#cliente" type="button">
                            <i class="fas fa-user tab-icon"></i>Cliente
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button">
                            <i class="fas fa-user-shield tab-icon"></i>Personal
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Login Cliente -->
                    <div class="tab-pane fade show active" id="cliente" role="tabpanel">
                        <form action="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=login" method="POST">
                            <div class="form-columns">
                                <div class="form-fields">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" name="email" class="form-control" placeholder="tucorreo@ejemplo.com" required autofocus>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-login">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>

                                    <div class="register-link">
                                        ¿No tienes cuenta? 
                                        <a href="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=mostrarRegistro">
                                            <i class="fas fa-user-plus me-1"></i>Regístrate aquí
                                        </a>
                                    </div>

                                    <a href="<?php echo BASE_URL; ?>" class="btn btn-back">
                                        <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Login Staff -->
                    <div class="tab-pane fade" id="staff" role="tabpanel">
                        <form action="<?php echo BASE_URL; ?>index.php?action=procesarLogin" method="POST">
                            <div class="form-columns">
                                <div class="form-fields">
                                    <div class="mb-3">
                                        <label class="form-label">Usuario</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-shield"></i>
                                            </span>
                                            <input type="text" name="usuario" class="form-control" placeholder="usuario" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-login">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>

                                    <div class="register-link">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Acceso solo para personal autorizado
                                        </small>
                                    </div>

                                    <a href="<?php echo BASE_URL; ?>" class="btn btn-back">
                                        <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
