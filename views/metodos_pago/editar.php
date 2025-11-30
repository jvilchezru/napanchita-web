<?php
$pageTitle = 'Editar Método de Pago';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i> Editar Método de Pago</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=metodos_pago">Métodos de Pago</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Método de Pago</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?action=metodos_pago_actualizar" method="POST">
                        <input type="hidden" name="id" value="<?php echo $metodoPago['id']; ?>">
                        
                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control" 
                                       value="<?php echo htmlspecialchars($metodoPago['nombre']); ?>"
                                       placeholder="Ej: Efectivo, Tarjeta, Yape" required maxlength="50">
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="activo" class="form-label">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="activo" 
                                           name="activo" <?php echo $metodoPago['activo'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="activo">
                                        Activo
                                    </label>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="col-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" 
                                          rows="3" maxlength="255" 
                                          placeholder="Descripción opcional del método de pago"><?php echo htmlspecialchars($metodoPago['descripcion'] ?? ''); ?></textarea>
                                <small class="text-muted">Máximo 255 caracteres</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo BASE_URL; ?>index.php?action=metodos_pago" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Método de Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
