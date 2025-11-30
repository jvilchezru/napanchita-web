<?php
$pageTitle = 'Mi Perfil';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-user-circle me-2"></i> Mi Perfil</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Mi Perfil</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Información del Perfil -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i> Información Personal</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo BASE_URL; ?>index.php?action=perfil_actualizar">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">
                            <small class="text-muted">Opcional</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                            <small class="text-muted">Opcional</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol</label>
                            <input type="text" class="form-control" 
                                   value="<?php 
                                   $roles = [
                                       'admin' => 'Administrador',
                                       'mesero' => 'Mesero',
                                       'repartidor' => 'Repartidor'
                                   ];
                                   echo htmlspecialchars($roles[$usuario['rol']] ?? $usuario['rol'] ?? '');
                                   ?>" disabled>
                            <small class="text-muted">No se puede modificar</small>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambiar Contraseña -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i> Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                <form id="formCambiarPassword">
                    <div class="mb-3">
                        <label for="password_actual" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                        <input type="password" name="password_actual" id="password_actual" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_nueva" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="password_nueva" id="password_nueva" class="form-control" 
                               minlength="6" required>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmar" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmar" id="password_confirmar" class="form-control" 
                               minlength="6" required>
                    </div>

                    <button type="submit" class="btn btn-warning w-100" id="btnCambiarPassword">
                        <i class="fas fa-lock"></i> Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Información de Cuenta</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Fecha de Registro:</strong><br>
                <?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?></p>
                
                <p class="mb-2"><strong>Estado:</strong><br>
                <?php if ($usuario['activo']): ?>
                    <span class="badge bg-success">Activo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                <?php endif; ?>
                </p>

                <p class="mb-0"><strong>ID de Usuario:</strong><br>
                #<?php echo $usuario['id']; ?></p>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
$(document).ready(function() {
    // Cambiar contraseña con AJAX
    $("#formCambiarPassword").on("submit", function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const btn = $("#btnCambiarPassword");
        
        btn.prop("disabled", true).html("<i class=\"fas fa-spinner fa-spin\"></i> Cambiando...");
        
        $.ajax({
            url: "' . BASE_URL . 'index.php?action=perfil_cambiar_password",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Éxito!",
                        text: response.message,
                        confirmButtonText: "OK"
                    }).then(() => {
                        $("#formCambiarPassword")[0].reset();
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
                    text: "Error al cambiar la contraseña"
                });
            },
            complete: function() {
                btn.prop("disabled", false).html("<i class=\"fas fa-lock\"></i> Cambiar Contraseña");
            }
        });
    });
});
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>
