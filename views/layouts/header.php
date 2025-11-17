<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo APP_NAME; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/tema-celeste.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #17a2b8;
            --secondary-color: #00bcd4;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #17a2b8 0%, #00bcd4 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .sidebar-menu li a {
            display: block;
            padding: 15px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar-menu li a i {
            width: 25px;
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        .top-header {
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .content-area {
            padding: 30px;
            flex: 1;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 5px 0 0 0;
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
        }

        .card-header {
            background: white;
            border-bottom: 2px solid #f1f1f1;
            font-weight: 600;
            padding: 20px;
        }

        .stat-card {
            padding: 25px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
        }

        .stat-card.red {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }

        .stat-card i {
            font-size: 40px;
            opacity: 0.3;
            float: right;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .badge-activo {
            background-color: #28a745;
        }

        .badge-inactivo {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div>
                    <button class="btn btn-link text-dark" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="ms-3 text-muted"><?php echo date('l, d F Y'); ?></span>
                </div>

                <div class="user-info">
                    <div class="dropdown">
                        <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell me-1"></i>
                            <span class="badge bg-danger rounded-pill">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">Notificaciones</h6>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-cart me-2"></i> Nuevo pedido #1234</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar me-2"></i> Reserva confirmada</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-truck me-2"></i> Delivery en camino</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                        </ul>
                    </div>

                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nombre'] ?? 'U', 0, 1)); ?>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <strong><?php echo $_SESSION['nombre'] ?? 'Usuario'; ?></strong>
                            <br>
                            <small class="text-muted"><?php echo ucfirst($_SESSION['rol'] ?? 'usuario'); ?></small>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-key me-2"></i> Cambiar Contraseña</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?action=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                </a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">