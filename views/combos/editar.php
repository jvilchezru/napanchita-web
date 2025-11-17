<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Combo - Sistema Napanchita</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .header-section {
            background: linear-gradient(135deg, #17a2b8 0%, #00bcd4 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

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
</head>

<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <h1><i class="fas fa-edit"></i> Editar Combo</h1>
            <p class="mb-0">Modifica los datos del combo</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Mensajes Flash -->
                <?php if (has_flash_message()): ?>
                    <?php $flash = get_flash_message(); ?>
                    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Datos del Combo</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo BASE_URL; ?>index.php?controller=combos&action=actualizar"
                            method="POST" enctype="multipart/form-data" id="formCombo">

                            <input type="hidden" name="id" value="<?php echo $combo['id']; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre del Combo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="<?php echo htmlspecialchars($combo['nombre']); ?>"
                                            placeholder="Ej: Combo Familiar, Combo Individual" required maxlength="100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="precio" class="form-label">Precio del Combo (S/) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="precio" name="precio"
                                            step="0.01" min="0.01" value="<?php echo $combo['precio']; ?>"
                                            placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion"
                                    rows="3" placeholder="Descripción del combo"><?php echo htmlspecialchars($combo['descripcion'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen del Combo</label>

                                <?php if (!empty($combo['imagen_url']) && file_exists($combo['imagen_url'])): ?>
                                    <div class="mb-2">
                                        <label class="form-label">Imagen actual:</label><br>
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

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="activo" name="activo"
                                    <?php echo $combo['activo'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activo">
                                    Combo activo
                                </label>
                            </div>

                            <hr>

                            <h5 class="mb-3"><i class="fas fa-box"></i> Productos del Combo</h5>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Selecciona los productos que formarán parte del combo y la cantidad de cada uno.
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

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=combos&action=index"
                                    class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Actualizar Combo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function previewImagen(event) {
            const preview = document.getElementById('imagenPreview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        function toggleCantidad(productoId) {
            const checkbox = document.getElementById('prod_' + productoId);
            const cantidadInput = document.getElementById('cant_' + productoId);

            if (checkbox.checked) {
                cantidadInput.disabled = false;
            } else {
                cantidadInput.disabled = true;
                cantidadInput.value = 1;
            }
        }

        // Validar que al menos un producto esté seleccionado
        document.getElementById('formCombo').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.producto-checkbox:checked');

            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Debes seleccionar al menos un producto para el combo');
                return false;
            }
        });
    </script>
</body>

</html>