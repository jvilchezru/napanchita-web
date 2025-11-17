<?php
$pageTitle = 'Combos';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .badge-activo {
        background-color: #28a745 !important;
        color: white;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }

    .badge-inactivo {
        background-color: #dc3545 !important;
        color: white;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }

    .combo-imagen {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-box-open me-2"></i> Gestión de Combos</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Combos</li>
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
                    <h5 class="mb-0"><i class="fas fa-list"></i> Listado de Combos</h5>
                </div>
                <div class="col-auto">
                    <a href="<?php echo BASE_URL; ?>index.php?action=combos_crear" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Combo
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="tablaCombos" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($combos as $combo): ?>
                        <tr>
                            <td>
                                <?php if (!empty($combo['imagen_url']) && file_exists($combo['imagen_url'])): ?>
                                    <img src="<?php echo BASE_URL . $combo['imagen_url']; ?>"
                                        alt="<?php echo htmlspecialchars($combo['nombre']); ?>"
                                        class="combo-imagen">
                                <?php else: ?>
                                    <div class="combo-imagen bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $combo['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($combo['nombre']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($combo['descripcion'] ?? '', 0, 50)); ?></small>
                            </td>
                            <td><strong>S/ <?php echo number_format($combo['precio'], 2); ?></strong></td>
                            <td>
                                <span class="badge bg-info"><?php echo $combo['cantidad_productos']; ?> productos</span>
                                <?php if (!empty($combo['productos'])): ?>
                                    <br>
                                    <small>
                                        <?php
                                        $nombres = array_slice(array_map(function ($p) {
                                            return $p['nombre'];
                                        }, $combo['productos']), 0, 2);
                                        echo htmlspecialchars(implode(', ', $nombres));
                                        if (count($combo['productos']) > 2) echo '...';
                                        ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $combo['activo'] ? 'badge-activo' : 'badge-inactivo'; ?>">
                                    <?php echo $combo['activo'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo BASE_URL; ?>index.php?action=combos_editar&id=<?php echo $combo['id']; ?>"
                                        class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="cambiarEstado(<?php echo $combo['id']; ?>, <?php echo $combo['activo'] ? 0 : 1; ?>)"
                                        class="btn btn-info" title="Cambiar Estado">
                                        <i class="fas fa-<?php echo $combo['activo'] ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                    </button>
                                    <button onclick="eliminarCombo(<?php echo $combo['id']; ?>)"
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
        $('#tablaCombos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [
                [2, 'asc']
            ] // Ordenar por nombre
        });
    });

    function cambiarEstado(id, estado) {
        const mensaje = estado === 1 ? 'activar' : 'desactivar';

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas ${mensaje} este combo?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>index.php?action=combos_cambiar_estado',
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

    function eliminarCombo(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el combo, su imagen y las asociaciones con productos. No se puede revertir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>index.php?action=combos_eliminar',
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
                        Swal.fire('Error', 'Error al eliminar el combo', 'error');
                    }
                });
            }
        });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>