<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%) !important;
            box-shadow: 0 4px 12px rgba(0,131,143,0.3);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card-header {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            padding: 1.2rem;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
            color: #00838f;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0f7fa;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #00acc1;
            box-shadow: 0 0 0 0.2rem rgba(0,172,193,0.25);
        }
        .summary-card {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            position: sticky;
            top: 20px;
        }
        .summary-divider {
            border-color: rgba(255,255,255,0.3);
            margin: 1rem 0;
        }
        .btn-confirm {
            background: white;
            color: #00838f;
            font-weight: 600;
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: #e0f7fa;
        }
        .alert-warning {
            background: rgba(255,193,7,0.2);
            border: 2px solid #ffc107;
            border-radius: 10px;
            color: #856404;
        }
        .saved-address {
            cursor: pointer;
            padding: 0.8rem;
            border-radius: 10px;
            background: #e0f7fa;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .saved-address:hover {
            background: #b2ebf2;
            transform: translateX(5px);
        }
        .zone-info {
            background: #e0f7fa;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
            display: none;
        }
        .zone-info.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php?action=portal">
                <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="<?php echo APP_NAME; ?>" style="height: 50px; width: auto;">
            </a>
            <div>
                <a href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=carrito" class="btn btn-light me-2">
                    <i class="fas fa-shopping-cart me-2"></i>Volver al Carrito
                </a>
                <a href="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=logout" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container py-5">
        <h2 class="text-center mb-5" style="color: #00838f; font-weight: 700;">
            <i class="fas fa-credit-card me-2"></i>Finalizar Pedido
        </h2>
        
        <form action="<?php echo BASE_URL; ?>index.php?controller=Portal&action=procesarPedido" method="POST" id="checkoutForm">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <!-- Datos de Entrega -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Datos de Entrega</h5>
                        </div>
                        <div class="card-body">
                            <!-- Zona de Entrega -->
                            <div class="mb-3">
                                <label class="form-label">Zona de Entrega *</label>
                                <select name="zona_id" id="zona_id" class="form-select" required onchange="calcularEnvio(this.value)">
                                    <option value="">Seleccione zona de entrega</option>
                                    <?php 
                                    if (empty($zonas)) {
                                        echo '<option value="" disabled>No hay zonas disponibles</option>';
                                    } else {
                                        foreach ($zonas as $zona): 
                                    ?>
                                        <option value="<?php echo $zona['id']; ?>" 
                                                data-costo="<?php echo $zona['costo_envio']; ?>"
                                                data-tiempo="<?php echo $zona['tiempo_estimado']; ?>"
                                                data-descripcion="<?php echo htmlspecialchars($zona['descripcion'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($zona['nombre']); ?> 
                                            (S/ <?php echo number_format($zona['costo_envio'], 2); ?> - 
                                            <?php echo $zona['tiempo_estimado']; ?> min)
                                        </option>
                                    <?php 
                                        endforeach;
                                    }
                                    ?>
                                </select>
                                <?php if (empty($zonas)): ?>
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No hay zonas de delivery configuradas. Por favor contacte al administrador.
                                    </div>
                                <?php endif; ?>
                                <div id="zone-info" class="zone-info">
                                    <strong><i class="fas fa-info-circle me-2"></i>Información de la zona:</strong>
                                    <p id="zone-description" class="mb-0 mt-2"></p>
                                </div>
                            </div>
                            
                            <!-- Direcciones Guardadas -->
                            <?php if (!empty($direcciones)): ?>
                                <div class="mb-3">
                                    <label class="form-label">Direcciones Guardadas</label>
                                    <div id="direcciones-guardadas">
                                        <?php foreach ($direcciones as $dir): ?>
                                            <div class="saved-address" onclick="seleccionarDireccion('<?php echo htmlspecialchars($dir['direccion']); ?>', '<?php echo htmlspecialchars($dir['referencia'] ?? ''); ?>')">
                                                <i class="fas fa-map-marker-alt me-2" style="color: #00acc1;"></i>
                                                <strong><?php echo htmlspecialchars($dir['direccion']); ?></strong>
                                                <?php if ($dir['principal']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Principal</span>
                                                <?php endif; ?>
                                                <?php if (!empty($dir['referencia'])): ?>
                                                    <br><small class="text-muted ms-4"><?php echo htmlspecialchars($dir['referencia']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Dirección -->
                            <div class="mb-3">
                                <label class="form-label">Dirección Completa *</label>
                                <textarea name="direccion" id="direccion" class="form-control" rows="2" required 
                                          placeholder="Ingrese su dirección completa"></textarea>
                            </div>
                            
                            <!-- Referencia -->
                            <div class="mb-3">
                                <label class="form-label">Referencia</label>
                                <input type="text" name="referencia" id="referencia" class="form-control" 
                                       placeholder="Ej: Casa verde, portón negro, al costado de...">
                                <small class="text-muted">Ayuda al repartidor a encontrar tu ubicación</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Método de Pago -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Método de Pago</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Seleccione método de pago *</label>
                                <select name="metodo_pago_id" class="form-select" required>
                                    <?php foreach ($metodos_pago as $metodo): ?>
                                        <option value="<?php echo $metodo['id']; ?>">
                                            <?php echo htmlspecialchars($metodo['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notas Adicionales</label>
                                <textarea name="notas" class="form-control" rows="3" 
                                          placeholder="Instrucciones especiales para tu pedido (opcional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen del Pedido -->
                <div class="col-lg-4">
                    <div class="card summary-card">
                        <div class="card-body">
                            <h5 class="mb-4"><i class="fas fa-receipt me-2"></i>Resumen del Pedido</h5>
                            
                            <!-- Items -->
                            <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($items as $item): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small><?php echo $item['cantidad']; ?>x <?php echo htmlspecialchars($item['nombre']); ?></small>
                                        <small>S/ <?php echo number_format($item['subtotal'], 2); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <hr class="summary-divider">
                            
                            <!-- Subtotal -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>S/ <?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            
                            <!-- Envío -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span id="costo-envio">S/ 0.00</span>
                            </div>
                            
                            <hr class="summary-divider">
                            
                            <!-- Total -->
                            <div class="d-flex justify-content-between mb-3">
                                <strong style="font-size: 1.2rem;">Total:</strong>
                                <strong style="font-size: 1.2rem;" id="total-final">S/ <?php echo number_format($subtotal, 2); ?></strong>
                            </div>
                            
                            <!-- Tiempo estimado -->
                            <div class="text-center mb-3 p-2" style="background: rgba(255,255,255,0.2); border-radius: 10px;">
                                <i class="fas fa-clock me-2"></i>
                                <small>Tiempo estimado: <span id="tiempo-entrega">30-45</span> min</small>
                            </div>
                            
                            <!-- Advertencia monto mínimo -->
                            <?php if ($subtotal < $monto_minimo): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small>Monto mínimo: S/ <?php echo number_format($monto_minimo, 2); ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Botón confirmar -->
                            <button type="submit" class="btn btn-confirm w-100" id="btnConfirmar"
                                    <?php echo ($subtotal < $monto_minimo) ? 'disabled' : ''; ?>>
                                <i class="fas fa-check-circle me-2"></i>Confirmar Pedido
                            </button>
                            
                            <div class="text-center mt-3">
                                <small><i class="fas fa-shield-alt me-1"></i>Transacción segura</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <script>
        const subtotal = <?php echo $subtotal; ?>;
        
        function calcularEnvio(zonaId) {
            const select = document.querySelector(`#zona_id option[value="${zonaId}"]`);
            if (select) {
                const costo = parseFloat(select.dataset.costo);
                const tiempo = select.dataset.tiempo;
                const descripcion = select.dataset.descripcion;
                const total = subtotal + costo;
                
                document.getElementById('costo-envio').textContent = 'S/ ' + costo.toFixed(2);
                document.getElementById('total-final').textContent = 'S/ ' + total.toFixed(2);
                document.getElementById('tiempo-entrega').textContent = tiempo;
                
                const zoneInfo = document.getElementById('zone-info');
                const zoneDescription = document.getElementById('zone-description');
                if (descripcion) {
                    zoneDescription.textContent = descripcion;
                    zoneInfo.classList.add('active');
                } else {
                    zoneInfo.classList.remove('active');
                }
            }
        }
        
        function seleccionarDireccion(direccion, referencia) {
            document.getElementById('direccion').value = direccion;
            document.getElementById('referencia').value = referencia || '';
            
            // Scroll suave a la dirección
            document.getElementById('direccion').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Validación del formulario
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const zonaId = document.getElementById('zona_id').value;
            const direccion = document.getElementById('direccion').value.trim();
            
            if (!zonaId) {
                e.preventDefault();
                alert('Por favor seleccione una zona de entrega');
                return false;
            }
            
            if (!direccion) {
                e.preventDefault();
                alert('Por favor ingrese su dirección completa');
                return false;
            }
            
            // Confirmación
            if (!confirm('¿Confirmar pedido?')) {
                e.preventDefault();
                return false;
            }
            
            // Deshabilitar botón para evitar doble envío
            document.getElementById('btnConfirmar').disabled = true;
            document.getElementById('btnConfirmar').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        });
    </script>
</body>
</html>
