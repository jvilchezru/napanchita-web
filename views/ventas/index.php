<?php
$pageTitle = 'Gestión de Ventas';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-cash-register me-2"></i> Gestión de Ventas</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Ventas</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <?php echo get_flash_message(); ?>

    <div class="row mb-3">
        <div class="col-auto">
            <a href="<?php echo BASE_URL; ?>index.php?action=ventas_registrar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Venta
            </a>
        </div>
        <div class="col-auto">
            <a href="<?php echo BASE_URL; ?>index.php?action=cierre_caja&fecha=<?php echo date('Y-m-d'); ?>" class="btn btn-secondary">
                <i class="fas fa-file-invoice-dollar"></i> Cierre de Caja
            </a>
        </div>
    </div>

    <!-- Filtros y Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="action" value="ventas">

                        <div class="col-md-3">
                            <label class="form-label">Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control"
                                value="<?php echo $filtros['fecha_desde']; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control"
                                value="<?php echo $filtros['fecha_hasta']; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="metodo_pago_id" class="form-control">
                                <option value="">Todos</option>
                                <?php if (!empty($metodosPago)): ?>
                                    <?php foreach ($metodosPago as $metodo): ?>
                                        <option value="<?php echo $metodo['id']; ?>"
                                            <?php echo $filtros['metodo_pago_id'] == $metodo['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($metodo['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Total Ventas</p>
                            <h3 class="mb-0"><?php echo $estadisticas['total_ventas'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Total Ingresos</p>
                            <h3 class="mb-0">S/ <?php echo number_format($estadisticas['total_ingresos'] ?? 0, 2); ?></h3>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Ticket Promedio</p>
                            <h3 class="mb-0">S/ <?php echo number_format($estadisticas['ticket_promedio'] ?? 0, 2); ?></h3>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Total Descuentos</p>
                            <h3 class="mb-0">S/ <?php echo number_format($estadisticas['total_descuentos'] ?? 0, 2); ?></h3>
                        </div>
                        <i class="fas fa-percent fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Totales por Método de Pago -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Ventas por Método de Pago</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Método de Pago</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($totalesPorMetodo)): ?>
                                    <?php foreach ($totalesPorMetodo as $metodo): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-<?php
                                                                    echo $metodo['metodo_pago'] == 'Efectivo' ? 'money-bill-wave' : ($metodo['metodo_pago'] == 'Tarjeta' ? 'credit-card' : 'mobile-alt');
                                                                    ?>"></i>
                                                <?php echo htmlspecialchars($metodo['metodo_pago']); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?php echo $metodo['cantidad']; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <strong>S/ <?php echo number_format($metodo['total'], 2); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Ventas -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Ventas</h5>
            <a href="<?php echo BASE_URL; ?>index.php?action=cierre_caja&fecha=<?php echo $filtros['fecha_desde']; ?>"
                class="btn btn-sm btn-secondary">
                <i class="fas fa-file-invoice-dollar"></i> Ver Cierre de Caja
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Pedido</th>
                            <th>Método Pago</th>
                            <th>Usuario</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ventas)): ?>
                            <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td><?php echo $venta['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></td>
                                    <td>
                                        <?php if ($venta['pedido_id']): ?>
                                            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_ver&id=<?php echo $venta['pedido_id']; ?>">
                                                #<?php echo $venta['pedido_id']; ?>
                                            </a>
                                            <br><small class="text-muted"><?php echo $venta['pedido_tipo']; ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($venta['metodo_pago_nombre'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($venta['usuario_nombre']); ?></td>
                                    <td class="text-end">
                                        <strong>S/ <?php echo number_format($venta['total'], 2); ?></strong>
                                        <?php if ($venta['descuento_aplicado'] > 0): ?>
                                            <br><small class="text-success">
                                                Desc: -S/ <?php echo number_format($venta['descuento_aplicado'], 2); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="verDetalleVenta(<?php echo $venta['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-secondary" onclick="imprimirComprobante(<?php echo $venta['id']; ?>)">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No se encontraron ventas</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function verDetalleVenta(id) {
        window.location.href = '<?php echo BASE_URL; ?>index.php?action=ventas_ver&id=' + id;
    }

    function imprimirComprobante(id) {
        window.open('<?php echo BASE_URL; ?>index.php?action=ventas_imprimir&id=' + id, '_blank');
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>