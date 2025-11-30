<?php
$pageTitle = 'Calendario de Reservas';
include __DIR__ . '/../layouts/header.php';

$mesActual = $_GET['mes'] ?? date('m');
$anioActual = $_GET['anio'] ?? date('Y');

// Calcular mes anterior y siguiente
$mesPrevio = $mesActual - 1;
$anioPrevio = $anioActual;
if ($mesPrevio < 1) {
    $mesPrevio = 12;
    $anioPrevio--;
}

$mesSiguiente = $mesActual + 1;
$anioSiguiente = $anioActual;
if ($mesSiguiente > 12) {
    $mesSiguiente = 1;
    $anioSiguiente++;
}

// Obtener primer y último día del mes
$primerDia = date('w', strtotime("$anioActual-$mesActual-01"));
$ultimoDia = date('t', strtotime("$anioActual-$mesActual-01"));

// Nombres de meses
$nombresMeses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Agrupar reservas por día
$reservasPorDia = [];
foreach ($reservas as $reserva) {
    $dia = date('j', strtotime($reserva['fecha']));
    if (!isset($reservasPorDia[$dia])) {
        $reservasPorDia[$dia] = [];
    }
    $reservasPorDia[$dia][] = $reserva;
}
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-alt me-2"></i> Calendario de Reservas</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=reservas">Reservas</a></li>
            <li class="breadcrumb-item active">Calendario</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar"></i> 
                        <?php echo $nombresMeses[(int)$mesActual] . ' ' . $anioActual; ?>
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <a href="<?php echo BASE_URL; ?>index.php?action=reservas_calendario&mes=<?php echo $mesPrevio; ?>&anio=<?php echo $anioPrevio; ?>" 
                           class="btn btn-light btn-sm">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=reservas_calendario&mes=<?php echo date('m'); ?>&anio=<?php echo date('Y'); ?>" 
                           class="btn btn-light btn-sm">
                            Hoy
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=reservas_calendario&mes=<?php echo $mesSiguiente; ?>&anio=<?php echo $anioSiguiente; ?>" 
                           class="btn btn-light btn-sm">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    <a href="<?php echo BASE_URL; ?>index.php?action=reservas" class="btn btn-light btn-sm ms-2">
                        <i class="fas fa-list"></i> Ver Lista
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?action=reservas_crear" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nueva Reserva
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Leyenda -->
            <div class="p-3 border-bottom">
                <div class="row">
                    <div class="col">
                        <span class="badge bg-warning text-dark me-2">Pendiente</span>
                        <span class="badge bg-success me-2">Confirmada</span>
                        <span class="badge bg-info me-2">Completada</span>
                        <span class="badge bg-danger me-2">Cancelada</span>
                    </div>
                </div>
            </div>

            <!-- Calendario -->
            <table class="table table-bordered mb-0" style="table-layout: fixed;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Domingo</th>
                        <th class="text-center">Lunes</th>
                        <th class="text-center">Martes</th>
                        <th class="text-center">Miércoles</th>
                        <th class="text-center">Jueves</th>
                        <th class="text-center">Viernes</th>
                        <th class="text-center">Sábado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $dia = 1;
                    $hoy = date('Y-m-d');
                    
                    // Calcular semanas necesarias
                    $totalCeldas = $primerDia + $ultimoDia;
                    $semanas = ceil($totalCeldas / 7);
                    
                    for ($semana = 0; $semana < $semanas; $semana++):
                    ?>
                        <tr>
                            <?php for ($diaSemana = 0; $diaSemana < 7; $diaSemana++): ?>
                                <td class="p-2" style="height: 120px; vertical-align: top;">
                                    <?php
                                    if (($semana == 0 && $diaSemana < $primerDia) || $dia > $ultimoDia) {
                                        // Celda vacía
                                        echo '&nbsp;';
                                    } else {
                                        $fechaActual = sprintf('%04d-%02d-%02d', $anioActual, $mesActual, $dia);
                                        $esHoy = ($fechaActual == $hoy);
                                        $claseHoy = $esHoy ? 'bg-light border-primary border-2' : '';
                                        ?>
                                        <div class="<?php echo $claseHoy; ?> h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <strong class="<?php echo $esHoy ? 'text-primary' : ''; ?>">
                                                    <?php echo $dia; ?>
                                                </strong>
                                                <?php if ($esHoy): ?>
                                                    <span class="badge bg-primary" style="font-size: 0.6rem;">HOY</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if (isset($reservasPorDia[$dia])): ?>
                                                <div class="reservas-dia" style="max-height: 90px; overflow-y: auto;">
                                                    <?php foreach ($reservasPorDia[$dia] as $reserva): ?>
                                                        <div class="mb-1">
                                                            <a href="#" 
                                                               class="text-decoration-none"
                                                               onclick="verDetalleReserva(<?php echo $reserva['id']; ?>); return false;">
                                                                <span class="badge bg-<?php 
                                                                    echo match($reserva['estado']) {
                                                                        'pendiente' => 'warning text-dark',
                                                                        'confirmada' => 'success',
                                                                        'completada' => 'info',
                                                                        'cancelada' => 'danger',
                                                                        default => 'secondary'
                                                                    };
                                                                ?>" style="font-size: 0.7rem; display: block;">
                                                                    <?php echo date('H:i', strtotime($reserva['hora'])); ?> - 
                                                                    Mesa <?php echo $reserva['mesa_numero']; ?>
                                                                    <br>
                                                                    <small><?php echo htmlspecialchars(substr($reserva['cliente_nombre'], 0, 15)); ?></small>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                        $dia++;
                                    }
                                    ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de detalle de reserva -->
<div class="modal fade" id="modalDetalleReserva" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleReserva">
                <div class="text-center py-3">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="btnEditarReserva" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalleReserva(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalDetalleReserva'));
    modal.show();
    
    // Actualizar enlace de edición
    document.getElementById('btnEditarReserva').href = 
        '<?php echo BASE_URL; ?>index.php?action=reservas_editar&id=' + id;
    
    // Cargar detalles
    $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?action=reservas',
        method: 'GET',
        data: { ajax: 1, id: id },
        success: function(reserva) {
            let html = `
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="alert alert-info">
                            <strong>Código:</strong> ${reserva.codigo_confirmacion}
                        </div>
                    </div>
                    <div class="col-6">
                        <strong>Cliente:</strong><br>
                        ${reserva.cliente_nombre}<br>
                        <small class="text-muted">${reserva.cliente_telefono || ''}</small>
                    </div>
                    <div class="col-6">
                        <strong>Mesa:</strong><br>
                        Mesa ${reserva.mesa_numero} (${reserva.mesa_capacidad} personas)
                    </div>
                    <div class="col-6 mt-3">
                        <strong>Fecha:</strong><br>
                        ${new Date(reserva.fecha).toLocaleDateString('es-PE')}
                    </div>
                    <div class="col-6 mt-3">
                        <strong>Hora:</strong><br>
                        ${reserva.hora.substring(0, 5)}
                    </div>
                    <div class="col-6 mt-3">
                        <strong>Personas:</strong><br>
                        ${reserva.personas}
                    </div>
                    <div class="col-6 mt-3">
                        <strong>Estado:</strong><br>
                        <span class="badge bg-${getEstadoBadge(reserva.estado)}">
                            ${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}
                        </span>
                    </div>
                    ${reserva.notas ? `
                        <div class="col-12 mt-3">
                            <strong>Notas:</strong><br>
                            ${reserva.notas}
                        </div>
                    ` : ''}
                </div>
            `;
            document.getElementById('contenidoDetalleReserva').innerHTML = html;
        },
        error: function() {
            document.getElementById('contenidoDetalleReserva').innerHTML = 
                '<div class="alert alert-danger">Error al cargar los detalles</div>';
        }
    });
}

function getEstadoBadge(estado) {
    const badges = {
        'pendiente': 'warning text-dark',
        'confirmada': 'success',
        'completada': 'info',
        'cancelada': 'danger',
        'no_show': 'secondary'
    };
    return badges[estado] || 'secondary';
}
</script>

<style>
.table-bordered td {
    border-color: #dee2e6;
}

.reservas-dia::-webkit-scrollbar {
    width: 4px;
}

.reservas-dia::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.reservas-dia::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.reservas-dia::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
