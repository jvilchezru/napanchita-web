<?php
$pageTitle = 'Editar Usuario';
include __DIR__ . '/../layouts/header.php';

// Los datos del usuario deberían venir del controlador
// Por ahora usamos un placeholder para la estructura
?>

<div class="page-header">
    <h1><i class="fas fa-user-edit me-2"></i> Editar Usuario</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=usuarios">Usuarios</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle me-2"></i> Modificar Datos del Usuario
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=usuarios_actualizar" method="POST" id="formEditarUsuario">
                    <input type="hidden" name="id" value="<?php echo $usuario['id'] ?? ''; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-user me-1"></i> Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                required
                                value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>"
                                minlength="3"
                                maxlength="100">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                required
                                value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Contraseña:</strong> Deje los campos en blanco si no desea cambiarla
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i> Nueva Contraseña
                            </label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    minlength="6"
                                    placeholder="Dejar en blanco para mantener">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">
                                <i class="fas fa-lock me-1"></i> Confirmar Contraseña
                            </label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control"
                                    id="password_confirm"
                                    name="password_confirm"
                                    minlength="6"
                                    placeholder="Repetir nueva contraseña">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label">
                                <i class="fas fa-user-tag me-1"></i> Rol <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin" <?php echo (isset($usuario['rol']) && $usuario['rol'] == 'admin') ? 'selected' : ''; ?>>
                                    Administrador
                                </option>
                                <option value="mesero" <?php echo (isset($usuario['rol']) && $usuario['rol'] == 'mesero') ? 'selected' : ''; ?>>
                                    Mesero
                                </option>
                                <option value="repartidor" <?php echo (isset($usuario['rol']) && $usuario['rol'] == 'repartidor') ? 'selected' : ''; ?>>
                                    Repartidor
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">
                                <i class="fas fa-phone me-1"></i> Teléfono
                            </label>
                            <input type="tel"
                                class="form-control"
                                id="telefono"
                                name="telefono"
                                value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"
                                pattern="[0-9]{9}"
                                maxlength="9">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i> Dirección
                        </label>
                        <textarea class="form-control"
                            id="direccion"
                            name="direccion"
                            rows="2"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                id="activo"
                                name="activo"
                                value="1"
                                <?php echo (isset($usuario['activo']) && $usuario['activo']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle text-success me-1"></i> Usuario activo
                            </label>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=usuarios" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Toggle password visibility
    document.getElementById("togglePassword").addEventListener("click", function() {
        const passwordInput = document.getElementById("password");
        const icon = this.querySelector("i");
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
    
    document.getElementById("togglePasswordConfirm").addEventListener("click", function() {
        const passwordInput = document.getElementById("password_confirm");
        const icon = this.querySelector("i");
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
    
    // Validación del formulario
    document.getElementById("formEditarUsuario").addEventListener("submit", function(e) {
        const password = document.getElementById("password").value;
        const passwordConfirm = document.getElementById("password_confirm").value;
        
        // Solo validar si se ingresó una nueva contraseña
        if (password || passwordConfirm) {
            if (password !== passwordConfirm) {
                e.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Las contraseñas no coinciden"
                });
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "La contraseña debe tener al menos 6 caracteres"
                });
                return false;
            }
        }
        
        const telefono = document.getElementById("telefono").value;
        if (telefono && telefono.length !== 9) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El teléfono debe tener exactamente 9 dígitos"
            });
            return false;
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>