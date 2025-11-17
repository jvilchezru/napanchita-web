<?php
$pageTitle = 'Crear Usuario';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-user-plus me-2"></i> Crear Nuevo Usuario</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=usuarios">Usuarios</a></li>
            <li class="breadcrumb-item active">Crear</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle me-2"></i> Datos del Nuevo Usuario
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=usuarios_guardar" method="POST" id="formCrearUsuario">
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
                                placeholder="Juan Pérez García"
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
                                placeholder="usuario@ejemplo.com">
                            <small class="form-text text-muted">Se usará para iniciar sesión</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i> Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required
                                    minlength="6"
                                    placeholder="Mínimo 6 caracteres">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">
                                <i class="fas fa-lock me-1"></i> Confirmar Contraseña <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control"
                                    id="password_confirm"
                                    name="password_confirm"
                                    required
                                    minlength="6"
                                    placeholder="Repetir contraseña">
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
                                <option value="admin">
                                    <i class="fas fa-user-shield"></i> Administrador (Acceso total)
                                </option>
                                <option value="mesero">
                                    <i class="fas fa-concierge-bell"></i> Mesero (Pedidos y mesas)
                                </option>
                                <option value="repartidor">
                                    <i class="fas fa-truck"></i> Repartidor (Solo deliveries)
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
                                placeholder="999 999 999"
                                pattern="[0-9]{9}"
                                maxlength="9">
                            <small class="form-text text-muted">9 dígitos</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i> Dirección
                        </label>
                        <textarea class="form-control"
                            id="direccion"
                            name="direccion"
                            rows="2"
                            placeholder="Dirección completa del usuario"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                type="checkbox"
                                id="estado"
                                name="estado"
                                value="activo"
                                checked>
                            <label class="form-check-label" for="estado">
                                <i class="fas fa-check-circle text-success me-1"></i> Usuario activo
                            </label>
                            <small class="form-text text-muted d-block">
                                Si está desactivado, el usuario no podrá iniciar sesión
                            </small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=usuarios" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ayuda -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-2"></i> Información sobre Roles:</h6>
                <ul class="mb-0">
                    <li><strong>Administrador:</strong> Acceso completo al sistema, puede gestionar usuarios, productos, reportes, etc.</li>
                    <li><strong>Mesero:</strong> Puede gestionar pedidos, mesas y reservas. No tiene acceso a configuración ni reportes.</li>
                    <li><strong>Repartidor:</strong> Solo puede ver y actualizar el estado de sus entregas asignadas.</li>
                </ul>
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
    document.getElementById("formCrearUsuario").addEventListener("submit", function(e) {
        const password = document.getElementById("password").value;
        const passwordConfirm = document.getElementById("password_confirm").value;
        
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