<?php
$pageTitle = 'Editar Categoría';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i> Editar Categoría</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=categorias">Categorías</a></li>
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
                <i class="fas fa-tags me-2"></i> Modificar Datos de la Categoría
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>index.php?action=categorias_actualizar" method="POST" id="formEditarCategoria">
                    <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-tag me-1"></i> Nombre <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            value="<?php echo htmlspecialchars($categoria['nombre']); ?>"
                            placeholder="Ej: Ceviches, Chicharrones, Bebidas" required maxlength="50">
                        <small class="form-text text-muted">Nombre único de la categoría</small>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left me-1"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion"
                            rows="3" placeholder="Descripción opcional de la categoría"><?php echo htmlspecialchars($categoria['descripcion'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="orden" class="form-label">
                            <i class="fas fa-sort-numeric-down me-1"></i> Orden de visualización
                        </label>
                        <input type="number" class="form-control" id="orden" name="orden"
                            value="<?php echo $categoria['orden']; ?>" min="0">
                        <small class="form-text text-muted">Número para ordenar las categorías en el menú (menor número = mayor prioridad)</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <!-- Campo oculto que envía 0 si el checkbox no está marcado -->
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                                <?php
                                // Convertir a entero para comparación segura
                                $activo = isset($categoria['activo']) ? (int)$categoria['activo'] : 0;
                                echo ($activo === 1) ? 'checked' : '';
                                ?>>
                            <label class="form-check-label" for="activo">
                                <i class="fas fa-check-circle text-success me-1"></i> Categoría activa
                            </label>
                        </div>
                    </div>

                    <?php if (isset($categoria['cantidad_productos']) && $categoria['cantidad_productos'] > 0): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Esta categoría tiene <strong><?php echo $categoria['cantidad_productos']; ?></strong> producto(s) asociado(s).
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo BASE_URL; ?>index.php?action=categorias" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Actualizar Categoría
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
    // Validación del formulario
    document.getElementById("formEditarCategoria").addEventListener("submit", function(e) {
        const nombre = document.getElementById("nombre").value.trim();
        
        if (nombre.length < 3) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El nombre debe tener al menos 3 caracteres"
            });
            return false;
        }
        
        if (nombre.length > 50) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "El nombre no puede tener más de 50 caracteres"
            });
            return false;
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>