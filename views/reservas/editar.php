<?php
$pageTitle = 'Editar Reserva';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i> Editar Reserva</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=reservas">Reservas</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Reserva - <?php echo htmlspecialchars($reserva['codigo_confirmacion']); ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?action=reservas_actualizar" method="POST" id="formReserva">
                        <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">
                        
                        <div class="row">
                            <!-- Cliente -->
                            <div class="col-md-6 mb-3">
                                <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <?php 
                                $campoDeshabilitado = in_array($reserva['estado'], ['cancelada', 'completada', 'no_show']) || $reserva['estado'] == 'confirmada';
                                if ($campoDeshabilitado): 
                                ?>
                                    <!-- Campo hidden para enviar el cliente_id cuando el select está deshabilitado -->
                                    <input type="hidden" name="cliente_id" value="<?php echo $reserva['cliente_id']; ?>">
                                <?php endif; ?>
                                <select id="cliente_id" class="form-select" <?php echo $campoDeshabilitado ? 'disabled' : 'name="cliente_id"'; ?> <?php echo !$campoDeshabilitado ? 'required' : ''; ?>>
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>" 
                                                <?php echo $cliente['id'] == $reserva['cliente_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cliente['nombre']); ?> - <?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Mesa -->
                            <div class="col-md-6 mb-3">
                                <label for="mesa_id" class="form-label">Mesa <span class="text-danger">*</span></label>
                                <?php 
                                $fechaHoraReserva = strtotime($reserva['fecha'] . ' ' . $reserva['hora']);
                                $yaPasoHora = $fechaHoraReserva <= time();
                                $deshabilitarMesaPersonas = in_array($reserva['estado'], ['cancelada', 'completada', 'no_show']) || ($reserva['estado'] == 'confirmada' && $yaPasoHora);
                                ?>
                                <select name="mesa_id" id="mesa_id" class="form-select" <?php echo $deshabilitarMesaPersonas ? 'disabled' : ''; ?> required>
                                    <option value="">Seleccione una mesa</option>
                                    <?php foreach ($mesas as $mesa): ?>
                                        <option value="<?php echo $mesa['id']; ?>" 
                                                data-capacidad="<?php echo $mesa['capacidad']; ?>"
                                                <?php echo $mesa['id'] == $reserva['mesa_id'] ? 'selected' : ''; ?>>
                                            Mesa <?php echo htmlspecialchars($mesa['numero']); ?> (Capacidad: <?php echo $mesa['capacidad']; ?> personas)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-4 mb-3">
                                <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                <?php if ($campoDeshabilitado): ?>
                                    <input type="hidden" name="fecha" value="<?php echo $reserva['fecha']; ?>">
                                <?php endif; ?>
                                <input type="date" id="fecha" class="form-control" 
                                       value="<?php echo $reserva['fecha']; ?>" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       <?php echo $campoDeshabilitado ? 'disabled' : 'name="fecha" required'; ?>>
                            </div>

                            <!-- Hora -->
                            <div class="col-md-4 mb-3">
                                <label for="hora" class="form-label">Hora <span class="text-danger">*</span></label>
                                <?php
                                $horaActual = substr($reserva['hora'], 0, 5);
                                if ($campoDeshabilitado): 
                                ?>
                                    <input type="hidden" name="hora" value="<?php echo $horaActual; ?>">
                                <?php endif; ?>
                                <select id="hora" class="form-select" <?php echo $campoDeshabilitado ? 'disabled' : 'name="hora" required'; ?>>
                                    <option value="">Seleccione hora</option>
                                    <?php
                                    $horas = [
                                        '00:00' => '12:00 AM', '01:00' => '1:00 AM', '02:00' => '2:00 AM', '03:00' => '3:00 AM',
                                        '04:00' => '4:00 AM', '05:00' => '5:00 AM', '06:00' => '6:00 AM', '07:00' => '7:00 AM',
                                        '08:00' => '8:00 AM', '09:00' => '9:00 AM', '10:00' => '10:00 AM', '11:00' => '11:00 AM',
                                        '12:00' => '12:00 PM', '13:00' => '1:00 PM', '14:00' => '2:00 PM', '15:00' => '3:00 PM',
                                        '16:00' => '4:00 PM', '17:00' => '5:00 PM', '18:00' => '6:00 PM', '19:00' => '7:00 PM',
                                        '20:00' => '8:00 PM', '21:00' => '9:00 PM', '22:00' => '10:00 PM', '23:00' => '11:00 PM'
                                    ];
                                    foreach ($horas as $valor => $etiqueta):
                                    ?>
                                        <option value="<?php echo $valor; ?>" <?php echo $horaActual == $valor ? 'selected' : ''; ?>>
                                            <?php echo $etiqueta; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Horario: 24 horas (Reserva con 1 hora de anticipación)</small>
                            </div>

                            <!-- Cantidad de personas -->
                            <div class="col-md-4 mb-3">
                                <label for="personas" class="form-label">Personas <span class="text-danger">*</span></label>
                                <input type="number" name="personas" id="personas" class="form-control" 
                                       value="<?php echo $reserva['personas']; ?>" min="1" max="20" 
                                       <?php echo $deshabilitarMesaPersonas ? 'disabled' : ''; ?> required>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <?php 
                                $estadoDeshabilitado = in_array($reserva['estado'], ['cancelada', 'completada']);
                                if ($estadoDeshabilitado): 
                                ?>
                                    <input type="hidden" name="estado" value="<?php echo $reserva['estado']; ?>">
                                <?php endif; ?>
                                <select id="estado" class="form-select" <?php echo $estadoDeshabilitado ? 'disabled' : 'name="estado"'; ?>>
                                    <?php if ($reserva['estado'] == 'pendiente'): ?>
                                        <option value="pendiente" selected>Pendiente</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="cancelada">Cancelada</option>
                                    <?php elseif ($reserva['estado'] == 'confirmada'): ?>
                                        <option value="confirmada" selected>Confirmada</option>
                                        <?php 
                                        $fechaHoraReserva = strtotime($reserva['fecha'] . ' ' . $reserva['hora']);
                                        $puedeCompletar = $fechaHoraReserva <= time();
                                        ?>
                                        <?php if ($puedeCompletar): ?>
                                            <option value="completada">Completada</option>
                                            <option value="no_show">No se presentó</option>
                                        <?php else: ?>
                                            <option value="completada" disabled>Completada (Solo después de la fecha/hora)</option>
                                        <?php endif; ?>
                                    <?php elseif ($reserva['estado'] == 'completada'): ?>
                                        <option value="completada" selected>Completada</option>
                                    <?php elseif ($reserva['estado'] == 'cancelada'): ?>
                                        <option value="cancelada" selected>Cancelada</option>
                                    <?php elseif ($reserva['estado'] == 'no_show'): ?>
                                        <option value="no_show" selected>No se presentó</option>
                                    <?php endif; ?>
                                </select>
                                <?php if (in_array($reserva['estado'], ['completada', 'cancelada'])): ?>
                                    <small class="text-muted"><i class="fas fa-lock"></i> Estado final, no se puede modificar</small>
                                <?php endif; ?>
                            </div>

                            <!-- Notas -->
                            <div class="col-12 mb-3">
                                <label for="notas" class="form-label">Notas / Observaciones</label>
                                <?php if ($campoDeshabilitado): ?>
                                    <input type="hidden" name="notas" value="<?php echo htmlspecialchars($reserva['notas'] ?? ''); ?>">
                                <?php endif; ?>
                                <textarea id="notas" class="form-control" rows="3" <?php echo $campoDeshabilitado ? 'disabled' : 'name="notas"'; ?>><?php echo htmlspecialchars($reserva['notas'] ?? ''); ?></textarea>
                            </div>

                            <!-- Mensaje de disponibilidad -->
                            <div class="col-12 mb-3">
                                <div id="mensajeDisponibilidad" class="alert" style="display:none;"></div>
                            </div>
                        </div>

                        <?php if ($reserva['estado'] == 'cancelada'): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-ban"></i> Esta reserva está cancelada y no puede ser modificada.
                            </div>
                        <?php elseif ($reserva['estado'] == 'completada'): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Esta reserva está completada y no puede ser modificada.
                            </div>
                        <?php elseif ($reserva['estado'] == 'no_show'): ?>
                            <div class="alert alert-dark">
                                <i class="fas fa-user-times"></i> Esta reserva está marcada como No Show y no puede ser modificada.
                            </div>
                        <?php endif; ?>

                        <?php
                        $fechaHoraReserva = strtotime($reserva['fecha'] . ' ' . $reserva['hora']);
                        $ahora = time();
                        $tiempoRestante = $fechaHoraReserva - $ahora;
                        $puedeCompletar = $fechaHoraReserva <= $ahora;
                        ?>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo BASE_URL; ?>index.php?action=reservas" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> <?php echo in_array($reserva['estado'], ['cancelada', 'confirmada', 'completada', 'no_show']) ? 'Volver' : 'Cancelar'; ?>
                            </a>
                            <?php if ($reserva['estado'] == 'pendiente'): ?>
                                <button type="submit" class="btn btn-warning" id="btnGuardar">
                                    <i class="fas fa-save"></i> Actualizar Reserva
                                </button>
                            <?php elseif ($reserva['estado'] == 'confirmada'): ?>
                                <?php if (!$puedeCompletar): ?>
                                    <!-- Antes de la hora: permitir actualizar mesa y personas -->
                                    <button type="submit" class="btn btn-warning" id="btnGuardarConfirmada">
                                        <i class="fas fa-save"></i> Actualizar Reserva
                                    </button>
                                    <button type="button" class="btn btn-secondary" disabled 
                                            title="Disponible a las <?php echo date('H:i', $fechaHoraReserva); ?>">
                                        <i class="fas fa-clock"></i> Esperar Hora
                                    </button>
                                <?php else: ?>
                                    <!-- Después de la hora: solo permitir completar o no show -->
                                    <button type="button" class="btn btn-info" id="btnCompletarReserva">
                                        <i class="fas fa-check"></i> Completar Reserva
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {


    // Botón para completar reserva (cambiar estado a completada)
    $('#btnCompletarReserva').on('click', function() {
        Swal.fire({
            title: '¿Completar reserva?',
            text: 'La mesa cambiará a estado ocupado y podrá crear el pedido',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, completar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#estado').val('completada');
                $('#formReserva').submit();
            }
        });
    });

    // Validar disponibilidad al cambiar fecha, hora o mesa
    $('#fecha, #hora, #mesa_id').on('change', function() {
        verificarDisponibilidad();
    });

    // Validar capacidad de mesa con número de personas
    $('#personas, #mesa_id').on('change', function() {
        const mesaId = $('#mesa_id').val();
        const personas = parseInt($('#personas').val());
        
        if (mesaId && personas) {
            const capacidad = parseInt($('#mesa_id option:selected').data('capacidad'));
            
            if (personas > capacidad) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Capacidad Excedida',
                    text: `La mesa seleccionada tiene capacidad para ${capacidad} personas. Ha ingresado ${personas} personas.`,
                    confirmButtonText: 'Entendido'
                });
            }
        }
    });

    // Actualizar opciones de hora disponibles según la fecha
    function actualizarHorasDisponibles() {
        const fechaSeleccionada = $('#fecha').val();
        const fechaHoy = '<?php echo date('Y-m-d'); ?>';
        const horaSelect = $('#hora');
        const estadoActual = '<?php echo $reserva['estado']; ?>';
        
        // Solo aplicar restricciones si no está completada o cancelada
        if (estadoActual === 'completada' || estadoActual === 'cancelada') {
            return;
        }
        
        horaSelect.find('option:not(:first)').prop('disabled', false).show();
        
        if (fechaSeleccionada === fechaHoy) {
            const ahora = new Date();
            const horaMinima = ahora.getHours() + 2; // Hora actual + 1 hora de anticipación, redondeado
            
            horaSelect.find('option').each(function() {
                const valor = $(this).val();
                if (valor) {
                    const horaOpcion = parseInt(valor.split(':')[0]);
                    if (horaOpcion < horaMinima) {
                        $(this).prop('disabled', true).hide();
                    }
                }
            });
        }
    }
    
    // Ejecutar al cargar y al cambiar fecha
    actualizarHorasDisponibles();
    $('#fecha').on('change', function() {
        actualizarHorasDisponibles();
    });

    // Verificar disponibilidad antes de enviar
    $('#formReserva').on('submit', function(e) {
        const disponible = $('#mensajeDisponibilidad').data('disponible');
        const hora = $('#hora').val();
        const fecha = $('#fecha').val();
        const fechaHoy = '<?php echo date('Y-m-d'); ?>';
        const estadoActual = '<?php echo $reserva['estado']; ?>';
        const estadoNuevo = $('#estado').val();
        
        // Validar que confirmadas no se cancelen
        if (estadoActual === 'confirmada' && estadoNuevo === 'cancelada') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'No permitido',
                text: 'No se puede cancelar una reserva confirmada',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Validar anticipación de 1 hora (solo si no está completada o cancelada)
        if (estadoActual !== 'completada' && estadoActual !== 'cancelada') {
            if (fecha === fechaHoy && hora) {
                const ahora = new Date();
                const [h, m] = hora.split(':').map(Number);
                const horaReserva = new Date();
                horaReserva.setHours(h, m || 0, 0, 0);
                
                const diferenciaHoras = (horaReserva - ahora) / (1000 * 60 * 60);
                
                if (diferenciaHoras < 1) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Anticipación requerida',
                        text: 'Las reservas deben hacerse con al menos 1 hora de anticipación',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
            }
        }
        
        if (disponible === false) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Mesa no disponible',
                text: 'La mesa seleccionada no está disponible para esa fecha y hora.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
    });
});

function verificarDisponibilidad() {
    const mesa_id = $('#mesa_id').val();
    const fecha = $('#fecha').val();
    const hora = $('#hora').val();
    const reserva_id = <?php echo $reserva['id']; ?>;
    
    if (!mesa_id || !fecha || !hora) {
        $('#mensajeDisponibilidad').hide();
        return;
    }
    
    $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?action=reservas_verificarDisponibilidad',
        method: 'GET',
        data: {
            mesa_id: mesa_id,
            fecha: fecha,
            hora: hora,
            reserva_id: reserva_id
        },
        dataType: 'json',
        success: function(response) {
            const $mensaje = $('#mensajeDisponibilidad');
            
            if (response.disponible) {
                $mensaje.removeClass('alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-check-circle"></i> ' + response.message)
                        .data('disponible', true)
                        .show();
                $('#btnGuardar').prop('disabled', false);
            } else {
                $mensaje.removeClass('alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-times-circle"></i> ' + response.message)
                        .data('disponible', false)
                        .show();
                $('#btnGuardar').prop('disabled', true);
            }
        },
        error: function() {
            $('#mensajeDisponibilidad').hide();
        }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
