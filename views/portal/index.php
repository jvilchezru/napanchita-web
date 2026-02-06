<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - <?php echo APP_NAME; ?></title>
    
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
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 50%, #80deea 100%);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff5252;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100"><path fill="%23e0f7fa" d="M0,50 Q360,0 720,50 T1440,50 L1440,100 L0,100 Z"></path></svg>');
            background-size: cover;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        /* Filtros de Categorías */
        .category-filters {
            margin: 30px 0;
            text-align: center;
        }

        .category-btn {
            margin: 5px;
            padding: 10px 25px;
            border: 2px solid #00acc1;
            background: white;
            color: #00838f;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }

        .category-btn:hover,
        .category-btn.active {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 131, 143, 0.3);
        }

        /* Tarjetas de Productos */
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            margin-bottom: 30px;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
        }

        .product-body {
            padding: 20px;
        }

        .product-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #00838f;
            margin-bottom: 10px;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            height: 40px;
            overflow: hidden;
        }

        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #00acc1;
            margin-bottom: 15px;
        }

        .btn-add-cart {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 12px;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 131, 143, 0.4);
            color: white;
        }

        /* Badge de Categoría */
        .category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 131, 143, 0.9);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .product-image-container {
            position: relative;
        }

        /* Sección de Combos */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #00838f;
            margin: 50px 0 30px;
            text-align: center;
        }

        .section-title i {
            margin-right: 15px;
            color: #00acc1;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            padding: 30px 0;
            margin-top: 60px;
            text-align: center;
        }

        /* Carrito Flotante */
        .floating-cart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .floating-cart-btn {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff5252 0%, #ff1744 100%);
            color: white;
            border: none;
            font-size: 1.5rem;
            box-shadow: 0 5px 25px rgba(255, 23, 68, 0.4);
            transition: all 0.3s;
            position: relative;
        }

        .floating-cart-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(255, 23, 68, 0.5);
        }

        /* Animaciones */
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

        .product-card {
            animation: fadeInUp 0.6s ease;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 50px;
        }

        .spinner-border {
            color: #00acc1;
        }

        /* Carrusel */
        .hero-carousel {
            position: relative;
            overflow: hidden;
        }

        .hero-carousel .carousel-inner {
            border-radius: 0;
        }

        .hero-carousel .carousel-item {
            height: 500px;
            position: relative;
        }

        .hero-carousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
        }

        .hero-carousel .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            background: rgba(0, 131, 143, 0.85);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .hero-carousel .carousel-caption h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .hero-carousel .carousel-caption p {
            font-size: 1.5rem;
            color: white;
            font-weight: 500;
        }

        /* Sección Sobre Nosotros */
        .about-section {
            background: white;
            padding: 80px 0;
            margin: 60px 0;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .about-section h2 {
            font-size: 3rem;
            font-weight: 700;
            color: #00838f;
            margin-bottom: 30px;
            text-align: center;
        }

        .about-section h2 i {
            color: #00acc1;
            margin-right: 15px;
        }

        .about-content {
            display: flex;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .about-image {
            flex: 1;
            min-width: 300px;
        }

        .about-image img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .about-text {
            flex: 1;
            min-width: 300px;
        }

        .about-text p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 20px;
            text-align: justify;
        }

        .about-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .about-feature {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            border-radius: 15px;
        }

        .about-feature i {
            font-size: 2.5rem;
            color: #00acc1;
            margin-bottom: 10px;
        }

        .about-feature h4 {
            color: #00838f;
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* Sección de Reseñas */
        .reviews-section {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            padding: 80px 0;
            margin: 60px 0;
            border-radius: 20px;
        }

        .reviews-section h2 {
            font-size: 3rem;
            font-weight: 700;
            color: #00838f;
            margin-bottom: 50px;
            text-align: center;
        }

        .reviews-section h2 i {
            color: #00acc1;
            margin-right: 15px;
        }

        .review-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .review-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .review-card.destacado {
            border: 3px solid #ffd700;
            background: linear-gradient(135deg, #fffef7 0%, #fff9e6 100%);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .review-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .review-author-info h5 {
            margin: 0;
            color: #00838f;
            font-weight: 600;
        }

        .review-date {
            font-size: 0.85rem;
            color: #888;
        }

        .review-stars {
            color: #ffc107;
            font-size: 1.2rem;
        }

        .review-stars i {
            margin-right: 3px;
        }

        .review-text {
            color: #555;
            line-height: 1.6;
            font-size: 1rem;
            margin-top: 15px;
        }

        .review-stats {
            text-align: center;
            margin-bottom: 50px;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .review-stats h3 {
            color: #00838f;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .review-stats .rating-display {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .review-stats .average-rating {
            font-size: 4rem;
            font-weight: 700;
            color: #00acc1;
        }

        .review-stats .stars-large {
            font-size: 2rem;
            color: #ffc107;
        }

        .review-stats .total-reviews {
            color: #666;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .hero-carousel .carousel-item {
                height: 350px;
            }

            .hero-carousel .carousel-caption h1 {
                font-size: 2rem;
            }

            .hero-carousel .carousel-caption p {
                font-size: 1rem;
            }

            .about-content {
                flex-direction: column;
            }

            .about-section h2,
            .reviews-section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>portal">
                <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="<?php echo APP_NAME; ?>">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">
                            <i class="fas fa-home me-2"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sobre-nosotros">
                            <i class="fas fa-info-circle me-2"></i>Sobre Nosotros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">
                            <i class="fas fa-utensils me-2"></i>Menú
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#resenas">
                            <i class="fas fa-star me-2"></i>Reseñas
                        </a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=mis-pedidos">
                                <i class="fas fa-receipt me-2"></i>Mis Pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=perfil">
                                <i class="fas fa-user me-2"></i>Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Salir
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>login">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Carrusel Hero -->
    <div id="inicio"></div>
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?php echo BASE_URL; ?>public/images/carousel-1.jpg" class="d-block w-100" alt="Ceviche Fresco" onerror="this.src='https://images.unsplash.com/photo-1625937286074-9ca519d5d9df?w=1200'">
                <div class="carousel-caption">
                    <h1><i class="fas fa-fish"></i> Los Mejores Ceviches de la Ciudad</h1>
                    <p>Sabor auténtico, ingredientes frescos del mar a tu mesa</p>
                </div>
            </div>
            
            <div class="carousel-item">
                <img src="<?php echo BASE_URL; ?>public/images/carousel-2.jpg" class="d-block w-100" alt="Mariscos Frescos" onerror="this.src='https://images.unsplash.com/photo-1559737558-2f5a767fd84e?w=1200'">
                <div class="carousel-caption">
                    <h1><i class="fas fa-utensils"></i> Mariscos Frescos del Día</h1>
                    <p>Pescados y mariscos seleccionados con la más alta calidad</p>
                </div>
            </div>
            
            <div class="carousel-item">
                <img src="<?php echo BASE_URL; ?>public/images/carousel-3.jpg" class="d-block w-100" alt="Delivery Rápido" onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200'">
                <div class="carousel-caption">
                    <h1><i class="fas fa-shipping-fast"></i> Delivery Express</h1>
                    <p>Tu pedido fresco y caliente, directo a tu hogar</p>
                </div>
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Mensajes -->
    <div class="container">
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sección Sobre Nosotros -->
    <div id="sobre-nosotros" class="container">
        <div class="about-section">
            <h2><i class="fas fa-info-circle"></i> Sobre Nosotros</h2>
            <div class="about-content">
                <div class="about-image">
                    <img src="<?php echo BASE_URL; ?>public/images/staff.jpg" alt="Equipo Ñapanchita" onerror="this.src='https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=800'">
                </div>
                <div class="about-text">
                    <p>
                        <strong>Cevichería Ñapanchita</strong> nace del amor por la gastronomía marina peruana 
                        y el deseo de compartir los sabores auténticos del mar con nuestra comunidad.
                    </p>
                    <p>
                        Desde nuestros inicios, nos hemos comprometido a seleccionar los pescados y mariscos 
                        más frescos del día, trabajando directamente con pescadores locales para garantizar 
                        la calidad que nuestros clientes merecen.
                    </p>
                    <p>
                        Nuestro equipo de chefs especializados combina recetas tradicionales con técnicas 
                        modernas para crear platos que deleitan el paladar y alimentan el alma.
                    </p>
                    
                    <div class="about-features">
                        <div class="about-feature">
                            <i class="fas fa-fish"></i>
                            <h4>Productos Frescos</h4>
                            <p>Del mar a tu mesa</p>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-award"></i>
                            <h4>Calidad Premium</h4>
                            <p>Ingredientes seleccionados</p>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-heart"></i>
                            <h4>Hecho con Amor</h4>
                            <p>Recetas tradicionales</p>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-truck"></i>
                            <h4>Delivery Rápido</h4>
                            <p>A tu puerta en minutos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Categorías -->
    <div id="menu" class="container">
        <div class="category-filters">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-th-large me-2"></i>Todos
            </button>
            <?php foreach ($categorias as $cat): ?>
                <button class="category-btn" data-category="<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </button>
            <?php endforeach; ?>
            <button class="category-btn" data-category="combos">
                <i class="fas fa-boxes me-2"></i>Combos
            </button>
        </div>
    </div>

    <!-- Platos -->
    <div class="container" id="platos-container">
        <div class="row" id="platos-list">
            <?php foreach ($platos as $plato): ?>
                <div class="col-md-4 col-lg-3 product-item" data-category="<?php echo $plato['categoria_id']; ?>">
                    <div class="product-card">
                        <div class="product-image-container">
                            <?php if ($plato['imagen_url']): ?>
                                <img src="<?php echo BASE_URL . $plato['imagen_url']; ?>" 
                                     alt="<?php echo htmlspecialchars($plato['nombre']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>public/images/default-plato.jpg" 
                                     alt="<?php echo htmlspecialchars($plato['nombre']); ?>" 
                                     class="product-image">
                            <?php endif; ?>
                            <span class="category-badge">
                                <?php 
                                $cat = array_filter($categorias, fn($c) => $c['id'] == $plato['categoria_id']);
                                echo htmlspecialchars(reset($cat)['nombre'] ?? 'Plato');
                                ?>
                            </span>
                        </div>
                        <div class="product-body">
                            <h5 class="product-title"><?php echo htmlspecialchars($plato['nombre']); ?></h5>
                            <p class="product-description"><?php echo htmlspecialchars($plato['descripcion']); ?></p>
                            <div class="product-price">S/ <?php echo number_format($plato['precio'], 2); ?></div>
                            <button class="btn btn-add-cart" 
                                    onclick="agregarAlCarrito('plato', <?php echo $plato['id']; ?>, '<?php echo htmlspecialchars($plato['nombre']); ?>')">
                                <i class="fas fa-cart-plus me-2"></i>Agregar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Combos -->
    <?php if (!empty($combos)): ?>
        <div class="container mt-5" id="combos-container">
            <h2 class="section-title">
                <i class="fas fa-boxes"></i>
                Combos Especiales
            </h2>
            <div class="row" id="combos-list">
                <?php foreach ($combos as $combo): ?>
                    <div class="col-md-6 col-lg-4 product-item" data-category="combos">
                        <div class="product-card">
                            <div class="product-image-container">
                                <?php if ($combo['imagen_url']): ?>
                                    <img src="<?php echo BASE_URL . $combo['imagen_url']; ?>" 
                                         alt="<?php echo htmlspecialchars($combo['nombre']); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <img src="<?php echo BASE_URL; ?>public/images/default-combo.jpg" 
                                         alt="<?php echo htmlspecialchars($combo['nombre']); ?>" 
                                         class="product-image">
                                <?php endif; ?>
                                <span class="category-badge" style="background: #ff5252;">
                                    <i class="fas fa-fire me-1"></i>COMBO
                                </span>
                            </div>
                            <div class="product-body">
                                <h5 class="product-title"><?php echo htmlspecialchars($combo['nombre']); ?></h5>
                                <p class="product-description"><?php echo htmlspecialchars($combo['descripcion']); ?></p>
                                <div class="product-price">S/ <?php echo number_format($combo['precio'], 2); ?></div>
                                <button class="btn btn-add-cart" 
                                        onclick="agregarAlCarrito('combo', <?php echo $combo['id']; ?>, '<?php echo htmlspecialchars($combo['nombre']); ?>')">
                                    <i class="fas fa-cart-plus me-2"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Carrito Flotante -->
    <div class="floating-cart">
        <a href="<?php echo BASE_URL; ?>index.php?controller=Portal&action=verCarrito" 
           class="btn floating-cart-btn">
            <i class="fas fa-shopping-cart"></i>
            <?php if ($items_carrito > 0): ?>
                <span class="cart-badge" id="cart-count"><?php echo $items_carrito; ?></span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Sección de Reseñas -->
    <div id="resenas" class="container">
        <div class="reviews-section">
            <h2><i class="fas fa-star"></i> Lo Que Dicen Nuestros Clientes</h2>
            
            <?php if (!empty($resenas)): ?>
                <!-- Estadísticas de Reseñas -->
                <?php if (isset($estadisticasResenas)): ?>
                <div class="container">
                    <div class="review-stats">
                        <h3>Calificación General</h3>
                        <div class="rating-display">
                            <div class="average-rating">
                                <?php echo number_format($estadisticasResenas['promedio_calificacion'], 1); ?>
                            </div>
                            <div>
                                <div class="stars-large">
                                    <?php 
                                    $promedio = round($estadisticasResenas['promedio_calificacion']);
                                    for ($i = 1; $i <= 5; $i++): 
                                    ?>
                                        <i class="fas fa-star<?php echo $i <= $promedio ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <div class="total-reviews">
                                    Basado en <?php echo $estadisticasResenas['total_resenas']; ?> reseñas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Lista de Reseñas -->
                <div class="container">
                    <div class="row">
                        <?php foreach ($resenas as $resena): ?>
                        <div class="col-md-6">
                            <div class="review-card <?php echo $resena['destacado'] ? 'destacado' : ''; ?>">
                                <div class="review-header">
                                    <div class="review-author">
                                        <div class="review-avatar">
                                            <?php echo strtoupper(substr($resena['cliente_nombre'], 0, 1)); ?>
                                        </div>
                                        <div class="review-author-info">
                                            <h5><?php echo htmlspecialchars($resena['cliente_nombre']); ?></h5>
                                            <div class="review-date"><?php echo $resena['fecha_formateada']; ?></div>
                                        </div>
                                    </div>
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo $i <= $resena['calificacion'] ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="review-text">
                                    <?php echo nl2br(htmlspecialchars($resena['comentario'])); ?>
                                </div>
                                <?php if ($resena['destacado']): ?>
                                    <div class="mt-2">
                                        <span class="badge" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #000;">
                                            <i class="fas fa-trophy"></i> Reseña Destacada
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="container">
                    <div class="text-center py-5">
                        <i class="fas fa-comments" style="font-size: 4rem; color: #00acc1; opacity: 0.5;"></i>
                        <p class="mt-3" style="color: #666; font-size: 1.2rem;">Sé el primero en dejarnos tu opinión</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
            <p>
                <i class="fas fa-phone me-2"></i><?php echo $this->config->obtener('telefono'); ?> | 
                <i class="fas fa-envelope me-2"></i><?php echo $this->config->obtener('email'); ?>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Filtrado por categoría
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Actualizar botón activo
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const category = this.dataset.category;
                const platosContainer = document.getElementById('platos-container');
                const combosContainer = document.getElementById('combos-container');

                // Filtrar items
                document.querySelectorAll('.product-item').forEach(item => {
                    if (category === 'all') {
                        item.style.display = 'block';
                        if (platosContainer) platosContainer.style.display = 'block';
                        if (combosContainer) combosContainer.style.display = 'block';
                    } else if (category === 'combos') {
                        if (item.dataset.category === 'combos') {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                        if (platosContainer) platosContainer.style.display = 'none';
                        if (combosContainer) combosContainer.style.display = 'block';
                    } else {
                        if (item.dataset.category === category) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                        if (platosContainer) platosContainer.style.display = 'block';
                        if (combosContainer) combosContainer.style.display = 'none';
                    }
                });
            });
        });

        // Agregar al carrito
        function agregarAlCarrito(tipo, id, nombre) {
            const formData = new FormData();
            formData.append('tipo_producto', tipo);
            formData.append('producto_id', id);
            formData.append('cantidad', 1);

            fetch('<?php echo BASE_URL; ?>index.php?controller=Portal&action=agregarAlCarrito', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador del carrito
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.total_items;
                    } else {
                        const floatingBtn = document.querySelector('.floating-cart-btn');
                        floatingBtn.innerHTML += `<span class="cart-badge" id="cart-count">${data.total_items}</span>`;
                    }

                    // Mostrar notificación
                    mostrarNotificacion('success', `${nombre} agregado al carrito`);
                } else {
                    mostrarNotificacion('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('error', 'Error al agregar al carrito');
            });
        }

        // Mostrar notificación
        function mostrarNotificacion(tipo, mensaje) {
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
            alert.style.zIndex = '9999';
            alert.style.minWidth = '300px';
            alert.innerHTML = `
                <i class="fas ${icon} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }
    </script>
</body>
</html>
