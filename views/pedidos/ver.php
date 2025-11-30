<?php
$pageTitle = 'Detalle del Pedido #' . $pedido['id'];
include __DIR__ . '/../layouts/header.php';

?>

<div class="page-header">
    <h1><i class="fas fa-receipt me-2"></i> Pedido #<?php echo $pedido['id']; ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=pedidos">Pedidos</a></li>
            <li class="breadcrumb-item active">Pedido #<?php echo $pedido['id']; ?></li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Información del pedido -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tipo:</strong> 
                            <?php
                            $tipos = ['mesa' => 'Mesa', 'delivery' => 'Delivery', 'para_llevar' => 'Para Llevar'];
                            echo $tipos[$pedido['tipo']] ?? $pedido['tipo'];
                            ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong>
                            <?php
                            $estados = [
                                'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
                                'en_preparacion' => '<span class="badge bg-info">En Preparación</span>',
                                'listo' => '<span class="badge bg-primary">Listo</span>',
                                'entregado' => '<span class="badge bg-success">Entregado</span>',
                                'finalizado' => '<span class="badge bg-dark">Finalizado</span>',
                                'cancelado' => '<span class="badge bg-danger">Cancelado</span>'
                            ];
                            echo $estados[$pedido['estado']] ?? $pedido['estado'];
                            ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <?php if ($pedido['tipo'] == 'mesa'): ?>
                            <div class="col-md-6">
                                <strong>Mesa:</strong> <?php echo $pedido['mesa_numero'] ?? 'N/A'; ?>
                            </div>
                        <?php else: ?>
                            <div class="col-md-6">
                                <strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'N/A'); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Teléfono:</strong> <?php echo $pedido['cliente_telefono'] ?? 'N/A'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Atendido por:</strong> <?php echo $pedido['usuario_nombre']; ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                        </div>
                    </div>
                    <?php if ($pedido['notas']): ?>
                        <div class="alert alert-info mt-3 mb-0">
                            <strong><i class="fas fa-comment"></i> Notas:</strong> <?php echo htmlspecialchars($pedido['notas']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items del pedido -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Items del Pedido</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Plato/Combo</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $items = $pedido['items'] ?? [];
                            if (!empty($items) && is_array($items) && count($items) > 0): 
                            ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($item['nombre'] ?? 'Producto'); ?>
                                            <?php if (!empty($item['notas'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($item['notas']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo $item['cantidad'] ?? 0; ?></td>
                                        <td class="text-end">S/ <?php echo number_format($item['precio_unitario'] ?? 0, 2); ?></td>
                                        <td class="text-end"><strong>S/ <?php echo number_format($item['subtotal'] ?? 0, 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        <p class="mb-0">Este pedido no tiene items registrados</p>
                                        <small>Es posible que el pedido esté en proceso de creación</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Resumen y acciones -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill"></i> Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>S/ <?php echo number_format($pedido['subtotal'], 2); ?></strong>
                    </div>
                    <?php if ($pedido['costo_envio'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Costo de envío:</span>
                            <strong>S/ <?php echo number_format($pedido['costo_envio'], 2); ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($pedido['descuento'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Descuento:</span>
                            <strong>- S/ <?php echo number_format($pedido['descuento'], 2); ?></strong>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h4>Total:</h4>
                        <h4 class="text-success">S/ <?php echo number_format($pedido['total'], 2); ?></h4>
                    </div>
                </div>
            </div>

            <!-- Información de Pago (solo si está finalizado) -->
            <?php if ($pedido['estado'] == 'finalizado' && isset($venta) && $venta): ?>
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Método de Pago:</strong>
                                <span class="badge bg-primary"><?php echo htmlspecialchars($venta['metodo_pago']); ?></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha de Pago:</strong>
                                <?php echo date('d/m/Y H:i:s', strtotime($venta['fecha_venta'])); ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Total Cobrado:</strong>
                                <span class="text-success fw-bold">S/ <?php echo number_format($venta['total'], 2); ?></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Cajero:</strong>
                                <?php echo htmlspecialchars($venta['cajero']); ?>
                            </div>
                        </div>
                        <?php if ($venta['monto_recibido'] > 0 && $venta['monto_cambio'] > 0): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Monto Recibido:</strong>
                                    <span class="text-info">S/ <?php echo number_format($venta['monto_recibido'], 2); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Vuelto:</strong>
                                    <span class="text-warning">S/ <?php echo number_format($venta['monto_cambio'], 2); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Acciones -->
            <?php if ($pedido['estado'] != 'cancelado' && $pedido['estado'] != 'finalizado'): ?>
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cog"></i> Acciones</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($pedido['estado'] == 'pendiente' && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
                            <button class="btn btn-info w-100 mb-2" onclick="cambiarEstado('en_preparacion')">
                                <i class="fas fa-fire"></i> Iniciar Preparación
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'en_preparacion' && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
                            <button class="btn btn-success w-100 mb-2" onclick="cambiarEstado('listo')">
                                <i class="fas fa-check"></i> Marcar como Listo
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'listo'): ?>
                            <button class="btn btn-primary w-100 mb-2" onclick="cambiarEstado('entregado')">
                                <i class="fas fa-check-double"></i> Marcar como Entregado
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'entregado'): ?>
                            <button class="btn btn-success w-100 mb-2" onclick="cobrarYFinalizar()">
                                <i class="fas fa-cash-register"></i> Cobrar y Finalizar
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                        <button class="btn btn-danger w-100" onclick="cancelarPedido()">
                            <i class="fas fa-times"></i> Cancelar Pedido
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos" class="btn btn-secondary w-100 mt-3">
                <i class="fas fa-arrow-left"></i> Volver a Pedidos
            </a>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
function cambiarEstado(nuevoEstado) {
    $.post("index.php?action=pedidos_cambiarEstado", {
        id: ' . $pedido['id'] . ',
        estado: nuevoEstado
    }, function(response) {
        if (response.success) {
            Swal.fire("Éxito", response.message, "success")
                .then(() => location.reload());
        } else {
            Swal.fire("Error", response.message, "error");
        }
    }, "json");
}

function cancelarPedido() {
    Swal.fire({
        title: "¿Cancelar pedido?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("index.php?action=pedidos_cancelar&id=' . $pedido['id'] . '", function(response) {
                if (response.success) {
                    Swal.fire("Cancelado", response.message, "success")
                        .then(() => location.reload());
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            }, "json");
        }
    });
}

async function cobrarYFinalizar() {
    // Obtener métodos de pago
    const metodosPago = await obtenerMetodosPago();
    
    if (!metodosPago || metodosPago.length === 0) {
        Swal.fire("Error", "No hay métodos de pago disponibles", "error");
        return;
    }
    
    // Crear opciones para el select de métodos de pago
    const opcionesMetodos = metodosPago.reduce((html, metodo) => {
        return html + `<option value="${metodo.id}">${metodo.nombre}</option>`;
    }, "");
    
    const totalPedido = ' . $pedido['total'] . ';
    
    // Mostrar modal con método de pago
    const { value: formValues } = await Swal.fire({
        title: "Cobrar Pedido",
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label fw-bold">Total a cobrar:</label>
                    <h3 class="text-success">S/ ${totalPedido.toFixed(2)}</h3>
                </div>
                <div class="mb-3">
                    <label for="metodo_pago" class="form-label fw-bold">Método de pago <span class="text-danger">*</span></label>
                    <select id="metodo_pago" class="form-select">
                        ${opcionesMetodos}
                    </select>
                </div>
                <div id="efectivo-fields" style="display: none;">
                    <div class="mb-3">
                        <label for="monto_recibido" class="form-label fw-bold">Monto recibido <span class="text-danger">*</span></label>
                        <input type="number" id="monto_recibido" class="form-control" 
                               min="${totalPedido}" step="0.01" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Vuelto:</label>
                        <h4 id="vuelto" class="text-info">S/ 0.00</h4>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Cobrar y Finalizar",
        cancelButtonText: "Cancelar",
        didOpen: () => {
            const metodoPagoSelect = document.getElementById("metodo_pago");
            const efectivoFields = document.getElementById("efectivo-fields");
            const montoRecibidoInput = document.getElementById("monto_recibido");
            const vueltoDisplay = document.getElementById("vuelto");
            
            // Buscar si existe "Efectivo" en los métodos
            const efectivoMetodo = metodosPago.find(m => 
                m.nombre.toLowerCase() === "efectivo"
            );
            
            // Mostrar/ocultar campos de efectivo según el método seleccionado
            metodoPagoSelect.addEventListener("change", function() {
                const metodoSeleccionado = metodosPago.find(m => m.id == this.value);
                if (metodoSeleccionado && metodoSeleccionado.nombre.toLowerCase() === "efectivo") {
                    efectivoFields.style.display = "block";
                    montoRecibidoInput.value = totalPedido.toFixed(2);
                    montoRecibidoInput.focus();
                } else {
                    efectivoFields.style.display = "none";
                    montoRecibidoInput.value = "";
                }
            });
            
            // Calcular vuelto automáticamente
            montoRecibidoInput.addEventListener("input", function() {
                const montoRecibido = parseFloat(this.value) || 0;
                const vuelto = montoRecibido - totalPedido;
                vueltoDisplay.textContent = "S/ " + (vuelto > 0 ? vuelto.toFixed(2) : "0.00");
                
                if (vuelto < 0) {
                    vueltoDisplay.classList.remove("text-info");
                    vueltoDisplay.classList.add("text-danger");
                } else {
                    vueltoDisplay.classList.remove("text-danger");
                    vueltoDisplay.classList.add("text-info");
                }
            });
            
            // Trigger inicial si ya está en efectivo
            metodoPagoSelect.dispatchEvent(new Event("change"));
        },
        preConfirm: () => {
            const metodoPagoId = document.getElementById("metodo_pago").value;
            const metodoSeleccionado = metodosPago.find(m => m.id == metodoPagoId);
            const montoRecibido = document.getElementById("monto_recibido").value;
            
            if (!metodoPagoId) {
                Swal.showValidationMessage("Debe seleccionar un método de pago");
                return false;
            }
            
            // Si es efectivo, validar monto recibido
            if (metodoSeleccionado && metodoSeleccionado.nombre.toLowerCase() === "efectivo") {
                if (!montoRecibido || parseFloat(montoRecibido) < totalPedido) {
                    Swal.showValidationMessage("El monto recibido debe ser mayor o igual al total");
                    return false;
                }
                
                return {
                    metodo_pago_id: metodoPagoId,
                    monto_recibido: parseFloat(montoRecibido),
                    monto_cambio: parseFloat(montoRecibido) - totalPedido
                };
            }
            
            return {
                metodo_pago_id: metodoPagoId,
                monto_recibido: totalPedido,
                monto_cambio: 0
            };
        }
    });
    
    if (formValues) {
        // Enviar datos al servidor
        $.post("index.php?action=pedidos_finalizar", {
            id: ' . $pedido['id'] . ',
            metodo_pago_id: formValues.metodo_pago_id,
            monto_recibido: formValues.monto_recibido,
            monto_cambio: formValues.monto_cambio
        }, function(response) {
            if (response.success) {
                let mensaje = response.message;
                if (formValues.monto_cambio > 0) {
                    mensaje += `<br><strong>Vuelto: S/ ${formValues.monto_cambio.toFixed(2)}</strong>`;
                }
                
                Swal.fire({
                    icon: "success",
                    title: "Pedido Finalizado",
                    html: mensaje,
                    confirmButtonText: "Aceptar"
                }).then(() => {
                    window.location.href = "index.php?action=pedidos";
                });
            } else {
                Swal.fire("Error", response.message, "error");
            }
        }, "json").fail(function() {
            Swal.fire("Error", "Error al procesar la solicitud", "error");
        });
    }
}

// Función para obtener métodos de pago
async function obtenerMetodosPago() {
    try {
        const response = await $.ajax({
            url: "index.php?action=pedidos_obtenerMetodosPago",
            method: "GET",
            dataType: "json"
        });
        
        if (response.success) {
            return response.data;
        }
        return [];
    } catch (error) {
        console.error("Error al obtener métodos de pago:", error);
        return [];
    }
}
</script>
';
include __DIR__ . '/../layouts/footer.php';
?>
