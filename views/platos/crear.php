<?php
$pageTitle = 'Crear Producto';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .imagen-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 5px;
        display: none;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-plus-circle me-2"></i> Crear Nuevo Plato</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=platos">Platos</a></li>
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
                <i class="fas fa-fish me-2"></i> Datos del Nuevo Plato
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=platos_guardar" method="POST" enctype="multipart/form-data" id="formCrearProducto">

                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">
                            <i class="fas fa-tags me-1"></i> Categoría <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required>
                            <option value="">Seleccionar categoría...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Selecciona la categoría a la que pertenece el plato</small>
                    </div>

                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-utensils me-1"></i> Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            placeholder="Ej: Ceviche de pescado, Chicharrón de pota" required maxlength="100">
                        <small class="form-text text-muted">Nombre del plato (máximo 100 caracteres)</small>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left me-1"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion"
                            rows="3" placeholder="Descripción detallada del plato"></textarea>
                        <small class="form-text text-muted">Descripción opcional del plato</small>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">
                            <i class="fas fa-dollar-sign me-1"></i> Precio (S/) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="precio" name="precio"
                            step="0.01" min="0.01" placeholder="0.00" required>
                        <small class="form-text text-muted">Precio del plato en soles</small>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">
                            <i class="fas fa-image me-1"></i> Imagen del Plato
                        </label>
                        <input type="file" class="form-control" id="imagen" name="imagen"
                            accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImagen(event)">
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB</small>
                        <img id="imagenPreview" class="imagen-preview" alt="Vista previa">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="disponible" value="0">
                            <input class="form-check-input" type="checkbox" id="disponible" name="disponible" value="1" checked>
                            <label class="form-check-label" for="disponible">
                                <i class="fas fa-check-circle text-success me-1"></i> Producto disponible
                            </label>
                            <small class="form-text text-muted d-block">
                                Si está desactivado, no aparecerá en el menú
                            </small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=platos" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Guardar Producto
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
                    <li><strong>Categoría:</strong> Asegúrate de seleccionar la categoría correcta para organizar tu menú.</li>
                    <li><strong>Imagen:</strong> Una buena imagen aumenta las ventas. Usa fotos claras y apetitosas.</li>
                    <li><strong>Precio:</strong> Revisa que el precio sea correcto antes de guardar.</li>
                    <li><strong>Disponibilidad:</strong> Marca como no disponible si el plato está temporalmente agotado.</li>
                </ul>
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

    // Validación del formulario
    document.getElementById("formCrearProducto").addEventListener("submit", function(e) {
        const nombre = document.getElementById("nombre").value.trim();
        const precio = parseFloat(document.getElementById("precio").value);
        const categoria = document.getElementById("categoria_id").value;
        
        if (!categoria) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Debes seleccionar una categoría"
            });
            return false;
        }
        
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
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>