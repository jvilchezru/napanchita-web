<?php
// Cargar configuración del sistema
require_once __DIR__ . '/../../models/Configuracion.php';
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$configuracionModel = new Configuracion($db);

// Obtener configuraciones
$configuraciones = $configuracionModel->obtenerTodas();
$config = [];
foreach ($configuraciones as $conf) {
    $config[$conf['clave']] = $conf['valor'];
}

// Variables para el header
$nombreRestaurante = $config['nombre_restaurante'] ?? 'Cevichería Ñapanchita';
$telefonoRestaurante = $config['telefono'] ?? '';
$direccionRestaurante = $config['direccion'] ?? '';

// Función para convertir fecha a español
function fechaEspanol($formato = 'l, d \d\e F \d\e Y') {
    $dias = array('Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 
                  'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo');
    $meses = array('January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril',
                   'May' => 'Mayo', 'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto',
                   'September' => 'Septiembre', 'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre');
    
    $fecha = date('l, d \d\e F \d\e Y');
    $fecha = str_replace(array_keys($dias), array_values($dias), $fecha);
    $fecha = str_replace(array_keys($meses), array_values($meses), $fecha);
    return $fecha;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo $nombreRestaurante; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Font Awesome Fallback -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css" crossorigin="anonymous">

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
            min-height: var(--header-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        #sidebarToggle {
            font-size: 1.25rem;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: transparent;
            border: none;
            color: #17a2b8;
        }

        #sidebarToggle:hover {
            background-color: #e7f6f8;
            color: #00bcd4;
            transform: scale(1.05);
        }

        #sidebarToggle:active {
            transform: scale(0.95);
        }

        .sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .main-content.expanded {
            margin-left: 0 !important;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info > div {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s;
            cursor: pointer;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }

        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            padding-left: 25px;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .dropdown-item-text {
            padding: 15px 20px;
        }

        .dropdown-header {
            padding: 12px 20px;
        }

        .user-info .btn-link {
            text-decoration: none;
            padding: 10px 15px;
        }

        .user-info .btn-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        .user-info .dropdown-toggle {
            padding: 8px 15px;
        }

        .user-info .dropdown-toggle:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
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
                <div class="d-flex align-items-center">
                    <button class="btn" id="sidebarToggle" title="Ocultar/Mostrar menú">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="ms-3 text-muted fw-medium"><i class="far fa-calendar-alt me-2"></i><?php echo fechaEspanol(); ?></span>
                </div>

                <div class="user-info">
                    <?php
                    // Obtener notificaciones según el rol
                    require_once __DIR__ . '/../../models/Pedido.php';
                    require_once __DIR__ . '/../../models/Reserva.php';
                    
                    // Crear nueva instancia para notificaciones
                    $dbNotif = (new Database())->getConnection();
                    $pedidoModelNotif = new Pedido($dbNotif);
                    $reservaModelNotif = new Reserva($dbNotif);
                    
                    $notificaciones = [];
                    $totalNotificaciones = 0;
                    
                    if (has_role([ROL_ADMIN, ROL_MESERO])) {
                        // Pedidos pendientes
                        $pedidosPendientes = $pedidoModelNotif->listar(['estado' => 'pendiente']);
                        foreach ($pedidosPendientes as $pedidoNotif) {
                            $notificaciones[] = [
                                'icono' => 'fa-shopping-cart',
                                'color' => 'warning',
                                'texto' => 'Pedido #' . $pedidoNotif['id'] . ' pendiente',
                                'enlace' => BASE_URL . 'index.php?action=pedidos_ver&id=' . $pedidoNotif['id'],
                                'tiempo' => $pedidoNotif['fecha_pedido']
                            ];
                        }
                        
                        // Pedidos listos
                        $pedidosListos = $pedidoModelNotif->listar(['estado' => 'listo']);
                        foreach ($pedidosListos as $pedidoNotif) {
                            $notificaciones[] = [
                                'icono' => 'fa-check-circle',
                                'color' => 'success',
                                'texto' => 'Pedido #' . $pedidoNotif['id'] . ' listo para entregar',
                                'enlace' => BASE_URL . 'index.php?action=pedidos_ver&id=' . $pedidoNotif['id'],
                                'tiempo' => $pedidoNotif['fecha_pedido']
                            ];
                        }
                        
                        // Reservas pendientes de hoy
                        $reservasHoy = $reservaModelNotif->listarPorFecha(date('Y-m-d'));
                        foreach ($reservasHoy as $reserva) {
                            if ($reserva['estado'] == 'pendiente') {
                                $notificaciones[] = [
                                    'icono' => 'fa-calendar-check',
                                    'color' => 'info',
                                    'texto' => 'Reserva de ' . $reserva['cliente_nombre'] . ' a las ' . date('H:i', strtotime($reserva['hora'])),
                                    'enlace' => BASE_URL . 'index.php?action=reservas_editar&id=' . $reserva['id'],
                                    'tiempo' => $reserva['fecha'] . ' ' . $reserva['hora']
                                ];
                            }
                        }
                    }
                    
                    // Ordenar por tiempo descendente (más recientes primero)
                    usort($notificaciones, function($a, $b) {
                        return strtotime($b['tiempo']) - strtotime($a['tiempo']);
                    });
                    
                    // Limitar a 10 notificaciones
                    $notificaciones = array_slice($notificaciones, 0, 10);
                    $totalNotificaciones = count($notificaciones);
                    ?>
                    
                    <div class="dropdown">
                        <button class="btn btn-link text-dark position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell fa-lg"></i>
                            <?php if ($totalNotificaciones > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $totalNotificaciones > 9 ? '9+' : $totalNotificaciones; ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 350px; max-height: 500px; overflow-y: auto;">
                            <li>
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Notificaciones</span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $totalNotificaciones; ?></span>
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <?php if (empty($notificaciones)): ?>
                                <li>
                                    <div class="dropdown-item text-center text-muted py-4">
                                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                        <p class="mb-0">No hay notificaciones</p>
                                    </div>
                                </li>
                            <?php else: ?>
                                <?php foreach ($notificaciones as $notif): ?>
                                    <li>
                                        <a class="dropdown-item py-3" href="<?php echo $notif['enlace']; ?>">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <i class="fas <?php echo $notif['icono']; ?> fa-lg text-<?php echo $notif['color']; ?>"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div><?php echo htmlspecialchars($notif['texto']); ?></div>
                                                    <small class="text-muted">
                                                        <?php 
                                                        $tiempo = strtotime($notif['tiempo']);
                                                        $ahora = time();
                                                        $diff = $ahora - $tiempo;
                                                        
                                                        if ($diff < 60) {
                                                            echo 'Hace unos segundos';
                                                        } elseif ($diff < 3600) {
                                                            echo 'Hace ' . floor($diff / 60) . ' min';
                                                        } elseif ($diff < 86400) {
                                                            echo 'Hace ' . floor($diff / 3600) . ' hrs';
                                                        } else {
                                                            echo date('d/m/Y H:i', $tiempo);
                                                        }
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php
                    // Obtener información completa del usuario
                    require_once __DIR__ . '/../../models/Usuario.php';
                    $usuarioModel = new Usuario($db);
                    $usuarioModel->id = $_SESSION['usuario_id'];
                    $usuarioCompleto = $usuarioModel->obtenerPorId();
                    
                    $nombreUsuario = $usuarioCompleto['nombre'] ?? $_SESSION['nombre'] ?? 'Usuario';
                    $emailUsuario = $usuarioCompleto['email'] ?? '';
                    $rolUsuario = $usuarioCompleto['rol'] ?? $_SESSION['rol'] ?? 'usuario';
                    $iniciales = strtoupper(substr($nombreUsuario, 0, 1));
                    
                    // Generar color de avatar basado en el rol
                    $colorAvatar = match($rolUsuario) {
                        'admin' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);',
                        'mesero' => 'background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);',
                        'repartidor' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);',
                        default => 'background: linear-gradient(135deg, #6c757d 0%, #495057 100%);'
                    };
                    ?>

                    <div class="user-avatar" style="<?php echo $colorAvatar; ?>" title="<?php echo htmlspecialchars($nombreUsuario); ?>">
                        <?php echo $iniciales; ?>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-link text-dark text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <div class="text-start">
                                <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php 
                                    $rolesTexto = [
                                        'admin' => 'Administrador',
                                        'mesero' => 'Mesero',
                                        'repartidor' => 'Repartidor'
                                    ];
                                    echo $rolesTexto[$rolUsuario] ?? ucfirst($rolUsuario);
                                    ?>
                                </small>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-item-text">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="<?php echo $colorAvatar; ?> width: 50px; height: 50px; font-size: 20px;">
                                            <?php echo $iniciales; ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($emailUsuario); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?action=perfil">
                                    <i class="fas fa-user me-2"></i> Mi Perfil
                                </a>
                            </li>
                            <?php if (has_role(ROL_ADMIN)): ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?action=configuracion">
                                    <i class="fas fa-cog me-2"></i> Configuración
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?action=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">