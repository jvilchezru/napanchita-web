<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header i {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }

        .form-control:focus+.input-group-text {
            border-color: #667eea;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="Logo Cevichería Ñapanchita" style="max-width: 400px; margin-bottom: 15px;">
            </div>

            <div class="login-body">
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $_SESSION['login_error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form action="index.php?action=procesarLogin" method="POST" id="loginForm">
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i> Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="correo@ejemplo.com"
                                required
                                autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i> Contraseña
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required>
                            <button class="btn btn-outline-secondary"
                                type="button"
                                id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                    </button>
                </form>


            </div>
        </div>

        <div class="text-center mt-4">
            <small class="text-white">
                © <?php echo date('Y'); ?> Cevichería Ñapanchita v<?php echo APP_VERSION; ?> - Todos los derechos reservados
            </small>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Validación del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Por favor complete todos los campos');
                return false;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor ingrese un email válido');
                return false;
            }
        });
    </script>
</body>

</html>