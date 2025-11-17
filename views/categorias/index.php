<?php
$pageTitle = 'Categorías';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .badge-activa {
        background-color: #28a745 !important;
        color: white;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }

    .badge-inactiva {
        background-color: #dc3545 !important;
        color: white;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-tags me-2"></i> Gestión de Categorías</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Categorías</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <!-- Mensajes Flash -->
    <?php if (has_flash_message()): ?>
        <?php $flash = get_flash_message(); ?>
        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Listado de Categorías</h5>
                </div>
                <div class="col-auto">
                    <a href="<?php echo BASE_URL; ?>index.php?action=categorias_crear" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nueva Categoría
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="tablaCategorias" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($cat['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($cat['descripcion'] ?? '', 0, 50)); ?></td>
                            <td><?php echo $cat['orden']; ?></td>
                            <td>
                                <span class="badge <?php echo ($cat['activo'] ?? 1) ? 'badge-activa' : 'badge-inactiva'; ?>">
                                    <?php echo ($cat['activo'] ?? 1) ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </td>
                            <td><?php echo $cat['cantidad_productos'] ?? 0; ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo BASE_URL; ?>index.php?action=categorias_editar&id=<?php echo $cat['id']; ?>"
                                        class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="cambiarEstado(<?php echo $cat['id']; ?>, <?php echo ($cat['activo'] ?? 1) ? 0 : 1; ?>)"
                                        class="btn btn-info" title="Cambiar Estado">
                                        <i class="fas fa-<?php echo ($cat['activo'] ?? 1) ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                    </button>
                                    <button onclick="eliminarCategoria(<?php echo $cat['id']; ?>)"
                                        class="btn btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tablaCategorias').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [
                [3, 'asc']
            ] // Ordenar por orden
        });
    });

    function cambiarEstado(id, estado) {
        const mensaje = estado === 1 ? 'activar' : 'desactivar';

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas ${mensaje} esta categoría?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>index.php?action=categorias_cambiar_estado',
                    method: 'POST',
                    data: {
                        id: id,
                        estado: estado
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al cambiar el estado', 'error');
                    }
                });
            }
        });
    }

    function eliminarCategoria(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede revertir',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>index.php?action=categorias_eliminar',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('¡Eliminado!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al eliminar la categoría', 'error');
                    }
                });
            }
        });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>