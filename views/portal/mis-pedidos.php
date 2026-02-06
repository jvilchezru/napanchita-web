<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - <?php echo APP_NAME; ?></title>
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
        .page-header {
            color: #00838f;
            font-weight: 700;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease;
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .pedido-card {
            border: none;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
            animation-fill-mode: both;
        }
        .pedido-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
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
        .pedido-card:nth-child(1) { animation-delay: 0.1s; }
        .pedido-card:nth-child(2) { animation-delay: 0.2s; }
        .pedido-card:nth-child(3) { animation-delay: 0.3s; }
        .pedido-card:nth-child(4) { animation-delay: 0.4s; }
        .pedido-id {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00838f;
        }
        .badge-estado {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-radius: 10px;
            font-weight: 600;
        }
        .precio-total {
            color: #00acc1;
            font-size: 1.3rem;
            font-weight: 700;
        }
        .btn-ver-detalle {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-ver-detalle:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,131,143,0.4);
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .empty-state i {
            font-size: 6rem;
            color: #00acc1;
            margin-bottom: 2rem;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }
        .pedido-tipo {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #e0f7fa;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #00838f;
            font-weight: 500;
        }
        .tracking-status {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(0,172,193,0.1);
            border-radius: 8px;
            font-size: 0.85rem;
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
                <a href="<?php echo BASE_URL; ?>index.php?action=portal" class="btn btn-light me-2">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
                <a href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=perfil" class="btn btn-light me-2">
                    <i class="fas fa-user me-2"></i>Perfil
                </a>
                <a href="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=logout" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container py-5">
        <h2 class="page-header">
            <i class="fas fa-receipt me-2"></i>Mis Pedidos
        </h2>
        
        <?php if (empty($pedidos)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h3 class="mb-3">No tienes pedidos aún</h3>
                <p class="text-muted mb-4">¡Empieza a ordenar tus platos favoritos!</p>
                <a href="<?php echo BASE_URL; ?>index.php?action=portal" class="btn btn-ver-detalle btn-lg">
                    <i class="fas fa-utensils me-2"></i>Ver Menú
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($pedidos as $index => $pedido): 
                $badge_class = match($pedido['estado']) {
                    'pendiente' => 'warning',
                    'en_preparacion' => 'info',
                    'listo' => 'primary',
                    'en_camino' => 'primary',
                    'entregado' => 'success',
                    'cancelado' => 'danger',
                    default => 'secondary'
                };
                $badge_icon = match($pedido['estado']) {
                    'pendiente' => 'fa-clock',
                    'en_preparacion' => 'fa-utensils',
                    'listo' => 'fa-check',
                    'en_camino' => 'fa-motorcycle',
                    'entregado' => 'fa-check-circle',
                    'cancelado' => 'fa-times-circle',
                    default => 'fa-info-circle'
                };
                $badge_text = match($pedido['estado']) {
                    'pendiente' => 'Pendiente',
                    'en_preparacion' => 'En Preparación',
                    'listo' => 'Listo',
                    'en_camino' => 'En Camino',
                    'entregado' => 'Entregado',
                    'cancelado' => 'Cancelado',
                    default => ucfirst($pedido['estado'])
                };
            ?>
                <div class="card pedido-card" style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- ID y Fecha -->
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                <div class="pedido-id">#<?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?></div>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?>
                                </small>
                                <br>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    <?php echo date('H:i', strtotime($pedido['fecha_pedido'])); ?>
                                </small>
                            </div>
                            
                            <!-- Estado -->
                            <div class="col-md-3 mb-3 mb-md-0">
                                <span class="badge badge-estado bg-<?php echo $badge_class; ?>">
                                    <i class="fas <?php echo $badge_icon; ?> me-1"></i>
                                    <?php echo $badge_text; ?>
                                </span>
                                <div class="mt-2">
                                    <span class="pedido-tipo">
                                        <?php if ($pedido['tipo'] === 'delivery'): ?>
                                            <i class="fas fa-motorcycle me-1"></i>Delivery
                                        <?php else: ?>
                                            <i class="fas fa-shopping-bag me-1"></i>Para Llevar
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if (isset($pedido['delivery_estado']) && $pedido['tipo'] === 'delivery'): ?>
                                    <div class="tracking-status">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <small><?php echo ucfirst(str_replace('_', ' ', $pedido['delivery_estado'])); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Detalles -->
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-box me-1"></i>
                                    <?php 
                                    $total_items = isset($pedido['total_items']) ? $pedido['total_items'] : 0;
                                    echo $total_items . ' ' . ($total_items == 1 ? 'item' : 'items'); 
                                    ?>
                                </div>
                                <?php if ($pedido['tipo'] === 'delivery' && !empty($pedido['direccion_entrega'])): ?>
                                    <div class="text-muted small">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo substr(htmlspecialchars($pedido['direccion_entrega']), 0, 30); ?>...
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Precio -->
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                <div class="text-muted small">Total</div>
                                <div class="precio-total">S/ <?php echo number_format($pedido['total'], 2); ?></div>
                            </div>
                            
                            <!-- Acción -->
                            <div class="col-md-2 text-center">
                                <a href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=ver-pedido&id=<?php echo $pedido['id']; ?>" 
                                   class="btn btn-ver-detalle w-100">
                                    <i class="fas fa-eye me-1"></i>Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Paginación si hay muchos pedidos -->
            <?php if (isset($total_paginas) && $total_paginas > 1): ?>
                <nav aria-label="Navegación de pedidos" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                                <a class="page-link" href="?action=portal&subaction=mis-pedidos&pagina=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto-actualizar cada 30 segundos si hay pedidos en proceso
        <?php if (!empty($pedidos)): ?>
            const hayPedidosActivos = <?php 
                echo json_encode(array_filter($pedidos, function($p) {
                    return in_array($p['estado'], ['pendiente', 'en_preparacion', 'en_camino']);
                })); 
            ?>.length > 0;
            
            if (hayPedidosActivos) {
                setInterval(function() {
                    location.reload();
                }, 30000); // 30 segundos
            }
        <?php endif; ?>
    </script>
</body>
</html>
