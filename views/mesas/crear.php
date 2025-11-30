<?php
$pageTitle = 'Crear Mesa';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-plus-circle me-2"></i> Crear Nueva Mesa</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=mesas">Mesas</a></li>
            <li class="breadcrumb-item active">Crear</li>
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
                <i class="fas fa-chair me-2"></i> Datos de la Nueva Mesa
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=mesas_guardar" method="POST" id="formCrearMesa">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i> Número de Mesa <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="numero" name="numero"
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
                                    min="1" max="20" placeholder="Número de personas" required>
                                <small class="form-text text-muted">Cantidad máxima de personas</small>
                            </div>
                        </div>
                    </div>

                    <!-- Estado siempre disponible al crear -->
                    <input type="hidden" name="estado" value="disponible">

                    <hr>
                    <h6><i class="fas fa-map-marker-alt me-2"></i> Posición en el Layout</h6>
                    <p class="text-muted small">Puedes ajustar la posición después desde la vista gráfica</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posicion_x" class="form-label">
                                    <i class="fas fa-arrows-alt-h me-1"></i> Posición X (horizontal)
                                </label>
                                <input type="number" class="form-control" id="posicion_x" name="posicion_x"
                                    value="50" min="0" max="1000">
                                <small class="form-text text-muted">Posición horizontal (píxeles)</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posicion_y" class="form-label">
                                    <i class="fas fa-arrows-alt-v me-1"></i> Posición Y (vertical)
                                </label>
                                <input type="number" class="form-control" id="posicion_y" name="posicion_y"
                                    value="50" min="0" max="1000">
                                <small class="form-text text-muted">Posición vertical (píxeles)</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
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
                            <i class="fas fa-save me-2"></i> Guardar Mesa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ayuda -->
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-2"></i> Consejos:</h6>
                <ul class="mb-0">
                    <li><strong>Número:</strong> Usa identificadores claros y fáciles de recordar (1, 2, A1, VIP-1).</li>
                    <li><strong>Capacidad:</strong> Indica el número máximo de comensales que puede acomodar la mesa.</li>
                    <li><strong>Estado:</strong> Normalmente las mesas nuevas se crean como "Disponible".</li>
                    <li><strong>Posición:</strong> Puedes arrastrar las mesas en la vista gráfica para organizar el layout del restaurante.</li>
                    <li><strong>Mesas pequeñas:</strong> Se consideran "pequeñas" las mesas de hasta 4 personas (icono 🪑).</li>
                    <li><strong>Mesas grandes:</strong> Las mesas de más de 4 personas se muestran con icono especial (🍽️).</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Validación del formulario
    document.getElementById("formCrearMesa").addEventListener("submit", function(e) {
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
                title: "Sugerencia",
                text: mensaje,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: "top-end"
            });
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>