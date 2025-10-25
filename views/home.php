<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Napanchita - Restaurante Delivery</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <h1>🍽️ Napanchita</h1>
                </div>
                <button class="nav-toggle" id="navToggle">☰</button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#menu">Menú</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                    <li><a href="index.php?action=login" class="btn-primary">Ingresar</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2 class="hero-title animate-fade-in">Sabor Tradicional<br>en tu Mesa</h2>
            <p class="hero-subtitle animate-slide-up">Comida casera boliviana con delivery a domicilio</p>
            <div class="hero-buttons">
                <a href="#menu" class="btn btn-large btn-primary">Ver Menú</a>
                <a href="index.php?action=registro" class="btn btn-large btn-secondary">Registrarse</a>
            </div>
        </div>
    </section>

    <!-- Características -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">🚚</div>
                    <h3>Delivery Rápido</h3>
                    <p>Entrega en 30-45 minutos</p>
                </div>
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">👨‍🍳</div>
                    <h3>Comida Casera</h3>
                    <p>Recetas tradicionales auténticas</p>
                </div>
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">💳</div>
                    <h3>Pago Fácil</h3>
                    <p>Efectivo o transferencia</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menú Preview -->
    <section class="menu-preview" id="menu">
        <div class="container">
            <h2 class="section-title">Nuestro Menú</h2>
            <p class="section-subtitle">Platos preparados con ingredientes frescos</p>
            <div id="menuContainer" class="menu-grid">
                <!-- Se llena dinámicamente con JavaScript -->
            </div>
            <div class="text-center mt-40">
                <a href="index.php?action=login" class="btn btn-primary">Ver Menú Completo</a>
            </div>
        </div>
    </section>

    <!-- Sobre Nosotros -->
    <section class="about" id="nosotros">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Sobre Napanchita</h2>
                    <p>Somos un restaurante familiar dedicado a compartir los sabores auténticos de la cocina boliviana. Cada plato es preparado con amor y dedicación, usando recetas tradicionales transmitidas de generación en generación.</p>
                    <p>Nuestro compromiso es llevar la mejor comida casera directamente a tu hogar, manteniendo la calidad y el sabor que nos caracteriza.</p>
                    <ul class="about-list">
                        <li>✓ Ingredientes frescos y naturales</li>
                        <li>✓ Preparación del día</li>
                        <li>✓ Atención personalizada</li>
                        <li>✓ Precios justos</li>
                    </ul>
                </div>
                <div class="about-image">
                    <div class="image-placeholder">
                        <span>🍲</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section class="contact" id="contacto">
        <div class="container">
            <h2 class="section-title">Contáctanos</h2>
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">📱</div>
                    <h3>Teléfono</h3>
                    <p>+591 123-45678</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">📧</div>
                    <h3>Email</h3>
                    <p>info@napanchita.com</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">⏰</div>
                    <h3>Horario</h3>
                    <p>Lun-Dom: 11:00 - 22:00</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Napanchita. Todos los derechos reservados.</p>
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">WhatsApp</a>
            </div>
        </div>
    </footer>

    <script src="public/js/main.js"></script>
</body>
</html>
