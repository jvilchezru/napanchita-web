<?php
$pageTitle = 'Gestión de Reservas';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-check me-2"></i> Gestión de Reservas</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Reservas</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <?php if (has_flash_message()): ?>
        <?php $flash = get_flash_message(); ?>
        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Reservas</h5>
                    <h2><?php echo $estadisticas['total'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pendientes</h5>
                    <h2><?php echo $estadisticas['pendientes'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Confirmadas</h5>
                    <h2><?php echo $estadisticas['confirmadas'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Completadas</h5>
                    <h2><?php echo $estadisticas['completadas'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Listado de Reservas</h5>
                </div>
                <div class="col-auto">
                    <a href="<?php echo BASE_URL; ?>index.php?action=reservas_calendario" class="btn btn-light btn-sm">
                        <i class="fas fa-calendar"></i> Calendario
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?action=reservas_crear" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nueva Reserva
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Búsqueda por código -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="buscarCodigo" class="form-control" placeholder="Buscar por código (RES-XXXXXX)">
                        <button type="button" onclick="buscarPorCodigo()" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <form method="GET" action="<?php echo BASE_URL; ?>index.php" class="row mb-3">
                <input type="hidden" name="action" value="reservas">
                <div class="col-md-3">
                    <label>Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmada">Confirmada</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Desde:</label>
                    <input type="date" name="fecha_desde" class="form-control" value="<?php echo $_GET['fecha_desde'] ?? date('Y-m-d'); ?>">
                </div>
                <div class="col-md-3">
                    <label>Hasta:</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="<?php echo $_GET['fecha_hasta'] ?? date('Y-m-d', strtotime('+30 days')); ?>">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>

            <table id="tablaReservas" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Mesa</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Personas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($reserva['codigo_confirmacion']); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($reserva['cliente_nombre']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($reserva['cliente_telefono'] ?? ''); ?></small>
                            </td>
                            <td>Mesa <?php echo htmlspecialchars($reserva['mesa_numero']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reserva['fecha'])); ?></td>
                            <td><?php echo date('H:i', strtotime($reserva['hora'])); ?></td>
                            <td><?php echo $reserva['personas']; ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($reserva['estado']) {
                                        'pendiente' => 'warning',
                                        'confirmada' => 'success',
                                        'completada' => 'info',
                                        'cancelada' => 'danger',
                                        'no_show' => 'secondary',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst($reserva['estado']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($reserva['estado'] == 'pendiente'): ?>
                                        <button onclick="cambiarEstado(<?php echo $reserva['id']; ?>, 'confirmada')" 
                                                class="btn btn-success" title="Confirmar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php 
                                    $fechaHoraReserva = strtotime($reserva['fecha'] . ' ' . $reserva['hora']);
                                    $ahora = time();
                                    $tiempoRestante = $fechaHoraReserva - $ahora;
                                    $minutosRestantes = ceil($tiempoRestante / 60);
                                    ?>
                                    <?php if ($reserva['estado'] == 'confirmada'): ?>
                                        <?php if ($fechaHoraReserva <= $ahora): ?>
                                            <button onclick="cambiarEstado(<?php echo $reserva['id']; ?>, 'completada')" 
                                                    class="btn btn-info" title="Completar">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled 
                                                    title="Disponible en <?php echo $minutosRestantes; ?> minutos (<?php echo date('H:i', $fechaHoraReserva); ?>)">
                                                <i class="fas fa-clock"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <a href="<?php echo BASE_URL; ?>index.php?action=reservas_editar&id=<?php echo $reserva['id']; ?>" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($reserva['estado'] == 'pendiente'): ?>
                                        <button onclick="cambiarEstado(<?php echo $reserva['id']; ?>, 'cancelada')" 
                                                class="btn btn-danger" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($reserva['estado'] == 'confirmada' && $fechaHoraReserva < $ahora): ?>
                                        <button onclick="marcarNoShow(<?php echo $reserva['id']; ?>)" 
                                                class="btn btn-secondary" title="No se presentó">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function buscarPorCodigo() {
    const codigo = $('#buscarCodigo').val().trim();
    
    if (!codigo) {
        Swal.fire('Atención', 'Ingrese un código de reserva', 'info');
        return;
    }
    
    $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?action=reservas_buscarPorCodigo',
        method: 'GET',
        data: { codigo: codigo },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const r = response.data;
                Swal.fire({
                    title: 'Reserva Encontrada',
                    html: `
                        <div class="text-start">
                            <p><strong>Código:</strong> ${r.codigo_confirmacion}</p>
                            <p><strong>Cliente:</strong> ${r.cliente_nombre}</p>
                            <p><strong>Mesa:</strong> ${r.mesa_numero}</p>
                            <p><strong>Fecha:</strong> ${new Date(r.fecha).toLocaleDateString('es-PE')}</p>
                            <p><strong>Hora:</strong> ${r.hora.substring(0, 5)}</p>
                            <p><strong>Personas:</strong> ${r.personas}</p>
                            <p><strong>Estado:</strong> <span class="badge bg-primary">${r.estado}</span></p>
                        </div>
                    `,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Ver/Editar',
                    cancelButtonText: 'Cerrar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?php echo BASE_URL; ?>index.php?action=reservas_editar&id=' + r.id;
                    }
                });
            } else {
                Swal.fire('No encontrada', response.message, 'warning');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al buscar la reserva', 'error');
        }
    });
}

$('#buscarCodigo').on('keypress', function(e) {
    if (e.which === 13) {
        buscarPorCodigo();
    }
});

$(document).ready(function() {
    $('#tablaReservas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        order: [[3, 'desc'], [4, 'desc']]
    });
});

function cambiarEstado(id, estado) {
    const mensaje = estado === 'confirmada' ? 'confirmar' : 
                   estado === 'completada' ? 'completar' : 'cancelar';

    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${mensaje} esta reserva?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, ' + mensaje,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>index.php?action=reservas_cambiarEstado',
                method: 'POST',
                data: { id: id, estado: estado },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.redirect) {
                            // Si es completada, mostrar opción de crear pedido
                            Swal.fire({
                                title: '¡Reserva completada!',
                                text: response.message + '. ¿Desea crear un pedido para esta mesa?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, crear pedido',
                                cancelButtonText: 'Ahora no'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = response.redirect;
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al cambiar el estado', 'error');
                }
            });
        }
    });
}

function marcarNoShow(id) {
    Swal.fire({
        title: '¿Cliente no se presentó?',
        text: '¿Deseas marcar esta reserva como "No show"?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, marcar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>index.php?action=reservas_marcarNoShow',
                method: 'POST',
                data: { id: id },
                success: function() {
                    Swal.fire('¡Marcado!', 'Reserva marcada como no presentado', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Error al marcar la reserva', 'error');
                }
            });
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
