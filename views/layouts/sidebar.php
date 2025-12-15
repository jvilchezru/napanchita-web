<aside class="sidebar">
    <div class="sidebar-header">
        <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="Logo Cevichería Ñapanchita" style="max-width: 200px; margin-bottom: 15px;">
    </div>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="<?php echo (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <!-- Administrador -->
        <?php if (has_role(ROL_ADMIN)): ?>
            <li class="mt-3">
                <small class="text-white-50 px-3">ADMINISTRACIÓN</small>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=usuarios" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'usuario') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog"></i> Gestión de Usuarios
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=clientes" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'cliente') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Clientes
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=categorias" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'categoria') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Categorías
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=platos" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'plato') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-fish"></i> Platos
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=combos" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'combo') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-box-open"></i> Combos
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=mesas" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'mesa') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-chair"></i> Mesas
                </a>
            </li>

            <?php /* TEMPORALMENTE OCULTO
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=zonas_delivery">
                    <i class="fas fa-map-marked-alt"></i> Zonas de Delivery
                </a>
            </li>
            */ ?>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=metodos_pago">
                    <i class="fas fa-credit-card"></i> Métodos de Pago
                </a>
            </li>
        <?php endif; ?>

        <!-- Pedidos (Admin y Mesero) -->
        <?php if (has_role([ROL_ADMIN, ROL_MESERO])): ?>
            <li class="mt-3">
                <small class="text-white-50 px-3">OPERACIONES</small>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=pedidos" class="<?php echo (isset($_GET['action']) && (strpos($_GET['action'], 'pedido') !== false)) ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Pedidos
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=reservas">
                    <i class="fas fa-calendar-check"></i> Reservas
                </a>
            </li>
        <?php endif; ?>

        <?php /* TEMPORALMENTE OCULTO - MÓDULO DELIVERY
        <!-- Delivery (Admin y Repartidor) -->
        <?php if (has_role([ROL_ADMIN, ROL_REPARTIDOR])): ?>
            <?php if (has_role(ROL_REPARTIDOR) && !has_role(ROL_ADMIN)): ?>
                <li class="mt-3">
                    <small class="text-white-50 px-3">ENTREGAS</small>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=deliveries">
                    <i class="fas fa-truck"></i> Deliveries
                </a>
            </li>
        <?php endif; ?>
        */ ?>

        <!-- Ventas y Reportes (Solo Admin) -->
        <?php if (has_role(ROL_ADMIN)): ?>
            <li class="mt-3">
                <small class="text-white-50 px-3">FINANZAS</small>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=ventas" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'venta') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-cash-register"></i> Ventas
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=cierre_caja" class="<?php echo (isset($_GET['action']) && strpos($_GET['action'], 'cierre') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Cierres de Caja
                </a>
            </li>

            <li class="mt-3">
                <small class="text-white-50 px-3">REPORTES</small>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=reportes_ventas">
                    <i class="fas fa-chart-line"></i> Reporte de Ventas
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=reportes_platos">
                    <i class="fas fa-chart-bar"></i> Platos Más Vendidos
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=reportes_clientes">
                    <i class="fas fa-user-friends"></i> Clientes Frecuentes
                </a>
            </li>

            <!-- Log de Actividad - Oculto
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=logs">
                    <i class="fas fa-history"></i> Log de Actividad
                </a>
            </li>
            -->
        <?php endif; ?>

        <!-- Configuración -->
        <li class="mt-3">
            <small class="text-white-50 px-3">SISTEMA</small>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>index.php?action=perfil">
                <i class="fas fa-user-circle"></i> Mi Perfil
            </a>
        </li>

        <?php if (has_role(ROL_ADMIN)): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php?action=configuracion">
                    <i class="fas fa-cog"></i> Configuración
                </a>
            </li>
        <?php endif; ?>

        <li>
            <a href="<?php echo BASE_URL; ?>index.php?action=logout" class="text-warning">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </li>
    </ul>

    <div class="text-center py-3 border-top border-white-50 mt-4">
        <small class="text-white-50">v<?php echo APP_VERSION; ?></small>
    </div>
</aside>