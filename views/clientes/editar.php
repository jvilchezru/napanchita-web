<?php
$pageTitle = 'Editar Cliente';
include __DIR__ . '/../layouts/header.php';

// Obtener dirección principal si existe
$direccion_principal = null;
$referencia_principal = '';
if (!empty($cliente['direcciones'])) {
    foreach ($cliente['direcciones'] as $dir) {
        if (isset($dir['principal']) && $dir['principal']) {
            $direccion_principal = $dir['direccion'] ?? '';
            $referencia_principal = $dir['referencia'] ?? '';
            break;
        }
    }
    // Si no hay dirección marcada como principal, tomar la primera
    if (!$direccion_principal && count($cliente['direcciones']) > 0) {
        $direccion_principal = $cliente['direcciones'][0]['direccion'] ?? '';
        $referencia_principal = $cliente['direcciones'][0]['referencia'] ?? '';
    }
}
?>

<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i> Editar Cliente</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=clientes">Clientes</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>
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

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user me-2"></i> Modificar Datos del Cliente
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=clientes_actualizar" method="POST" id="formEditarCliente">
                    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">

                    <h6 class="mb-3"><i class="fas fa-id-card me-2"></i> Información Personal</h6>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-user me-1"></i> Nombre Completo <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                            placeholder="Ej: Juan Pérez García" required maxlength="100">
                        <small class="form-text text-muted">Nombre completo del cliente</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-1"></i> Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                    value="<?php echo htmlspecialchars($cliente['telefono']); ?>"
                                    placeholder="Ej: 987654321" required maxlength="20">
                                <small class="form-text text-muted">Número de contacto principal</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>"
                                    placeholder="ejemplo@correo.com" maxlength="100">
                                <small class="form-text text-muted">Correo electrónico (opcional)</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> Dirección Principal</h6>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-home me-1"></i> Dirección
                        </label>
                        <input type="text" class="form-control" id="direccion" name="direccion"
                            value="<?php echo htmlspecialchars($direccion_principal ?? ''); ?>"
                            placeholder="Ej: Av. Principal 123, San Isidro">
                        <small class="form-text text-muted">Dirección para entregas a domicilio</small>
                    </div>

                    <div class="mb-3">
                        <label for="referencia" class="form-label">
                            <i class="fas fa-info-circle me-1"></i> Referencia
                        </label>
                        <input type="text" class="form-control" id="referencia" name="referencia"
                            value="<?php echo htmlspecialchars($referencia_principal); ?>"
                            placeholder="Ej: Casa blanca con portón verde, frente al parque">
                        <small class="form-text text-muted">Punto de referencia para encontrar la dirección</small>
                    </div>

                    <?php if (!empty($cliente['direcciones']) && count($cliente['direcciones']) > 1): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Este cliente tiene <?php echo count($cliente['direcciones']); ?> direcciones registradas.
                            Puedes gestionar todas desde el módulo de pedidos.
                        </div>
                    <?php endif; ?>

                    <hr>

                    <h6 class="mb-3"><i class="fas fa-sticky-note me-2"></i> Información Adicional</h6>

                    <div class="mb-3">
                        <label for="notas" class="form-label">
                            <i class="fas fa-comment-alt me-1"></i> Notas
                        </label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"
                            placeholder="Observaciones, preferencias del cliente, alergias, etc."><?php echo htmlspecialchars($cliente['notas'] ?? ''); ?></textarea>
                        <small class="form-text text-muted">Información relevante sobre el cliente</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                <?php
                                $activo = isset($cliente['activo']) ? (int)$cliente['activo'] : 0;
                                echo ($activo === 1) ? 'checked' : '';
                                ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle text-success me-1"></i> Cliente activo
                            </label>
                            <small class="form-text text-muted d-block">
                                Si está desactivado, no podrá realizar nuevos pedidos
                            </small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=clientes" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Actualizar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-chart-bar me-2"></i> Información del Cliente:</h6>
                <ul class="mb-0">
                    <li><strong>ID:</strong> #<?php echo $cliente['id']; ?></li>
                    <li><strong>Fecha de registro:</strong> <?php echo date('d/m/Y H:i', strtotime($cliente['fecha_registro'])); ?></li>
                    <li><strong>Estado:</strong>
                        <?php if ($cliente['activo']): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </li>
                    <li><strong>Direcciones registradas:</strong> <?php echo count($cliente['direcciones'] ?? []); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Validación del formulario
    document.getElementById("formEditarCliente").addEventListener("submit", function(e) {
        const nombre = document.getElementById("nombre").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const email = document.getElementById("email").value.trim();
        
        if (nombre.length < 3) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El nombre debe tener al menos 3 caracteres"
            });
            return false;
        }
        
        if (telefono.length < 7) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El teléfono debe tener al menos 7 dígitos"
            });
            return false;
        }

        // Validar formato de email si se ingresó
        if (email && !validateEmail(email)) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El email no tiene un formato válido"
            });
            return false;
        }
    });

    // Función para validar email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Auto-formateo del teléfono (solo números)
    document.getElementById("telefono").addEventListener("input", function(e) {
        this.value = this.value.replace(/[^0-9+]/g, "");
    });

    // Advertencia al desactivar
    document.getElementById("activo").addEventListener("change", function() {
        if (!this.checked) {
            Swal.fire({
                icon: "warning",
                title: "Atención",
                text: "Al desactivar el cliente, no podrá realizar nuevos pedidos",
                showConfirmButton: true
            });
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>