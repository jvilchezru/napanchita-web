<?php
$pageTitle = 'Configuración del Sistema';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-cog me-2"></i> Configuración del Sistema</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Configuración</li>
        </ol>
    </nav>
</div>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $_SESSION['tipo_mensaje'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
        <?php 
        echo $_SESSION['mensaje']; 
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form action="<?php echo BASE_URL; ?>index.php?action=configuracion_guardar" method="POST" id="formConfiguracion">
    <div class="row">
        <!-- Información General -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle me-2"></i> Información General
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nombre_restaurante" class="form-label">Nombre del Restaurante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_restaurante" name="nombre_restaurante" 
                               value="<?php echo htmlspecialchars($config['nombre_restaurante'] ?? 'Cevichería Ñapanchita'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="ruc" class="form-label">RUC</label>
                        <input type="text" class="form-control" id="ruc" name="ruc" 
                               value="<?php echo htmlspecialchars($config['ruc'] ?? ''); ?>" maxlength="11">
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2" required><?php echo htmlspecialchars($config['direccion'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               value="<?php echo htmlspecialchars($config['telefono'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($config['email'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo del Restaurante</label>
                        <div class="mb-2">
                            <?php if (!empty($config['logo'])): ?>
                                <img src="<?php echo BASE_URL; ?>public/images/<?php echo htmlspecialchars($config['logo']); ?>" 
                                     alt="Logo" id="logoPreview" style="max-width: 200px; max-height: 100px;">
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>public/images/logo.png" 
                                     alt="Logo" id="logoPreview" style="max-width: 200px; max-height: 100px;">
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuraciones de Operación -->
        <div class="col-md-6 mb-4">
            <?php /* TEMPORALMENTE OCULTO - MÓDULO DELIVERY
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-motorcycle me-2"></i> Delivery
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="costo_delivery" class="form-label">Costo de Delivery (S/)</label>
                        <input type="number" class="form-control" id="costo_delivery" name="costo_delivery" 
                               value="<?php echo htmlspecialchars($config['costo_delivery'] ?? '5.00'); ?>" 
                               step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="monto_minimo_delivery" class="form-label">Monto Mínimo para Delivery (S/)</label>
                        <input type="number" class="form-control" id="monto_minimo_delivery" name="monto_minimo_delivery" 
                               value="<?php echo htmlspecialchars($config['monto_minimo_delivery'] ?? '20.00'); ?>" 
                               step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="tiempo_preparacion" class="form-label">Tiempo Estimado de Preparación (minutos)</label>
                        <input type="number" class="form-control" id="tiempo_preparacion" name="tiempo_preparacion" 
                               value="<?php echo htmlspecialchars($config['tiempo_preparacion'] ?? '30'); ?>" 
                               min="1">
                    </div>
                </div>
            </div>
            */ ?>

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-calendar-check me-2"></i> Reservas
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tiempo_max_reserva" class="form-label">Tiempo Máximo de Reserva (horas)</label>
                        <input type="number" class="form-control" id="tiempo_max_reserva" name="tiempo_max_reserva" 
                               value="<?php echo htmlspecialchars($config['tiempo_max_reserva'] ?? '2'); ?>" 
                               min="1">
                        <small class="text-muted">Tiempo que se mantiene la mesa reservada</small>
                    </div>

                    <div class="mb-3">
                        <label for="anticipacion_minima_reserva" class="form-label">Anticipación Mínima (horas)</label>
                        <input type="number" class="form-control" id="anticipacion_minima_reserva" name="anticipacion_minima_reserva" 
                               value="<?php echo htmlspecialchars($config['anticipacion_minima_reserva'] ?? '1'); ?>" 
                               min="0">
                        <small class="text-muted">Horas de anticipación para crear una reserva</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-receipt me-2"></i> Impuestos y Facturación
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="igv" class="form-label">IGV (%)</label>
                        <input type="number" class="form-control" id="igv" name="igv" 
                               value="<?php echo htmlspecialchars($config['igv'] ?? '18'); ?>" 
                               step="0.01" min="0" max="100">
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="aplicar_igv" name="aplicar_igv" value="1"
                               <?php echo (isset($config['aplicar_igv']) && $config['aplicar_igv'] == '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="aplicar_igv">
                            Aplicar IGV a todos los productos
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuraciones del Sistema -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-tools me-2"></i> Sistema
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="modo_mantenimiento" name="modo_mantenimiento" value="1"
                                       <?php echo (isset($config['modo_mantenimiento']) && $config['modo_mantenimiento'] == '1') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="modo_mantenimiento">
                                    <strong>Modo Mantenimiento</strong>
                                    <br><small class="text-muted">Desactiva el sistema temporalmente para mantenimiento</small>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Versión del Sistema:</strong> <?php echo APP_VERSION; ?>
                                <br><small>Última actualización: <?php echo date('d/m/Y'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-end">
                    <a href="<?php echo BASE_URL; ?>index.php?action=dashboard" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Guardar Configuración
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$extraScripts = '
<script>
    // Preview del logo
    document.getElementById("logo").addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: "error",
                    title: "Archivo muy grande",
                    text: "El logo no debe superar 2MB"
                });
                e.target.value = "";
                return;
            }

            // Preview
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById("logoPreview").src = event.target.result;
            };
            reader.readAsDataURL(file);

            // Subir con AJAX
            const formData = new FormData();
            formData.append("logo", file);

            fetch("' . BASE_URL . 'index.php?action=configuracion_subir_logo", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Logo actualizado",
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Error al subir el logo"
                });
            });
        }
    });

    // Validación del formulario
    document.getElementById("formConfiguracion").addEventListener("submit", function(e) {
        e.preventDefault();

        // Validar campos requeridos
        const nombreRestaurante = document.getElementById("nombre_restaurante").value.trim();
        const direccion = document.getElementById("direccion").value.trim();
        const telefono = document.getElementById("telefono").value.trim();

        if (!nombreRestaurante || !direccion || !telefono) {
            Swal.fire({
                icon: "warning",
                title: "Campos incompletos",
                text: "Por favor complete todos los campos obligatorios"
            });
            return;
        }

        // Confirmar guardado
        Swal.fire({
            title: "¿Guardar cambios?",
            text: "Se actualizará la configuración del sistema",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sí, guardar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Validar RUC (11 dígitos)
    document.getElementById("ruc").addEventListener("input", function(e) {
        this.value = this.value.replace(/[^0-9]/g, "").substring(0, 11);
    });

    // Validar teléfono (solo números)
    document.getElementById("telefono").addEventListener("input", function(e) {
        this.value = this.value.replace(/[^0-9+\-\s()]/g, "");
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>
