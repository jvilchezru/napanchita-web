<?php
$pageTitle = 'Detalle del Pedido #' . $pedido['id'];
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-receipt me-2"></i> Pedido #<?php echo $pedido['id']; ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=pedidos">Pedidos</a></li>
            <li class="breadcrumb-item active">Pedido #<?php echo $pedido['id']; ?></li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Información del pedido -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tipo:</strong> 
                            <?php
                            $tipos = ['mesa' => 'Mesa', 'delivery' => 'Delivery', 'para_llevar' => 'Para Llevar'];
                            echo $tipos[$pedido['tipo']] ?? $pedido['tipo'];
                            ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong>
                            <?php
                            $estados = [
                                'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
                                'en_preparacion' => '<span class="badge bg-info">En Preparación</span>',
                                'listo' => '<span class="badge bg-primary">Listo</span>',
                                'entregado' => '<span class="badge bg-success">Entregado</span>',
                                'cancelado' => '<span class="badge bg-danger">Cancelado</span>'
                            ];
                            echo $estados[$pedido['estado']] ?? $pedido['estado'];
                            ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <?php if ($pedido['tipo'] == 'mesa'): ?>
                            <div class="col-md-6">
                                <strong>Mesa:</strong> <?php echo $pedido['mesa_numero'] ?? 'N/A'; ?>
                            </div>
                        <?php else: ?>
                            <div class="col-md-6">
                                <strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'N/A'); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Teléfono:</strong> <?php echo $pedido['cliente_telefono'] ?? 'N/A'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Atendido por:</strong> <?php echo $pedido['usuario_nombre']; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                        </div>
                    </div>
                    <?php if ($pedido['notas']): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <strong><i class="fas fa-comment"></i> Notas:</strong> <?php echo htmlspecialchars($pedido['notas']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items del pedido -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Items del Pedido</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto/Combo</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedido['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                        <?php if ($item['notas']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($item['notas']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo $item['cantidad']; ?></td>
                                    <td class="text-end">S/ <?php echo number_format($item['precio_unitario'], 2); ?></td>
                                    <td class="text-end"><strong>S/ <?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Resumen y acciones -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill"></i> Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>S/ <?php echo number_format($pedido['subtotal'], 2); ?></strong>
                    </div>
                    <?php if ($pedido['costo_envio'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Costo de envío:</span>
                            <strong>S/ <?php echo number_format($pedido['costo_envio'], 2); ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($pedido['descuento'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Descuento:</span>
                            <strong>- S/ <?php echo number_format($pedido['descuento'], 2); ?></strong>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h4>Total:</h4>
                        <h4 class="text-success">S/ <?php echo number_format($pedido['total'], 2); ?></h4>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <?php if ($pedido['estado'] != 'cancelado' && $pedido['estado'] != 'entregado'): ?>
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cog"></i> Acciones</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($pedido['estado'] == 'pendiente' && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
                            <button class="btn btn-info w-100 mb-2" onclick="cambiarEstado('en_preparacion')">
                                <i class="fas fa-fire"></i> Iniciar Preparación
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'en_preparacion' && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
                            <button class="btn btn-success w-100 mb-2" onclick="cambiarEstado('listo')">
                                <i class="fas fa-check"></i> Marcar como Listo
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'listo'): ?>
                            <button class="btn btn-primary w-100 mb-2" onclick="cambiarEstado('entregado')">
                                <i class="fas fa-check-double"></i> Marcar como Entregado
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                        <button class="btn btn-danger w-100" onclick="cancelarPedido()">
                            <i class="fas fa-times"></i> Cancelar Pedido
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos" class="btn btn-secondary w-100 mt-3">
                <i class="fas fa-arrow-left"></i> Volver a Pedidos
            </a>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
function cambiarEstado(nuevoEstado) {
    $.post("index.php?action=pedidos_cambiarEstado", {
        id: ' . $pedido['id'] . ',
        estado: nuevoEstado
    }, function(response) {
        if (response.success) {
            Swal.fire("Éxito", response.message, "success")
                .then(() => location.reload());
        } else {
            Swal.fire("Error", response.message, "error");
        }
    }, "json");
}

function cancelarPedido() {
    Swal.fire({
        title: "¿Cancelar pedido?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("index.php?action=pedidos_cancelar&id=' . $pedido['id'] . '", function(response) {
                if (response.success) {
                    Swal.fire("Cancelado", response.message, "success")
                        .then(() => location.reload());
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            }, "json");
        }
    });
}
</script>
';
include __DIR__ . '/../layouts/footer.php';
?>
