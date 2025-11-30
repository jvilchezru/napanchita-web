<?php
$pageTitle = 'Gestión de Pedidos';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-shopping-cart me-2"></i> Gestión de Pedidos</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Pedidos</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-3">
        <div class="col-auto">
            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_crear" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Pedido
            </a>
        </div>
        <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
        <div class="col-auto">
            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_cocina" class="btn btn-primary">
                <i class="fas fa-utensils"></i> Vista de Cocina
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="action" value="pedidos">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="mesa" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'mesa') ? 'selected' : ''; ?>>Mesa</option>
                        <option value="delivery" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'delivery') ? 'selected' : ''; ?>>Delivery</option>
                        <option value="para_llevar" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'para_llevar') ? 'selected' : ''; ?>>Para Llevar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en_preparacion" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'en_preparacion') ? 'selected' : ''; ?>>En Preparación</option>
                        <option value="listo" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'listo') ? 'selected' : ''; ?>>Listo</option>
                        <option value="entregado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'entregado') ? 'selected' : ''; ?>>Entregado</option>
                        <option value="finalizado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                        <option value="cancelado" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo $_GET['fecha'] ?? date('Y-m-d'); ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo BASE_URL; ?>index.php?action=pedidos" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de pedidos -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Listado de Pedidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaPedidos">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Cliente/Mesa</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pedidos)): ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                    <td>
                                        <?php
                                        $tipo_badges = [
                                            'mesa' => '<span class="badge bg-info"><i class="fas fa-chair"></i> Mesa</span>',
                                            'delivery' => '<span class="badge bg-success"><i class="fas fa-truck"></i> Delivery</span>',
                                            'para_llevar' => '<span class="badge bg-warning"><i class="fas fa-shopping-bag"></i> Para Llevar</span>'
                                        ];
                                        echo $tipo_badges[$pedido['tipo']] ?? $pedido['tipo'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($pedido['tipo'] == 'mesa'): ?>
                                            Mesa <?php echo $pedido['mesa_numero'] ?? 'N/A'; ?>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'Sin cliente'); ?>
                                            <br><small class="text-muted"><?php echo $pedido['cliente_telefono'] ?? ''; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $pedido['total_items']; ?> items</td>
                                    <td><strong>S/ <?php echo number_format($pedido['total'], 2); ?></strong></td>
                                    <td>
                                        <?php
                                        $estado_badges = [
                                            'pendiente' => 'warning',
                                            'en_preparacion' => 'info',
                                            'listo' => 'primary',
                                            'entregado' => 'success',
                                            'finalizado' => 'dark',
                                            'cancelado' => 'danger'
                                        ];
                                        $estado_textos = [
                                            'pendiente' => 'Pendiente',
                                            'en_preparacion' => 'En Preparación',
                                            'listo' => 'Listo',
                                            'entregado' => 'Entregado',
                                            'finalizado' => 'Finalizado',
                                            'cancelado' => 'Cancelado'
                                        ];
                                        $badge_class = $estado_badges[$pedido['estado']] ?? 'secondary';
                                        $estado_texto = $estado_textos[$pedido['estado']] ?? $pedido['estado'];
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>">
                                            <?php echo $estado_texto; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_ver&id=<?php echo $pedido['id']; ?>&t=<?php echo time(); ?>" 
                                               class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($pedido['estado'] == 'pendiente'): ?>
                                                <button class="btn btn-sm btn-danger btn-cancelar" 
                                                        data-id="<?php echo $pedido['id']; ?>" title="Cancelar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                    <p>No hay pedidos registrados</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
$(document).ready(function() {
    // Solo inicializar DataTable si hay datos
    <?php if (!empty($pedidos)): ?>
    $("#tablaPedidos").DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        order: [[0, "desc"]],
        pageLength: 25
    });
    <?php endif; ?>

    $(".btn-cancelar").on("click", function() {
        const id = $(this).data("id");
        
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
                $.post("index.php?action=pedidos_cancelar&id=" + id, function(response) {
                    if (response.success) {
                        Swal.fire("Cancelado", response.message, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                }, "json");
            }
        });
    });
});
</script>
';
include __DIR__ . '/../layouts/footer.php';
?>
