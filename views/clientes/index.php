<?php
$pageTitle = 'Gestión de Clientes';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-users me-2"></i> Gestión de Clientes</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Clientes</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php?action=clientes_crear" class="btn btn-success">
                <i class="fas fa-plus-circle me-2"></i> Nuevo Cliente
            </a>
        </div>
    </div>
</div>

<!-- Mensajes Flash -->
<?php if (has_flash_message()): ?>
    <?php $flash = get_flash_message(); ?>
    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
        <i class="fas fa-<?php echo $flash['type'] === 'error' ? 'exclamation-circle' : ($flash['type'] === 'success' ? 'check-circle' : 'info-circle'); ?> me-2"></i>
        <?php echo $flash['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tabla de Clientes -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i> Listado de Clientes
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaClientes">
                <thead class="table-primary">
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td>
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong>
                            </td>
                            <td>
                                <i class="fas fa-phone text-success me-2"></i>
                                <a href="tel:<?php echo $cliente['telefono']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($cliente['telefono']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($cliente['email'])): ?>
                                    <i class="fas fa-envelope text-info me-2"></i>
                                    <a href="mailto:<?php echo $cliente['email']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($cliente['email']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <i class="fas fa-calendar text-secondary me-2"></i>
                                <?php echo date('d/m/Y', strtotime($cliente['fecha_registro'])); ?>
                            </td>
                            <td>
                                <?php if ($cliente['activo']): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>index.php?action=clientes_editar&id=<?php echo $cliente['id']; ?>"
                                    class="btn btn-sm btn-outline-warning"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($cliente['activo']): ?>
                                    <a href="<?php echo BASE_URL; ?>index.php?action=clientes_eliminar&id=<?php echo $cliente['id']; ?>"
                                        class="btn btn-sm btn-outline-danger btn-eliminar"
                                        title="Desactivar"
                                        onclick="return confirm('¿Estás seguro de desactivar este cliente?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Información adicional -->
<div class="card mt-3">
    <div class="card-body">
        <h6><i class="fas fa-info-circle me-2"></i> Información:</h6>
        <ul class="mb-0">
            <li><strong>Total de clientes:</strong> <?php echo count($clientes); ?></li>
            <li><strong>Clientes activos:</strong> <?php echo count(array_filter($clientes, fn($c) => $c['activo'])); ?></li>
            <li>Los clientes son personas que realizan pedidos en el restaurante (sin acceso al sistema).</li>
            <li>Puedes buscar clientes por nombre, teléfono o email usando el cuadro de búsqueda.</li>
            <li>Al desactivar un cliente, este no se elimina, solo se marca como inactivo.</li>
        </ul>
    </div>
</div>

<?php
$extraScripts = '
<script>
$(document).ready(function() {
    // Inicializar DataTables
    $("#tablaClientes").DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        order: [[0, "asc"]],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: 5 }
        ]
    });

    // Confirmación de eliminación con SweetAlert
    $(".btn-eliminar").click(function(e) {
        e.preventDefault();
        const url = $(this).attr("href");
        
        Swal.fire({
            title: "¿Desactivar cliente?",
            text: "El cliente no podrá realizar nuevos pedidos hasta que se reactive",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, desactivar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>