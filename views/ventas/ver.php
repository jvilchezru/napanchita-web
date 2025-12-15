<?php
$pageTitle = 'Detalle de Venta';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-file-invoice-dollar me-2"></i> Detalle de Venta #<?php echo $venta['id']; ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=ventas">Ventas</a></li>
            <li class="breadcrumb-item active">Detalle #<?php echo $venta['id']; ?></li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Información de la Venta -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información de la Venta</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">ID Venta:</td>
                            <td>#<?php echo $venta['id']; ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Fecha y Hora:</td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($venta['fecha_venta'])); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Usuario:</td>
                            <td><?php echo htmlspecialchars($venta['usuario_nombre'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">ID Pedido:</td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_ver&id=<?php echo $venta['pedido_id']; ?>">
                                    #<?php echo $venta['pedido_id']; ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tipo de Pedido:</td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo ucfirst($venta['pedido_tipo'] ?? 'N/A'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Estado del Pedido:</td>
                            <td>
                                <span class="badge bg-success">
                                    <?php echo ucfirst($venta['pedido_estado'] ?? 'N/A'); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Información de Pago</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 40%;">Método de Pago:</td>
                            <td>
                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars($venta['metodo_pago_nombre'] ?? 'N/A'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Subtotal:</td>
                            <td>S/ <?php echo number_format($venta['total'] + $venta['descuento_aplicado'], 2); ?></td>
                        </tr>
                        <?php if ($venta['descuento_aplicado'] > 0): ?>
                            <tr>
                                <td class="fw-bold">Descuento:</td>
                                <td class="text-danger">
                                    - S/ <?php echo number_format($venta['descuento_aplicado'], 2); ?>
                                    <?php if ($venta['codigo_descuento']): ?>
                                        <small class="text-muted">(<?php echo htmlspecialchars($venta['codigo_descuento']); ?>)</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="fw-bold fs-5">Total:</td>
                            <td class="fw-bold fs-5 text-success">S/ <?php echo number_format($venta['total'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Monto Recibido:</td>
                            <td>S/ <?php echo number_format($venta['monto_recibido'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Cambio:</td>
                            <td>S/ <?php echo number_format($venta['monto_cambio'], 2); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle del Pedido -->
    <?php if (!empty($pedido)): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Detalle del Pedido</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($pedido['items'])): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedido['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <span class="badge <?php echo $item['tipo'] == 'combo' ? 'bg-warning' : 'bg-info'; ?>">
                                                <?php echo ucfirst($item['tipo'] ?? 'producto'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                            <?php if (!empty($item['notas'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($item['notas']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $item['cantidad']; ?></td>
                                        <td class="text-end">S/ <?php echo number_format($item['precio_unitario'], 2); ?></td>
                                        <td class="text-end"><strong>S/ <?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">TOTAL:</td>
                                    <td class="text-end fw-bold text-success fs-5">
                                        S/ <?php echo number_format($pedido['total'], 2); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No se encontraron items en este pedido.
                    </div>
                <?php endif; ?>

                <?php if (!empty($pedido['cliente_nombre'])): ?>
                    <div class="mt-3">
                        <h6 class="fw-bold">Información del Cliente:</h6>
                        <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre']); ?></p>
                        <?php if (!empty($pedido['cliente_telefono'])): ?>
                            <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['cliente_telefono']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($pedido['direccion_entrega'])): ?>
                            <p class="mb-1"><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion_entrega']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Acciones -->
    <div class="card">
        <div class="card-body text-center">
            <a href="<?php echo BASE_URL; ?>index.php?action=ventas" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Ventas
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?php echo BASE_URL; ?>index.php?action=ventas_imprimir&id=<?php echo $venta['id']; ?>"
                class="btn btn-success" target="_blank">
                <i class="fas fa-receipt"></i> Ver Comprobante
            </a>
        </div>
    </div>
</div>

<style>
    @media print {

        .sidebar,
        .page-header nav,
        .card-body .btn,
        .no-print,
        button,
        .btn-group {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 20px !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            page-break-inside: avoid;
            margin-bottom: 15px !important;
        }

        .card-header {
            background: white !important;
            color: black !important;
            border-bottom: 2px solid #333 !important;
        }
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>