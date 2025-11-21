<?php
$pageTitle = 'Gestión de Mesas';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Estilos para el layout de mesas */
    .mesas-layout {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 10px;
        padding: 30px;
        min-height: 600px;
        position: relative;
        overflow: hidden;
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .mesa-item {
        position: absolute;
        cursor: move;
        transition: all 0.3s ease;
        user-select: none;
    }

    .mesa-card {
        background: white;
        border-radius: 15px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        min-width: 100px;
        border: 3px solid;
    }

    .mesa-card:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .mesa-disponible {
        border-color: #28a745;
    }

    .mesa-ocupada {
        border-color: #dc3545;
    }

    .mesa-reservada {
        border-color: #ffc107;
    }

    .mesa-inactiva {
        border-color: #6c757d;
        opacity: 0.6;
    }

    .mesa-icono {
        font-size: 2.5rem;
        margin-bottom: 5px;
    }

    .mesa-numero {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 5px 0;
    }

    .mesa-capacidad {
        font-size: 0.9rem;
        color: #666;
    }

    .mesa-estado-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
        margin-top: 5px;
        font-weight: bold;
    }

    .badge-disponible {
        background-color: #28a745;
        color: white;
    }

    .badge-ocupada {
        background-color: #dc3545;
        color: white;
    }

    .badge-reservada {
        background-color: #ffc107;
        color: #000;
    }

    .badge-inactiva {
        background-color: #6c757d;
        color: white;
    }

    /* Estadísticas */
    .stats-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .stats-disponible {
        border-color: #28a745;
    }

    .stats-ocupada {
        border-color: #dc3545;
    }

    .stats-reservada {
        border-color: #ffc107;
    }

    .stats-total {
        border-color: #17a2b8;
    }

    /* Filtros */
    .filter-btn {
        margin: 5px;
        padding: 8px 20px;
        border-radius: 20px;
    }

    .filter-btn.active {
        box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.3);
    }

    /* Vista de lista */
    .vista-toggle {
        background: white;
        padding: 5px;
        border-radius: 10px;
        display: inline-block;
    }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-chair me-2"></i> Gestión de Mesas</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Mesas</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php?action=mesas_crear" class="btn btn-success">
                <i class="fas fa-plus-circle me-2"></i> Nueva Mesa
            </a>
        </div>
    </div>
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

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card stats-total">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Mesas</h6>
                        <h3 class="mb-0"><?php echo $estadisticas['total'] ?? 0; ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-chair fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card stats-disponible">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Disponibles</h6>
                        <h3 class="mb-0 text-success"><?php echo $estadisticas['disponibles'] ?? 0; ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card stats-ocupada">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Ocupadas</h6>
                        <h3 class="mb-0 text-danger"><?php echo $estadisticas['ocupadas'] ?? 0; ?></h3>
                    </div>
                    <div class="text-danger">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card stats-reservada">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Reservadas</h6>
                        <h3 class="mb-0 text-warning"><?php echo $estadisticas['reservadas'] ?? 0; ?></h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-bookmark fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Controles -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="mb-2"><i class="fas fa-filter me-2"></i> Filtros:</h6>
                <button class="btn btn-sm btn-outline-primary filter-btn active" data-filter="all">
                    <i class="fas fa-list me-1"></i> Todas
                </button>
                <button class="btn btn-sm btn-outline-success filter-btn" data-filter="disponible">
                    <i class="fas fa-check-circle me-1"></i> Disponibles
                </button>
                <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="ocupada">
                    <i class="fas fa-users me-1"></i> Ocupadas
                </button>
                <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="reservada">
                    <i class="fas fa-bookmark me-1"></i> Reservadas
                </button>
                <br>
                <button class="btn btn-sm btn-outline-info filter-btn mt-2" data-capacity="small">
                    <i class="fas fa-user me-1"></i> Hasta 4 personas (<?php echo $estadisticas['pequenas'] ?? 0; ?>)
                </button>
                <button class="btn btn-sm btn-outline-info filter-btn mt-2" data-capacity="large">
                    <i class="fas fa-users me-1"></i> Más de 4 personas (<?php echo $estadisticas['grandes'] ?? 0; ?>)
                </button>
            </div>
            <div class="col-md-6 text-end">
                <h6 class="mb-2"><i class="fas fa-eye me-2"></i> Vista:</h6>
                <div class="vista-toggle">
                    <button class="btn btn-sm btn-primary" id="vistaGrafica">
                        <i class="fas fa-th me-1"></i> Gráfica
                    </button>
                    <button class="btn btn-sm btn-outline-primary" id="vistaLista">
                        <i class="fas fa-list me-1"></i> Lista
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista Gráfica (Layout de Mesas) -->
<div class="card mb-4" id="layoutGrafico">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-map me-2"></i> Layout del Restaurante</span>
        <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i> Arrastra las mesas para reorganizar
        </small>
    </div>
    <div class="card-body p-0">
        <div class="mesas-layout" id="mesasLayout">
            <?php foreach ($mesas as $mesa): ?>
                <?php if ($mesa['activo']): ?>
                    <div class="mesa-item mesa-<?php echo $mesa['estado']; ?>"
                        data-id="<?php echo $mesa['id']; ?>"
                        data-estado="<?php echo $mesa['estado']; ?>"
                        data-capacidad="<?php echo $mesa['capacidad']; ?>"
                        style="left: <?php echo $mesa['posicion_x']; ?>px; top: <?php echo $mesa['posicion_y']; ?>px;">
                        <div class="mesa-card">
                            <div class="mesa-icono">
                                <?php if ($mesa['capacidad'] <= 4): ?>
                                    🪑
                                <?php else: ?>
                                    🍽️
                                <?php endif; ?>
                            </div>
                            <div class="mesa-numero">Mesa <?php echo htmlspecialchars($mesa['numero']); ?></div>
                            <div class="mesa-capacidad">
                                <i class="fas fa-user me-1"></i><?php echo $mesa['capacidad']; ?> personas
                            </div>
                            <span class="mesa-estado-badge badge-<?php echo $mesa['estado']; ?>">
                                <?php echo ucfirst($mesa['estado']); ?>
                            </span>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary cambiar-estado"
                                    data-id="<?php echo $mesa['id']; ?>"
                                    title="Cambiar estado">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <a href="<?php echo BASE_URL; ?>index.php?action=mesas_editar&id=<?php echo $mesa['id']; ?>"
                                    class="btn btn-sm btn-outline-warning"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Vista de Lista (Tabla) -->
<div class="card" id="layoutLista" style="display: none;">
    <div class="card-header">
        <i class="fas fa-table me-2"></i> Listado de Mesas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaMesas">
                <thead class="table-primary">
                    <tr>
                        <th>Número</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th>Posición</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mesas as $mesa): ?>
                        <?php if ($mesa['activo']): ?>
                            <tr class="mesa-row"
                                data-estado="<?php echo $mesa['estado']; ?>"
                                data-capacidad="<?php echo $mesa['capacidad']; ?>">
                                <td><strong>Mesa <?php echo htmlspecialchars($mesa['numero']); ?></strong></td>
                                <td>
                                    <i class="fas fa-user me-1"></i><?php echo $mesa['capacidad']; ?> personas
                                </td>
                                <td>
                                    <span class="badge bg-<?php
                                                            echo $mesa['estado'] === 'disponible' ? 'success' : ($mesa['estado'] === 'ocupada' ? 'danger' : ($mesa['estado'] === 'reservada' ? 'warning' : 'secondary'));
                                                            ?>">
                                        <?php echo ucfirst($mesa['estado']); ?>
                                    </span>
                                </td>
                                <td>X: <?php echo $mesa['posicion_x']; ?>, Y: <?php echo $mesa['posicion_y']; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary cambiar-estado-lista"
                                        data-id="<?php echo $mesa['id']; ?>"
                                        title="Cambiar estado">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>index.php?action=mesas_editar&id=<?php echo $mesa['id']; ?>"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>index.php?action=mesas_eliminar&id=<?php echo $mesa['id']; ?>"
                                        class="btn btn-sm btn-outline-danger btn-eliminar"
                                        title="Eliminar"
                                        onclick="return confirm('¿Estás seguro de desactivar esta mesa?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
$(document).ready(function() {
    // DataTables
    $("#tablaMesas").DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        order: [[0, "asc"]]
    });

    // Cambiar entre vistas
    $("#vistaGrafica").click(function() {
        $(this).removeClass("btn-outline-primary").addClass("btn-primary");
        $("#vistaLista").removeClass("btn-primary").addClass("btn-outline-primary");
        $("#layoutGrafico").show();
        $("#layoutLista").hide();
    });

    $("#vistaLista").click(function() {
        $(this).removeClass("btn-outline-primary").addClass("btn-primary");
        $("#vistaGrafica").removeClass("btn-primary").addClass("btn-outline-primary");
        $("#layoutGrafico").hide();
        $("#layoutLista").show();
    });

    // Filtros
    $(".filter-btn").click(function() {
        const filter = $(this).data("filter");
        const capacity = $(this).data("capacity");
        
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");

        if (filter) {
            // Filtrar por estado
            $(".mesa-item, .mesa-row").each(function() {
                const estado = $(this).data("estado");
                if (filter === "all" || estado === filter) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else if (capacity) {
            // Filtrar por capacidad
            $(".mesa-item, .mesa-row").each(function() {
                const cap = parseInt($(this).data("capacidad"));
                if (capacity === "small" && cap <= 4) {
                    $(this).show();
                } else if (capacity === "large" && cap > 4) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Drag and Drop
    let isDragging = false;
    let currentMesa = null;
    let offsetX = 0;
    let offsetY = 0;

    $(".mesa-item").mousedown(function(e) {
        isDragging = true;
        currentMesa = $(this);
        const rect = this.getBoundingClientRect();
        const layoutRect = document.getElementById("mesasLayout").getBoundingClientRect();
        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;
        $(this).css("z-index", 1000);
    });

    $(document).mousemove(function(e) {
        if (isDragging && currentMesa) {
            const layoutRect = document.getElementById("mesasLayout").getBoundingClientRect();
            let newX = e.clientX - layoutRect.left - offsetX;
            let newY = e.clientY - layoutRect.top - offsetY;
            
            // Límites
            newX = Math.max(0, Math.min(newX, layoutRect.width - 120));
            newY = Math.max(0, Math.min(newY, layoutRect.height - 180));
            
            currentMesa.css({
                left: newX + "px",
                top: newY + "px"
            });
        }
    });

    $(document).mouseup(function() {
        if (isDragging && currentMesa) {
            const id = currentMesa.data("id");
            const x = parseInt(currentMesa.css("left"));
            const y = parseInt(currentMesa.css("top"));
            
            // Guardar posición
            $.post("' . BASE_URL . 'index.php?action=mesas_actualizarPosicion", {
                id: id,
                x: x,
                y: y
            });
            
            currentMesa.css("z-index", "");
            isDragging = false;
            currentMesa = null;
        }
    });

    // Cambiar estado
    $(".cambiar-estado, .cambiar-estado-lista").click(function() {
        const id = $(this).data("id");
        
        Swal.fire({
            title: "Cambiar Estado",
            input: "select",
            inputOptions: {
                disponible: "Disponible",
                ocupada: "Ocupada",
                reservada: "Reservada",
                inactiva: "Inactiva"
            },
            inputPlaceholder: "Selecciona el estado",
            showCancelButton: true,
            confirmButtonText: "Cambiar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                $.post("' . BASE_URL . 'index.php?action=mesas_cambiarEstado", {
                    id: id,
                    estado: result.value
                }, function(response) {
                    if (response.success) {
                        Swal.fire("Éxito", response.message, "success").then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                }, "json");
            }
        });
    });
});
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>