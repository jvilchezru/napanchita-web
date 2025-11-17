<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema Napanchita</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            min-height: 100vh;
            color: white;
        }

        .hero-section {
            padding: 100px 0;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .btn-access {
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            background: white;
            color: #17a2b8;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .btn-access:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            color: #00bcd4;
        }

        .footer {
            text-align: center;
            padding: 30px;
            margin-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <i class="fas fa-utensils fa-5x mb-4"></i>
            <h1>Cevichería Ñapanchita</h1>

            <a href="<?php echo BASE_URL; ?>index.php?action=login" class="btn btn-access">
                <i class="fas fa-sign-in-alt me-2"></i> Acceder al Sistema
            </a>
        </div>

        <!-- Features -->
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Gestión de Pedidos</h3>
                    <p>Control completo de pedidos para salón y delivery con seguimiento en tiempo real</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-table"></i>
                    </div>
                    <h3>Mesas y Reservas</h3>
                    <p>Administra mesas, reservas y optimiza la ocupación de tu restaurante</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Sistema de Delivery</h3>
                    <p>Seguimiento de entregas, asignación de repartidores y gestión de zonas</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Reportes y Estadísticas</h3>
                    <p>Análisis de ventas, productos más vendidos y rendimiento del negocio</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Gestión de Clientes</h3>
                    <p>Base de datos de clientes frecuentes con historial de pedidos</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>Productos y Combos</h3>
                    <p>Catálogo digital de productos, categorías y combos promocionales</p>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="text-center mt-5">
            <div class="feature-card">
                <h3><i class="fas fa-shield-alt me-2"></i> Sistema Seguro y Confiable</h3>
                <p class="mb-0">
                    Acceso basado en roles (Administrador, Mesero, Repartidor)<br>
                    Cifrado de contraseñas | Control de sesiones | Logs de actividad
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="mb-2">
                <strong>Cevichería Ñapanchita v<?php echo APP_VERSION; ?></strong>
            </p>
            <p class="mb-0">
                <small>
                    © <?php echo date('Y'); ?> Todos los derechos reservados<br>
                    Desarrollado para optimizar la gestión de cevicherías
                </small>
            </p>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.feature-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>