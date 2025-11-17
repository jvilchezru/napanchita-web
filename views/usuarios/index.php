<?php
$pageTitle = 'Gestión de Usuarios';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-users-cog me-2"></i> Gestión de Usuarios</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php?action=usuarios_crear" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Usuario
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i> Lista de Usuarios del Sistema
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filtroRol" class="form-label">Filtrar por Rol:</label>
                <select class="form-select" id="filtroRol">
                    <option value="">Todos los roles</option>
                    <option value="admin">Administrador</option>
                    <option value="mesero">Mesero</option>
                    <option value="repartidor">Repartidor</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filtroEstado" class="form-label">Filtrar por Estado:</label>
                <select class="form-select" id="filtroEstado">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-secondary w-100" id="btnLimpiarFiltros">
                    <i class="fas fa-times me-2"></i> Limpiar Filtros
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($usuarios) && count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <?php
                                    $badgeClass = '';
                                    $iconClass = '';
                                    switch ($usuario['rol']) {
                                        case 'admin':
                                            $badgeClass = 'bg-danger';
                                            $iconClass = 'fa-user-shield';
                                            $rolText = 'Administrador';
                                            break;
                                        case 'mesero':
                                            $badgeClass = 'bg-primary';
                                            $iconClass = 'fa-concierge-bell';
                                            $rolText = 'Mesero';
                                            break;
                                        case 'repartidor':
                                            $badgeClass = 'bg-success';
                                            $iconClass = 'fa-truck';
                                            $rolText = 'Repartidor';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                            $iconClass = 'fa-user';
                                            $rolText = ucfirst($usuario['rol']);
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <i class="fas <?php echo $iconClass; ?> me-1"></i>
                                        <?php echo $rolText; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['telefono'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($usuario['activo']): ?>
                                        <span class="badge badge-activo">
                                            <i class="fas fa-check-circle me-1"></i> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-inactivo">
                                            <i class="fas fa-times-circle me-1"></i> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo BASE_URL; ?>index.php?action=usuarios_editar&id=<?php echo $usuario['id']; ?>"
                                            class="btn btn-sm btn-info"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                            <button type="button"
                                                class="btn btn-sm btn-warning btn-cambiar-estado"
                                                data-id="<?php echo $usuario['id']; ?>"
                                                data-estado="<?php echo $usuario['activo']; ?>"
                                                title="<?php echo $usuario['activo'] ? 'Desactivar' : 'Activar'; ?>">
                                                <i class="fas fa-<?php echo $usuario['activo'] ? 'ban' : 'check'; ?>"></i>
                                            </button>

                                            <a href="<?php echo BASE_URL; ?>index.php?action=usuarios_eliminar&id=<?php echo $usuario['id']; ?>"
                                                class="btn btn-sm btn-danger btn-delete"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-secondary" disabled title="No puedes modificar tu propio usuario">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>No hay usuarios registrados</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    $(document).ready(function() {
        // DataTable
        const table = $("#tablaUsuarios").DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            order: [[0, "desc"]],
            pageLength: 25,
            responsive: true
        });
        
        // Variable para almacenar el índice del filtro
        let filtroIndex = null;
        
        // Filtros personalizados
        $("#filtroRol, #filtroEstado").on("change", function() {
            const rol = $("#filtroRol").val();
            const estado = $("#filtroEstado").val();
            
            // Remover filtro anterior si existe
            if (filtroIndex !== null) {
                $.fn.dataTable.ext.search.splice(filtroIndex, 1);
                filtroIndex = null;
            }
            
            // Agregar nuevo filtro si hay criterios
            if (rol || estado) {
                filtroIndex = $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        let cumpleRol = true;
                        let cumpleEstado = true;
                        
                        // Filtro de rol (columna 3)
                        if (rol) {
                            cumpleRol = data[3].toLowerCase().indexOf(rol.toLowerCase()) !== -1;
                        }
                        
                        // Filtro de estado (columna 5)
                        if (estado === "activo") {
                            cumpleEstado = data[5].indexOf("Activo") !== -1;
                        } else if (estado === "inactivo") {
                            cumpleEstado = data[5].indexOf("Inactivo") !== -1;
                        }
                        
                        return cumpleRol && cumpleEstado;
                    }
                ) - 1; // Restar 1 porque push devuelve la longitud
            }
            
            table.draw();
        });
        
        // Limpiar filtros
        $("#btnLimpiarFiltros").on("click", function() {
            $("#filtroRol, #filtroEstado").val("");
            
            // Remover filtro personalizado si existe
            if (filtroIndex !== null) {
                $.fn.dataTable.ext.search.splice(filtroIndex, 1);
                filtroIndex = null;
            }
            
            table.search("").columns().search("").draw();
        });
        
        // Cambiar estado
        $(".btn-cambiar-estado").on("click", function() {
            const btn = $(this);
            const id = btn.data("id");
            const estadoActual = btn.data("estado"); // 1 = activo, 0 = inactivo
            const accion = estadoActual ? "desactivar" : "activar";
            const mensajeAccion = estadoActual ? "desactivará" : "activará";
            
            Swal.fire({
                title: "¿Está seguro?",
                text: `Se ${mensajeAccion} este usuario`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, " + accion,
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hacer petición AJAX
                    $.ajax({
                        url: "index.php?action=usuarios_cambiar_estado&id=" + id,
                        type: "POST",
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "¡Éxito!",
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Error al cambiar el estado del usuario"
                            });
                        }
                    });
                }
            });
        });
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>