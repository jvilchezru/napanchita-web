<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?php echo $pedido['id']; ?> - <?php echo APP_NAME; ?></title>
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
        .page-header {
            color: #00838f;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        .status-timeline {
            position: relative;
            padding: 2rem 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 3rem;
            padding-bottom: 2rem;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 2rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: #e0f7fa;
        }
        .timeline-item:last-child::before {
            display: none;
        }
        .timeline-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0,131,143,0.3);
        }
        .timeline-icon.active {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 4px 10px rgba(0,131,143,0.3);
            }
            50% {
                box-shadow: 0 4px 20px rgba(0,172,193,0.6);
            }
        }
        .timeline-icon.inactive {
            background: #e0f7fa;
            color: #00838f;
        }
        .item-pedido {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .item-pedido:hover {
            background: #e0f7fa;
            transform: translateX(5px);
        }
        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 600;
            color: #00838f;
            margin-bottom: 0.5rem;
        }
        .total-section {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        .btn-volver {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-volver:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,131,143,0.4);
            color: white;
        }
        .repartidor-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
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
                <a href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=mis-pedidos" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Mis Pedidos
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-header mb-0">
                <i class="fas fa-receipt me-2"></i>Pedido #<?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?>
            </h2>
            <div class="text-end">
                <div class="text-muted small">Fecha</div>
                <div><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></div>
            </div>
        </div>
        
        <div class="row">
            <!-- Columna Izquierda: Timeline y Detalles -->
            <div class="col-lg-7 mb-4">
                <!-- Estado del Pedido -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-tasks me-2"></i>Estado del Pedido</h5>
                        <div class="status-timeline">
                            <?php 
                            $estados = [
                                'pendiente' => ['icon' => 'fa-clock', 'label' => 'Pedido Recibido', 'desc' => 'Tu pedido ha sido recibido'],
                                'en_preparacion' => ['icon' => 'fa-utensils', 'label' => 'En Preparación', 'desc' => 'Estamos preparando tu pedido'],
                                'listo' => ['icon' => 'fa-check', 'label' => 'Listo', 'desc' => 'Tu pedido está listo'],
                            ];
                            
                            if ($pedido['tipo'] === 'delivery') {
                                $estados['en_camino'] = ['icon' => 'fa-motorcycle', 'label' => 'En Camino', 'desc' => 'El repartidor está en camino'];
                                $estados['entregado'] = ['icon' => 'fa-check-circle', 'label' => 'Entregado', 'desc' => 'Pedido entregado'];
                            } else {
                                $estados['entregado'] = ['icon' => 'fa-check-circle', 'label' => 'Entregado', 'desc' => 'Pedido retirado'];
                            }
                            
                            $estado_actual = $pedido['estado'];
                            $estados_keys = array_keys($estados);
                            $index_actual = array_search($estado_actual, $estados_keys);
                            
                            foreach ($estados as $key => $info):
                                $index = array_search($key, $estados_keys);
                                $is_active = ($key === $estado_actual);
                                $is_completed = ($index < $index_actual);
                                $is_future = ($index > $index_actual);
                            ?>
                                <div class="timeline-item">
                                    <div class="timeline-icon <?php echo $is_active ? 'active' : ($is_future ? 'inactive' : ''); ?>">
                                        <i class="fas <?php echo $info['icon']; ?>"></i>
                                    </div>
                                    <div>
                                        <strong style="color: <?php echo $is_active ? '#00acc1' : ($is_future ? '#999' : '#00838f'); ?>">
                                            <?php echo $info['label']; ?>
                                        </strong>
                                        <div class="text-muted small"><?php echo $info['desc']; ?></div>
                                        <?php if ($is_active || $is_completed): ?>
                                            <div class="text-muted small mt-1">
                                                <i class="far fa-clock me-1"></i>
                                                <?php 
                                                $fecha_estado = $pedido['fecha_' . $key] ?? $pedido['fecha_pedido'];
                                                echo date('d/m/Y H:i', strtotime($fecha_estado)); 
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Items del Pedido -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-utensils me-2"></i>Detalle del Pedido</h5>
                        <?php foreach ($items as $item): ?>
                            <div class="item-pedido">
                                <div>
                                    <strong><?php echo $item['cantidad']; ?>x</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                    <?php if ($item['tipo'] === 'combo'): ?>
                                        <span class="badge bg-info text-dark ms-2">Combo</span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['nota'])): ?>
                                        <div class="text-muted small mt-1">
                                            <i class="fas fa-sticky-note me-1"></i><?php echo htmlspecialchars($item['nota']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <div style="color: #00838f; font-weight: 600;">
                                        S/ <?php echo number_format($item['subtotal'], 2); ?>
                                    </div>
                                    <small class="text-muted">S/ <?php echo number_format($item['precio_unitario'], 2); ?> c/u</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (!empty($pedido['notas'])): ?>
                            <div class="mt-3 p-2" style="background: #fff8e1; border-radius: 10px;">
                                <strong><i class="fas fa-comment-alt me-2"></i>Notas:</strong>
                                <div><?php echo htmlspecialchars($pedido['notas']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Columna Derecha: Información -->
            <div class="col-lg-5">
                <!-- Información de Entrega -->
                <?php if ($pedido['tipo'] === 'delivery'): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Información de Entrega</h5>
                            <div class="info-card">
                                <div class="info-label">Dirección</div>
                                <div><?php echo htmlspecialchars($pedido['direccion_entrega']); ?></div>
                                <?php if (!empty($pedido['referencia_entrega'])): ?>
                                    <div class="text-muted small mt-2">
                                        <i class="fas fa-info-circle me-1"></i><?php echo htmlspecialchars($pedido['referencia_entrega']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($delivery)): ?>
                                <div class="repartidor-info">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-motorcycle fa-2x me-3"></i>
                                        <div>
                                            <div class="fw-bold">Repartidor</div>
                                            <div><?php echo htmlspecialchars($delivery['repartidor_nombre'] ?? 'Por asignar'); ?></div>
                                        </div>
                                    </div>
                                    <?php if (!empty($delivery['telefono_repartidor'])): ?>
                                        <a href="tel:<?php echo $delivery['telefono_repartidor']; ?>" 
                                           class="btn btn-light btn-sm mt-2 w-100">
                                            <i class="fas fa-phone me-2"></i>Llamar al Repartidor
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Resumen de Pago -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Resumen de Pago</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>S/ <?php echo number_format($pedido['subtotal'], 2); ?></span>
                        </div>
                        <?php if ($pedido['tipo'] === 'delivery' && $pedido['costo_envio'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span>S/ <?php echo number_format($pedido['costo_envio'], 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($pedido['descuento']) && $pedido['descuento'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Descuento:</span>
                                <span>- S/ <?php echo number_format($pedido['descuento'], 2); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="total-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Total:</h4>
                                <h3 class="mb-0">S/ <?php echo number_format($pedido['total'], 2); ?></h3>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="info-label">Método de Pago</div>
                            <div>
                                <i class="fas fa-wallet me-2"></i>
                                <?php echo htmlspecialchars($pedido['metodo_pago']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón Volver -->
                <div class="mt-4">
                    <a href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=mis-pedidos" 
                       class="btn btn-volver w-100">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Mis Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-actualizar si el pedido está en proceso
        <?php if (in_array($pedido['estado'], ['pendiente', 'en_preparacion', 'en_camino'])): ?>
            setTimeout(function() {
                location.reload();
            }, 30000); // 30 segundos
        <?php endif; ?>
    </script>
</body>
</html>
