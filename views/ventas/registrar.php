<?php
$pageTitle = 'Registrar Venta';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-cash-register me-2"></i> Registrar Venta</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=ventas">Ventas</a></li>
            <li class="breadcrumb-item active">Registrar</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-auto">
            <a href="<?php echo BASE_URL; ?>index.php?action=ventas" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos de la Venta</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>index.php?action=ventas_guardar" id="formVenta">
                        <?php if (isset($pedido)): ?>
                            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Información del Pedido</h6>
                                <p class="mb-1"><strong>Pedido:</strong> #<?php echo $pedido['id']; ?></p>
                                <p class="mb-1"><strong>Tipo:</strong> <?php echo $pedido['tipo']; ?></p>
                                <p class="mb-0"><strong>Total:</strong> S/ <?php echo number_format($pedido['total'], 2); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" name="total" id="total" class="form-control"
                                        value="<?php echo $pedido['total'] ?? ''; ?>"
                                        step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Descuento</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" name="descuento" id="descuento" class="form-control"
                                        value="0" step="0.01" min="0">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                                <select name="metodo_pago_id" id="metodo_pago" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php if (!empty($metodosPago)): ?>
                                        <?php foreach ($metodosPago as $metodo): ?>
                                            <option value="<?php echo $metodo['id']; ?>" data-nombre="<?php echo htmlspecialchars($metodo['nombre']); ?>">
                                                <?php echo htmlspecialchars($metodo['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="campo_monto_recibido" style="display: none;">
                                <label class="form-label">Monto Recibido</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" name="monto_recibido" id="monto_recibido"
                                        class="form-control" step="0.01" min="0">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cambio</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="number" name="monto_cambio" id="monto_cambio"
                                        class="form-control" readonly value="0">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código de Descuento</label>
                                <input type="text" name="codigo_descuento" class="form-control"
                                    placeholder="Ej: DESC10">
                            </div>

                            <div class="col-md-12 mb-3" style="display: none;">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" class="form-control" rows="2"
                                    placeholder="Observaciones adicionales..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo BASE_URL; ?>index.php?action=ventas" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Registrar Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calculator"></i> Resumen</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong id="display_subtotal">S/ 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Descuento:</span>
                        <strong id="display_descuento">- S/ 0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total a Pagar:</h5>
                        <h4 class="text-primary" id="display_total">S/ 0.00</h4>
                    </div>

                    <div id="resumen_pago" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Monto Recibido:</span>
                            <strong id="display_recibido">S/ 0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Cambio:</span>
                            <h5 class="text-success" id="display_cambio">S/ 0.00</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const total = document.getElementById('total');
            const descuento = document.getElementById('descuento');
            const metodoPago = document.getElementById('metodo_pago');
            const montoRecibido = document.getElementById('monto_recibido');
            const montoCambio = document.getElementById('monto_cambio');
            const campoMontoRecibido = document.getElementById('campo_monto_recibido');
            const resumenPago = document.getElementById('resumen_pago');

            function actualizarResumen() {
                const totalVal = parseFloat(total.value) || 0;
                const descuentoVal = parseFloat(descuento.value) || 0;
                const totalFinal = totalVal - descuentoVal;
                const recibidoVal = parseFloat(montoRecibido.value) || 0;
                const cambioVal = Math.max(0, recibidoVal - totalFinal);

                document.getElementById('display_subtotal').textContent = 'S/ ' + totalVal.toFixed(2);
                document.getElementById('display_descuento').textContent = '- S/ ' + descuentoVal.toFixed(2);
                document.getElementById('display_total').textContent = 'S/ ' + totalFinal.toFixed(2);
                document.getElementById('display_recibido').textContent = 'S/ ' + recibidoVal.toFixed(2);
                document.getElementById('display_cambio').textContent = 'S/ ' + cambioVal.toFixed(2);

                montoCambio.value = cambioVal.toFixed(2);
            }

            metodoPago.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const nombreMetodo = selectedOption.getAttribute('data-nombre');

                if (nombreMetodo && nombreMetodo.toLowerCase().includes('efectivo')) {
                    campoMontoRecibido.style.display = 'block';
                    resumenPago.style.display = 'block';
                    montoRecibido.required = true;
                } else {
                    campoMontoRecibido.style.display = 'none';
                    resumenPago.style.display = 'none';
                    montoRecibido.required = false;
                    montoRecibido.value = '';
                    actualizarResumen();
                }
            });

            total.addEventListener('input', actualizarResumen);
            descuento.addEventListener('input', actualizarResumen);
            montoRecibido.addEventListener('input', actualizarResumen);

            actualizarResumen();
        });
    </script>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>