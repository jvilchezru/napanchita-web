<?php
$pageTitle = 'Métodos de Pago';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-credit-card me-2"></i> Métodos de Pago</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Métodos de Pago</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Métodos de Pago</h5>
            <a href="<?php echo BASE_URL; ?>index.php?action=metodos_pago_crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Método de Pago
            </a>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" action="<?php echo BASE_URL; ?>index.php" class="row mb-3">
                <input type="hidden" name="action" value="metodos_pago">
                <div class="col-md-4">
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar por nombre..." 
                           value="<?php echo $_GET['buscar'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" <?php echo (isset($_GET['activo']) && $_GET['activo'] == '1') ? 'selected' : ''; ?>>Activos</option>
                        <option value="0" <?php echo (isset($_GET['activo']) && $_GET['activo'] == '0') ? 'selected' : ''; ?>>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?php echo BASE_URL; ?>index.php?action=metodos_pago" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>

            <table id="tablaMetodosPago" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($metodosPago)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay métodos de pago registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($metodosPago as $metodo): ?>
                            <tr>
                                <td><?php echo $metodo['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($metodo['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($metodo['descripcion'] ?? '-'); ?></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="activo<?php echo $metodo['id']; ?>"
                                               <?php echo $metodo['activo'] ? 'checked' : ''; ?>
                                               onchange="cambiarEstado(<?php echo $metodo['id']; ?>, this.checked)">
                                        <label class="form-check-label" for="activo<?php echo $metodo['id']; ?>">
                                            <span class="badge bg-<?php echo $metodo['activo'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $metodo['activo'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo BASE_URL; ?>index.php?action=metodos_pago_editar&id=<?php echo $metodo['id']; ?>" 
                                           class="btn btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmarEliminar(<?php echo $metodo['id']; ?>)" 
                                                class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaMetodosPago').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[1, 'asc']]
    });
});

function cambiarEstado(id, activo) {
    $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?action=metodos_pago_cambiarEstado',
        method: 'POST',
        data: {
            id: id,
            activo: activo ? 1 : 0
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
                // Revertir el switch
                $('#activo' + id).prop('checked', !activo);
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al cambiar el estado', 'error');
            $('#activo' + id).prop('checked', !activo);
        }
    });
}

function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción eliminará permanentemente el método de pago del sistema y de la base de datos. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar permanentemente',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo BASE_URL; ?>index.php?action=metodos_pago_eliminar&id=' + id;
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
