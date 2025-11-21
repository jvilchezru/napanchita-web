<?php
$pageTitle = 'Editar Combo';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .imagen-preview,
    .imagen-actual {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 5px;
    }

    .imagen-preview {
        display: none;
    }

    .producto-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
    }

    .producto-item:hover {
        background-color: #e9ecef;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i> Editar Combo</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=combos">Combos</a></li>
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
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-boxes me-2"></i> Modificar Datos del Combo
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=combos_actualizar" method="POST" enctype="multipart/form-data" id="formCombo">
                    <input type="hidden" name="id" value="<?php echo $combo['id']; ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-boxes me-1"></i> Nombre del Combo <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?php echo htmlspecialchars($combo['nombre']); ?>"
                            placeholder="Ej: Combo Familiar, Combo Individual" required maxlength="100">
                        <small class="form-text text-muted">Nombre del combo</small>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left me-1"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion"
                            rows="3" placeholder="Descripción detallada del combo"><?php echo htmlspecialchars($combo['descripcion'] ?? ''); ?></textarea>
                        <small class="form-text text-muted">Descripción opcional del combo</small>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">
                            <i class="fas fa-dollar-sign me-1"></i> Precio del Combo (S/) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="precio" name="precio"
                            step="0.01" min="0.01" value="<?php echo $combo['precio']; ?>"
                            placeholder="0.00" required>
                        <small class="form-text text-muted">Precio del combo en soles</small>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">
                            <i class="fas fa-image me-1"></i> Imagen del Combo
                        </label>

                        <?php if (!empty($combo['imagen_url']) && file_exists($combo['imagen_url'])): ?>
                            <div class="mb-2">
                                <label class="form-label d-block"><strong>Imagen actual:</strong></label>
                                <img src="<?php echo BASE_URL . $combo['imagen_url']; ?>"
                                    alt="<?php echo htmlspecialchars($combo['nombre']); ?>"
                                    class="imagen-actual">
                            </div>
                        <?php endif; ?>

                        <input type="file" class="form-control" id="imagen" name="imagen"
                            accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImagen(event)">
                        <small class="form-text text-muted">
                            <?php if (!empty($combo['imagen_url'])): ?>
                                Sube una nueva imagen solo si deseas cambiar la actual.
                            <?php endif; ?>
                            Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB
                        </small>
                        <img id="imagenPreview" class="imagen-preview" alt="Vista previa">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                <?php
                                $activo = isset($combo['activo']) ? (int)$combo['activo'] : 0;
                                echo ($activo === 1) ? 'checked' : '';
                                ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle text-success me-1"></i> Combo activo
                            </label>
                            <small class="form-text text-muted d-block">
                                Si está desactivado, no aparecerá en el menú
                            </small>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3"><i class="fas fa-box me-2"></i> Productos del Combo</h5>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Selecciona los productos que formarán parte del combo y la cantidad de cada uno.
                    </div>

                    <?php
                    // Crear array asociativo de productos actuales del combo
                    $productos_combo = [];
                    foreach ($combo['productos'] as $prod_combo) {
                        $productos_combo[$prod_combo['producto_id']] = $prod_combo['cantidad'];
                    }
                    ?>

                    <div id="productosContainer">
                        <?php foreach ($productos as $prod): ?>
                            <?php
                            $esta_en_combo = isset($productos_combo[$prod['id']]);
                            $cantidad_actual = $esta_en_combo ? $productos_combo[$prod['id']] : 1;
                            ?>
                            <div class="producto-item">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input producto-checkbox" type="checkbox"
                                                id="prod_<?php echo $prod['id']; ?>"
                                                value="<?php echo $prod['id']; ?>"
                                                <?php echo $esta_en_combo ? 'checked' : ''; ?>
                                                onchange="toggleCantidad(<?php echo $prod['id']; ?>)">
                                            <label class="form-check-label" for="prod_<?php echo $prod['id']; ?>">
                                                <strong><?php echo htmlspecialchars($prod['nombre']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($prod['categoria_nombre']); ?> - S/ <?php echo number_format($prod['precio'], 2); ?></small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cant_<?php echo $prod['id']; ?>" class="form-label">Cantidad:</label>
                                        <input type="number" class="form-control form-control-sm cantidad-input"
                                            id="cant_<?php echo $prod['id']; ?>"
                                            name="productos[<?php echo $prod['id']; ?>]"
                                            min="1" value="<?php echo $cantidad_actual; ?>"
                                            <?php echo !$esta_en_combo ? 'disabled' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=combos" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Actualizar Combo
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
    function previewImagen(event) {
        const preview = document.getElementById("imagenPreview");
        const file = event.target.files[0];

        if (file) {
            // Validar tamaño (5MB máximo)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "La imagen no debe superar los 5MB"
                });
                event.target.value = "";
                preview.style.display = "none";
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
        }
    }

    function toggleCantidad(productoId) {
        const checkbox = document.getElementById("prod_" + productoId);
        const cantidadInput = document.getElementById("cant_" + productoId);

        if (checkbox.checked) {
            cantidadInput.disabled = false;
        } else {
            cantidadInput.disabled = true;
            cantidadInput.value = 1;
        }
    }

    // Validación del formulario
    document.getElementById("formCombo").addEventListener("submit", function(e) {
        const checkboxes = document.querySelectorAll(".producto-checkbox:checked");
        const nombre = document.getElementById("nombre").value.trim();
        const precio = parseFloat(document.getElementById("precio").value);
        
        if (nombre.length < 3) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El nombre debe tener al menos 3 caracteres"
            });
            return false;
        }
        
        if (precio <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El precio debe ser mayor a 0"
            });
            return false;
        }

        if (checkboxes.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Debes seleccionar al menos un producto para el combo"
            });
            return false;
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>