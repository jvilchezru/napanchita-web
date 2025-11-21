<?php
$pageTitle = 'Editar Mesa';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i> Editar Mesa</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=mesas">Mesas</a></li>
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
                <i class="fas fa-chair me-2"></i> Modificar Datos de la Mesa
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=mesas_actualizar" method="POST" id="formEditarMesa">
                    <input type="hidden" name="id" value="<?php echo $mesa['id']; ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i> Número de Mesa <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="numero" name="numero"
                                    value="<?php echo htmlspecialchars($mesa['numero']); ?>"
                                    placeholder="Ej: 1, 2, A1, VIP-1" required maxlength="10">
                                <small class="form-text text-muted">Identificador único de la mesa</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacidad" class="form-label">
                                    <i class="fas fa-users me-1"></i> Capacidad <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="capacidad" name="capacidad"
                                    value="<?php echo $mesa['capacidad']; ?>"
                                    min="1" max="20" placeholder="Número de personas" required>
                                <small class="form-text text-muted">Cantidad máxima de personas</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">
                            <i class="fas fa-info-circle me-1"></i> Estado Actual <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="disponible" <?php echo $mesa['estado'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                            <option value="ocupada" <?php echo $mesa['estado'] === 'ocupada' ? 'selected' : ''; ?>>Ocupada</option>
                            <option value="reservada" <?php echo $mesa['estado'] === 'reservada' ? 'selected' : ''; ?>>Reservada</option>
                            <option value="inactiva" <?php echo $mesa['estado'] === 'inactiva' ? 'selected' : ''; ?>>Inactiva</option>
                        </select>
                        <small class="form-text text-muted">Estado actual de la mesa</small>
                    </div>

                    <hr>
                    <h6><i class="fas fa-map-marker-alt me-2"></i> Posición en el Layout</h6>
                    <p class="text-muted small">También puedes ajustar la posición arrastrando la mesa en la vista gráfica</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posicion_x" class="form-label">
                                    <i class="fas fa-arrows-alt-h me-1"></i> Posición X (horizontal)
                                </label>
                                <input type="number" class="form-control" id="posicion_x" name="posicion_x"
                                    value="<?php echo $mesa['posicion_x']; ?>" min="0" max="1000">
                                <small class="form-text text-muted">Posición horizontal (píxeles)</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posicion_y" class="form-label">
                                    <i class="fas fa-arrows-alt-v me-1"></i> Posición Y (vertical)
                                </label>
                                <input type="number" class="form-control" id="posicion_y" name="posicion_y"
                                    value="<?php echo $mesa['posicion_y']; ?>" min="0" max="1000">
                                <small class="form-text text-muted">Posición vertical (píxeles)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                <?php
                                $activo = isset($mesa['activo']) ? (int)$mesa['activo'] : 0;
                                echo ($activo === 1) ? 'checked' : '';
                                ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle text-success me-1"></i> Mesa activa
                            </label>
                            <small class="form-text text-muted d-block">
                                Si está desactivada, no aparecerá en el layout
                            </small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=mesas" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Actualizar Mesa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-chart-bar me-2"></i> Información de la Mesa:</h6>
                <ul class="mb-0">
                    <li><strong>Tipo:</strong>
                        <?php if ($mesa['capacidad'] <= 4): ?>
                            <span class="badge bg-info">Mesa Pequeña (hasta 4 personas)</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Mesa Grande (más de 4 personas)</span>
                        <?php endif; ?>
                    </li>
                    <li><strong>Estado actual:</strong>
                        <span class="badge bg-<?php
                                                echo $mesa['estado'] === 'disponible' ? 'success' : ($mesa['estado'] === 'ocupada' ? 'danger' : ($mesa['estado'] === 'reservada' ? 'warning' : 'secondary'));
                                                ?>">
                            <?php echo ucfirst($mesa['estado']); ?>
                        </span>
                    </li>
                    <li><strong>ID en sistema:</strong> #<?php echo $mesa['id']; ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Validación del formulario
    document.getElementById("formEditarMesa").addEventListener("submit", function(e) {
        const numero = document.getElementById("numero").value.trim();
        const capacidad = parseInt(document.getElementById("capacidad").value);
        
        if (numero.length < 1) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El número de mesa es obligatorio"
            });
            return false;
        }
        
        if (capacidad < 1 || capacidad > 20) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "La capacidad debe estar entre 1 y 20 personas"
            });
            return false;
        }
    });

    // Sugerencias visuales según capacidad
    document.getElementById("capacidad").addEventListener("change", function() {
        const capacidad = parseInt(this.value);
        const capacidadAnterior = ' . $mesa['capacidad'] . ';
        
        if (capacidad !== capacidadAnterior) {
            let mensaje = "";
            
            if (capacidad <= 2) {
                mensaje = "💡 Mesa ideal para parejas o individuales";
            } else if (capacidad <= 4) {
                mensaje = "💡 Mesa pequeña - Perfecta para familias pequeñas";
            } else if (capacidad <= 6) {
                mensaje = "💡 Mesa mediana - Ideal para grupos";
            } else {
                mensaje = "💡 Mesa grande - Para grupos grandes o eventos";
            }
            
            if (mensaje) {
                Swal.fire({
                    icon: "info",
                    title: "Cambio de capacidad",
                    text: mensaje,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: "top-end"
                });
            }
        }
    });

    // Advertencia al cambiar estado a inactiva
    document.getElementById("estado").addEventListener("change", function() {
        if (this.value === "inactiva") {
            Swal.fire({
                icon: "warning",
                title: "Atención",
                text: "La mesa aparecerá como no disponible en el layout",
                showConfirmButton: true
            });
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>