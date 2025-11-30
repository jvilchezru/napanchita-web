<?php
$pageTitle = 'Nueva Reserva';
include __DIR__ . '/../layouts/header.php';
?>



<div class="page-header">
    <h1><i class="fas fa-calendar-plus me-2"></i> Nueva Reserva</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=reservas">Reservas</a></li>
            <li class="breadcrumb-item active">Nueva Reserva</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nueva Reserva</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>index.php?action=reservas_guardar" method="POST" id="formReserva">
                        <div class="row">
                            <!-- Cliente -->
                            <div class="col-md-6 mb-3">
                                <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cliente_id" id="cliente_id" class="form-select" required>
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>">
                                            <?php echo htmlspecialchars($cliente['nombre']); ?>
                                            <?php if (!empty($cliente['telefono'])): ?>
                                                - <?php echo htmlspecialchars($cliente['telefono']); ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Si no existe, créelo desde el módulo de clientes</small>
                            </div>

                            <!-- Mesa -->
                            <div class="col-md-6 mb-3">
                                <label for="mesa_id" class="form-label">Mesa <span class="text-danger">*</span></label>
                                <select name="mesa_id" id="mesa_id" class="form-select" required>
                                    <option value="">Seleccione una mesa</option>
                                    <?php if (empty($mesas)): ?>
                                        <option value="" disabled>No hay mesas disponibles</option>
                                    <?php else: ?>
                                        <?php foreach ($mesas as $mesa): ?>
                                            <option value="<?php echo $mesa['id']; ?>" data-capacidad="<?php echo $mesa['capacidad']; ?>">
                                                Mesa <?php echo htmlspecialchars($mesa['numero']); ?> (Capacidad: <?php echo $mesa['capacidad']; ?> personas)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Solo mesas disponibles (activas y sin reservas)</small>
                            </div>

                            <!-- Fecha -->
                            <div class="col-md-4 mb-3">
                                <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="fecha" class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <!-- Hora -->
                            <div class="col-md-4 mb-3">
                                <label for="hora" class="form-label">Hora <span class="text-danger">*</span></label>
                                <select name="hora" id="hora" class="form-select" required>
                                    <option value="">Seleccione hora</option>
                                </select>
                                <small class="text-muted" id="infoHorario">Horario: 24 horas (Reserva con 1 hora de anticipación)</small>
                            </div>

                            <!-- Cantidad de personas -->
                            <div class="col-md-4 mb-3">
                                <label for="personas" class="form-label">Personas <span class="text-danger">*</span></label>
                                <input type="number" name="personas" id="personas" class="form-control" 
                                       min="1" max="20" required>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="pendiente" selected>Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                </select>
                            </div>

                            <!-- Notas -->
                            <div class="col-12 mb-3">
                                <label for="notas" class="form-label">Notas / Observaciones</label>
                                <textarea name="notas" id="notas" class="form-control" rows="3" 
                                          placeholder="Ej: Cliente solicita mesa cerca de la ventana, celebración de cumpleaños, etc."></textarea>
                            </div>

                            <!-- Mensaje de disponibilidad -->
                            <div class="col-12 mb-3">
                                <div id="mensajeDisponibilidad" class="alert" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo BASE_URL; ?>index.php?action=reservas" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                <i class="fas fa-save"></i> Guardar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
console.log('Script cargado');
$(document).ready(function() {
    console.log('Document ready ejecutado');
    // Función para generar las opciones de hora basadas en la fecha seleccionada
    function generarOpcionesHora() {
        const fechaSeleccionada = $('#fecha').val();
        console.log('Generando horas. Fecha seleccionada:', fechaSeleccionada);
        
        if (!fechaSeleccionada) {
            console.log('No hay fecha seleccionada');
            $('#hora').html('<option value="">Seleccione primero una fecha</option>');
            return;
        }
        
        const ahora = new Date();
        const fechaHoy = ahora.toISOString().split('T')[0];
        const esFechaHoy = fechaSeleccionada === fechaHoy;
        
        console.log('Fecha hoy:', fechaHoy, 'Es fecha hoy:', esFechaHoy);
        console.log('Hora actual:', ahora.getHours(), 'Minutos:', ahora.getMinutes());
        
        // Calcular hora mínima (hora actual + 1)
        let horaMinima = 0;
        if (esFechaHoy) {
            const horaActual = ahora.getHours();
            const minutosActuales = ahora.getMinutes();
            
            // Hora actual + 1, y si tiene minutos, + 1 más
            horaMinima = horaActual + 1;
            if (minutosActuales > 0) {
                horaMinima++;
            }
        }
        
        console.log('Hora mínima calculada:', horaMinima);
        
        // Generar opciones
        let opciones = '<option value="">Seleccione hora</option>';
        let contadorOpciones = 0;
        
        for (let hora = 0; hora < 24; hora++) {
            // Si es hoy, solo mostrar horas válidas (1 hora después)
            if (esFechaHoy && hora < horaMinima) {
                continue;
            }
            
            const horaFormato24 = hora.toString().padStart(2, '0') + ':00';
            let horaFormato12;
            
            if (hora === 0) {
                horaFormato12 = '12:00 AM';
            } else if (hora < 12) {
                horaFormato12 = hora + ':00 AM';
            } else if (hora === 12) {
                horaFormato12 = '12:00 PM';
            } else {
                horaFormato12 = (hora - 12) + ':00 PM';
            }
            
            opciones += `<option value="${horaFormato24}">${horaFormato12}</option>`;
            contadorOpciones++;
        }
        
        console.log('Opciones generadas:', contadorOpciones);
        
        $('#hora').html(opciones);
        
        // Actualizar mensaje informativo
        if (esFechaHoy) {
            const ampm = horaMinima >= 12 ? 'PM' : 'AM';
            const hora12 = horaMinima > 12 ? horaMinima - 12 : (horaMinima === 0 ? 12 : horaMinima);
            $('#infoHorario').html(`<i class="fas fa-info-circle"></i> Disponible desde las ${hora12}:00 ${ampm} (1 hora de anticipación)`);
        } else {
            $('#infoHorario').html('Horario: 24 horas (Reserva con 1 hora de anticipación)');
        }
    }
    
    // Generar opciones al cargar la página (con pequeño delay para asegurar que el DOM esté listo)
    setTimeout(function() {
        generarOpcionesHora();
    }, 100);
    
    // Regenerar opciones cuando cambie la fecha
    $('#fecha').on('change', function() {
        generarOpcionesHora();
        verificarDisponibilidad();
    });

    // Validar disponibilidad al cambiar hora o mesa
    $('#hora, #mesa_id').on('change', function() {
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

    // Función para calcular fecha/hora más cercana con 1 hora de anticipación
    function obtenerFechaHoraMasCercana() {
        const ahora = new Date();
        ahora.setHours(ahora.getHours() + 1); // Agregar 1 hora de anticipación
        
        let fecha = new Date(ahora);
        let hora = ahora.getHours();
        
        // Redondear a la siguiente hora en punto
        hora = Math.ceil(hora);
        
        // Si la hora es 24 o superior, pasar al siguiente día
        if (hora >= 24) {
            fecha.setDate(fecha.getDate() + 1);
            hora = 0;
        }
        
        return {
            fecha: fecha.toISOString().split('T')[0],
            hora: hora.toString().padStart(2, '0') + ':00'
        };
    }
    
    // Función para actualizar opciones de hora disponibles
    function actualizarHorasDisponibles() {
        const fechaSeleccionada = $('#fecha').val();
        const fechaHoy = '<?php echo date('Y-m-d'); ?>';
        const horaSelect = $('#hora');
        
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
            
            // Si la hora seleccionada ya no está disponible, limpiar
            const horaActual = horaSelect.val();
            if (horaActual) {
                const horaSeleccionada = parseInt(horaActual.split(':')[0]);
                if (horaSeleccionada < horaMinima) {
                    horaSelect.val('');
                }
            }
        }
    }
    
    // Establecer fecha y hora inicial más cercana
    $(document).ready(function() {
        const fechaHoraMasCercana = obtenerFechaHoraMasCercana();
        $('#fecha').val(fechaHoraMasCercana.fecha);
        $('#hora').val(fechaHoraMasCercana.hora);
        actualizarHorasDisponibles();
    });
    
    // Validar cuando cambia la fecha
    $('#fecha').on('change', function() {
        const fechaSeleccionada = new Date($(this).val());
        const fechaHoy = new Date();
        fechaHoy.setHours(0, 0, 0, 0);
        
        if (fechaSeleccionada < fechaHoy) {
            Swal.fire({
                icon: 'warning',
                title: 'Fecha no válida',
                text: 'No puede seleccionar una fecha anterior a hoy',
                confirmButtonText: 'Entendido'
            });
            const fechaHoraMasCercana = obtenerFechaHoraMasCercana();
            $(this).val(fechaHoraMasCercana.fecha);
        }
        
        actualizarHorasDisponibles();
    });

    // Validar cuando cambia la hora
    $('#hora').on('change', function() {
        const hora = $(this).val();
        const fechaSeleccionada = $('#fecha').val();
        const fechaHoy = '<?php echo date('Y-m-d'); ?>';
        
        if (hora && fechaSeleccionada === fechaHoy) {
            const [h] = hora.split(':').map(Number);
            const ahora = new Date();
            const horaMinima = ahora.getHours() + 1;
            
            if (h <= horaMinima) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Anticipación requerida',
                    text: 'Las reservas deben hacerse con al menos 1 hora de anticipación',
                    confirmButtonText: 'Entendido'
                });
                const fechaHoraMasCercana = obtenerFechaHoraMasCercana();
                $('#fecha').val(fechaHoraMasCercana.fecha);
                $(this).val(fechaHoraMasCercana.hora);
                actualizarHorasDisponibles();
            }
        }
    });

    // Verificar disponibilidad antes de enviar
    $('#formReserva').on('submit', function(e) {
        const disponible = $('#mensajeDisponibilidad').data('disponible');
        const hora = $('#hora').val();
        const fecha = $('#fecha').val();
        const fechaHoy = '<?php echo date('Y-m-d'); ?>';
        
        // Validar que no sea fecha pasada
        const fechaSeleccionada = new Date(fecha);
        const fechaActual = new Date(fechaHoy);
        fechaActual.setHours(0, 0, 0, 0);
        
        if (fechaSeleccionada < fechaActual) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Fecha no válida',
                text: 'No puede crear reservas para fechas pasadas',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Validar anticipación de 1 hora
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

    function verificarDisponibilidad() {
        const mesa_id = $('#mesa_id').val();
        const fecha = $('#fecha').val();
        const hora = $('#hora').val();
        
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
                hora: hora
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
});
</script>
