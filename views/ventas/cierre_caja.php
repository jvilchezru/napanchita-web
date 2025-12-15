<?php
$pageTitle = 'Cierre de Caja';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-file-invoice-dollar me-2"></i> Cierre de Caja</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=ventas">Ventas</a></li>
            <li class="breadcrumb-item active">Cierre de Caja</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row mb-3 no-print">
        <div class="col-auto">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
        <div class="col-auto">
            <a href="<?php echo BASE_URL; ?>index.php?action=ventas" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Selector de Fecha -->
    <div class="row mb-4 no-print">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="action" value="cierre_caja">

                        <div class="col-md-4">
                            <label class="form-label">Fecha del Cierre</label>
                            <input type="date" name="fecha" class="form-control"
                                value="<?php echo $fecha; ?>" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Consultar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Cierre -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-calendar-day"></i>
                Cierre de Caja - <?php echo date('d/m/Y', strtotime($fecha)); ?>
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($fecha)); ?></p>
                    <p><strong>Generado por:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Sistema'); ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <p><strong>Hora de impresión:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                    <p><strong>Sistema:</strong> Ñapanchita POS</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <h6>Total de Ventas</h6>
                    <h2><?php echo $estadisticas['total_ventas'] ?? 0; ?></h2>
                    <small>transacciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h6>Ingresos Totales</h6>
                    <h2>S/ <?php echo number_format($estadisticas['total_ingresos'] ?? 0, 2); ?></h2>
                    <small>Total facturado</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <h6>Ticket Promedio</h6>
                    <h2>S/ <?php echo number_format($estadisticas['ticket_promedio'] ?? 0, 2); ?></h2>
                    <small>Promedio por venta</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle por Método de Pago -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-money-check-alt"></i> Detalle por Método de Pago</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Método de Pago</th>
                            <th class="text-center">Cantidad de Transacciones</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">% del Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalGeneral = $estadisticas['total_ingresos'] ?? 0;
                        if (!empty($totalesPorMetodo)):
                        ?>
                            <?php foreach ($totalesPorMetodo as $metodo): ?>
                                <?php $porcentaje = $totalGeneral > 0 ? ($metodo['total'] / $totalGeneral * 100) : 0; ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-<?php
                                                            echo $metodo['metodo_pago'] == 'Efectivo' ? 'money-bill-wave text-success' : ($metodo['metodo_pago'] == 'Tarjeta' ? 'credit-card text-primary' : 'mobile-alt text-info');
                                                            ?>"></i>
                                        <strong><?php echo htmlspecialchars($metodo['metodo_pago']); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?php echo $metodo['cantidad']; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <strong>S/ <?php echo number_format($metodo['total'], 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?php echo number_format($porcentaje, 1); ?>%</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-primary">
                                <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                                <td class="text-end">
                                    <h5 class="mb-0">S/ <?php echo number_format($totalGeneral, 2); ?></h5>
                                </td>
                                <td class="text-center">
                                    <strong>100%</strong>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay ventas registradas para esta fecha</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detalle de Ventas -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Detalle de Ventas del Día</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hora</th>
                            <th>Pedido</th>
                            <th>Método Pago</th>
                            <th>Usuario</th>
                            <th class="text-end">Descuento</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ventas)): ?>
                            <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td><?php echo $venta['id']; ?></td>
                                    <td><?php echo date('H:i', strtotime($venta['fecha_venta'])); ?></td>
                                    <td>
                                        <?php if ($venta['pedido_id']): ?>
                                            #<?php echo $venta['pedido_id']; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($venta['metodo_pago_nombre'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td><small><?php echo htmlspecialchars($venta['usuario_nombre']); ?></small></td>
                                    <td class="text-end">
                                        <?php if ($venta['descuento_aplicado'] > 0): ?>
                                            <small class="text-danger">-S/ <?php echo number_format($venta['descuento_aplicado'], 2); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong>S/ <?php echo number_format($venta['total'], 2); ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No hay ventas registradas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>7
                </table>
            </div>
        </div>
    </div>

    <!-- Firmas -->
    <div class="card">
        <div class="card-body">
            <div class="row mt-5 pt-5">
                <div class="col-md-4 text-center">
                    <div style="height: 80px;"></div>
                    <div class="border-top border-dark pt-3" style="border-width: 2px !important;">
                        <strong style="font-size: 14px;">Cajero</strong>
                        <p class="mb-0" style="font-size: 11px; color: #666;">Nombre y Firma</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="height: 80px;"></div>
                    <div class="border-top border-dark pt-3" style="border-width: 2px !important;">
                        <strong style="font-size: 14px;">Supervisor</strong>
                        <p class="mb-0" style="font-size: 11px; color: #666;">Nombre y Firma</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="height: 80px;"></div>
                    <div class="border-top border-dark pt-3" style="border-width: 2px !important;">
                        <strong style="font-size: 14px;">Gerencia</strong>
                        <p class="mb-0" style="font-size: 11px; color: #666;">Nombre y Firma</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .no-print,
            .sidebar,
            .btn,
            .card-header .no-print {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 20px !important;
            }

            .card {
                page-break-inside: avoid;
                border: 1px solid #000 !important;
            }

            body {
                font-size: 12pt !important;
                color: #000 !important;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                color: #000 !important;
                font-weight: bold !important;
            }

            table {
                font-size: 11pt !important;
            }

            .table thead th {
                background-color: #f0f0f0 !important;
                color: #000 !important;
                font-weight: bold !important;
                border: 1px solid #000 !important;
            }

            .table td,
            .table th {
                border: 1px solid #333 !important;
                padding: 8px !important;
            }

            strong {
                font-weight: bold !important;
                color: #000 !important;
            }

            .card-header {
                background-color: #f0f0f0 !important;
                color: #000 !important;
                font-weight: bold !important;
                border-bottom: 2px solid #000 !important;
            }

            p {
                color: #000 !important;
            }
        }
    </style>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>