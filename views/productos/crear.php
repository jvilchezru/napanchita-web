<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto - Sistema Napanchita</title>

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

        .imagen-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <h1><i class="fas fa-plus-circle"></i> Nuevo Producto</h1>
            <p class="mb-0">Agrega un nuevo producto al menú</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Mensajes Flash -->
                <?php if (has_flash_message()): ?>
                    <?php $flash = get_flash_message(); ?>
                    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Datos del Producto</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo BASE_URL; ?>index.php?controller=productos&action=guardar"
                            method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    placeholder="Ej: Ceviche de pescado, Chicharrón de pota" required maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion"
                                    rows="3" placeholder="Descripción detallada del producto"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio (S/) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="precio" name="precio"
                                    step="0.01" min="0.01" placeholder="0.00" required>
                            </div>

                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen del Producto</label>
                                <input type="file" class="form-control" id="imagen" name="imagen"
                                    accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImagen(event)">
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB</small>
                                <img id="imagenPreview" class="imagen-preview" alt="Vista previa">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="disponible" name="disponible" checked>
                                <label class="form-check-label" for="disponible">
                                    Producto disponible
                                </label>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?php echo BASE_URL; ?>index.php?controller=productos&action=index"
                                    class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Producto
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
    </script>
</body>

</html>