<?php
$pageTitle = 'Crear Combo';
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
    <h1><i class="fas fa-plus-circle me-2"></i> Crear Nuevo Combo</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=combos">Combos</a></li>
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
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-boxes me-2"></i> Datos del Nuevo Combo
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=combos_guardar" method="POST" enctype="multipart/form-data" id="formCombo">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-boxes me-1"></i> Nombre del Combo <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            placeholder="Ej: Combo Familiar, Combo Individual" required maxlength="100">
                        <small class="form-text text-muted">Nombre del combo (máximo 100 caracteres)</small>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left me-1"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion"
                            rows="3" placeholder="Descripción detallada del combo"></textarea>
                        <small class="form-text text-muted">Descripción opcional del combo</small>
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">
                            <i class="fas fa-dollar-sign me-1"></i> Precio del Combo (S/) <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="precio" name="precio"
                            step="0.01" min="0.01" placeholder="0.00" required>
                        <small class="form-text text-muted">Precio del combo en soles</small>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">
                            <i class="fas fa-image me-1"></i> Imagen del Combo
                        </label>
                        <input type="file" class="form-control" id="imagen" name="imagen"
                            accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImagen(event)">
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB</small>
                        <img id="imagenPreview" class="imagen-preview" alt="Vista previa">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle text-success me-1"></i> Combo activo
                            </label>
                            <small class="form-text text-muted d-block">
                                Si está desactivado, no aparecerá en el menú
                            </small>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3"><i class="fas fa-box me-2"></i> Platos del Combo</h5>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Selecciona los platos que formarán parte del combo y la cantidad de cada uno.
                    </div>

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-filter me-1"></i> Filtrar por categoría:</label>
                            <select id="filtroCategoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-search me-1"></i> Buscar plato:</label>
                            <input type="text" id="buscadorPlato" class="form-control" placeholder="Escribe el nombre del plato...">
                        </div>
                    </div>

                    <div id="productosContainer">
                        <?php foreach ($platos as $prod): ?>
                            <div class="producto-item" data-categoria="<?php echo $prod['categoria_id']; ?>" data-nombre="<?php echo strtolower($prod['nombre']); ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input producto-checkbox" type="checkbox"
                                                id="prod_<?php echo $prod['id']; ?>"
                                                value="<?php echo $prod['id']; ?>"
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
                                            min="1" value="1" disabled>
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
                            <i class="fas fa-save me-2"></i> Guardar Combo
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
                    <li><strong>Nombre:</strong> Elige un nombre atractivo que describa el combo.</li>
                    <li><strong>Precio:</strong> Asegúrate de que el precio del combo sea menor que la suma de los platos individuales.</li>
                    <li><strong>Imagen:</strong> Una buena imagen aumenta las ventas. Usa fotos claras y apetitosas.</li>
                    <li><strong>Productos:</strong> Debes seleccionar al menos un plato para crear el combo.</li>
                    <li><strong>Estado:</strong> Marca como activo para que aparezca en el menú.</li>
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
                text: "Debes seleccionar al menos un plato para el combo"
            });
            return false;
        }
    });

    // Filtro por categoría
    document.getElementById("filtroCategoria").addEventListener("change", function() {
        filtrarPlatos();
    });

    // Buscador de platos
    document.getElementById("buscadorPlato").addEventListener("keyup", function() {
        filtrarPlatos();
    });

    function filtrarPlatos() {
        const categoriaSeleccionada = document.getElementById("filtroCategoria").value;
        const textoBusqueda = document.getElementById("buscadorPlato").value.toLowerCase();
        const items = document.querySelectorAll(".producto-item");
        let contadorVisible = 0;

        items.forEach(function(item) {
            const categoria = item.getAttribute("data-categoria");
            const nombre = item.getAttribute("data-nombre");
            let mostrar = true;

            // Filtrar por categoría
            if (categoriaSeleccionada && categoria !== categoriaSeleccionada) {
                mostrar = false;
            }

            // Filtrar por búsqueda
            if (textoBusqueda && !nombre.includes(textoBusqueda)) {
                mostrar = false;
            }

            if (mostrar) {
                item.style.display = "";
                contadorVisible++;
            } else {
                item.style.display = "none";
            }
        });

        // Mostrar mensaje si no hay resultados
        const container = document.getElementById("productosContainer");
        let mensajeNoResultados = document.getElementById("mensajeNoResultados");
        
        if (contadorVisible === 0) {
            if (!mensajeNoResultados) {
                mensajeNoResultados = document.createElement("div");
                mensajeNoResultados.id = "mensajeNoResultados";
                mensajeNoResultados.className = "alert alert-warning";
                mensajeNoResultados.innerHTML = "<i class=\"fas fa-exclamation-triangle me-2\"></i> No se encontraron platos con los filtros seleccionados.";
                container.appendChild(mensajeNoResultados);
            }
        } else {
            if (mensajeNoResultados) {
                mensajeNoResultados.remove();
            }
        }
    }
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>