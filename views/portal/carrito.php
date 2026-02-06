<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%); min-height: 100vh; }
        .navbar { background: linear-gradient(135deg, #00838f 0%, #00acc1 100%); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        .navbar-brand, .nav-link { color: white !important; font-weight: 600; }
        .navbar-brand img { height: 50px; width: auto; }
        .cart-container { max-width: 1200px; margin: 30px auto; padding: 0 15px; }
        .cart-card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .cart-item { display: flex; align-items: center; padding: 20px 0; border-bottom: 1px solid #eee; }
        .cart-item:last-child { border-bottom: none; }
        .item-image { width: 100px; height: 100px; object-fit: cover; border-radius: 12px; margin-right: 20px; }
        .item-info { flex-grow: 1; }
        .item-title { font-size: 1.2rem; font-weight: 700; color: #00838f; margin-bottom: 5px; }
        .item-price { color: #00acc1; font-size: 1.1rem; font-weight: 600; }
        .quantity-control { display: flex; align-items: center; gap: 10px; }
        .quantity-btn { width: 35px; height: 35px; border: none; background: #00acc1; color: white; border-radius: 8px; cursor: pointer; }
        .quantity-btn:hover { background: #00838f; }
        .quantity-input { width: 60px; text-align: center; border: 2px solid #ddd; border-radius: 8px; padding: 5px; }
        .btn-remove { background: #ff5252; color: white; border: none; padding: 8px 15px; border-radius: 8px; }
        .summary-card { background: linear-gradient(135deg, #00838f 0%, #00acc1 100%); color: white; border-radius: 20px; padding: 30px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 1.1rem; }
        .summary-total { font-size: 1.5rem; font-weight: 700; padding-top: 15px; border-top: 2px solid rgba(255,255,255,0.3); }
        .btn-checkout { background: white; color: #00838f; border: none; padding: 15px; font-size: 1.2rem; font-weight: 700; border-radius: 12px; width: 100%; }
        .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .empty-cart { text-align: center; padding: 60px 20px; }
        .empty-cart i { font-size: 5rem; color: #00acc1; margin-bottom: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>portal">
                <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="<?php echo APP_NAME; ?>">
            </a>
            <div class="ms-auto">
                <a href="<?php echo BASE_URL; ?>portal" class="btn btn-light"><i class="fas fa-arrow-left me-2"></i>Seguir Comprando</a>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <h2 class="text-center mb-4" style="color: #00838f; font-weight: 700;"><i class="fas fa-shopping-cart me-2"></i>Mi Carrito</h2>
        
        <?php if (empty($items)): ?>
            <div class="cart-card empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Tu carrito está vacío</h3>
                <p class="text-muted">¡Agrega algunos deliciosos platos!</p>
                <a href="<?php echo BASE_URL; ?>portal" class="btn btn-primary mt-3"><i class="fas fa-utensils me-2"></i>Ver Menú</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-card">
                        <?php foreach ($items as $item): ?>
                            <div class="cart-item">
                                <img src="<?php echo BASE_URL . ($item['imagen_producto'] ?: 'public/images/default-plato.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>" 
                                     class="item-image">
                                <div class="item-info">
                                    <div class="item-title"><?php echo htmlspecialchars($item['nombre_producto']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($item['categoria_producto']); ?></div>
                                    <div class="item-price mt-2">S/ <?php echo number_format($item['precio_unitario'], 2); ?></div>
                                    <?php if ($item['notas']): ?>
                                        <small class="text-muted"><i>Nota: <?php echo htmlspecialchars($item['notas']); ?></i></small>
                                    <?php endif; ?>
                                </div>
                                <div class="quantity-control">
                                    <button class="quantity-btn" onclick="actualizarCantidad(<?php echo $item['id']; ?>, <?php echo $item['cantidad'] - 1; ?>)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" value="<?php echo $item['cantidad']; ?>" min="1" readonly>
                                    <button class="quantity-btn" onclick="actualizarCantidad(<?php echo $item['id']; ?>, <?php echo $item['cantidad'] + 1; ?>)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold" style="color: #00838f;">S/ <?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?></div>
                                    <button class="btn-remove mt-2" onclick="eliminarItem(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card">
                        <h4 class="mb-4"><i class="fas fa-receipt me-2"></i>Resumen del Pedido</h4>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">S/ <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Costo de envío:</span>
                            <span><small>(Se calcula en checkout)</small></span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span id="total">S/ <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <?php if ($isLoggedIn): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Portal&action=checkout" class="btn btn-checkout mt-4">
                                <i class="fas fa-check-circle me-2"></i>Proceder al Checkout
                            </a>
                        <?php else: ?>
                            <div class="alert alert-info mt-3 mb-3" style="background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.4);">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Inicia sesión para finalizar tu pedido</small>
                            </div>
                            <a href="<?php echo BASE_URL; ?>login" class="btn btn-checkout mt-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=mostrarRegistro" class="btn btn-checkout mt-2" style="background: transparent; border: 2px solid white;">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </a>
                        <?php endif; ?>
                        <div class="text-center mt-3">
                            <small style="opacity: 0.9;">Tiempo de entrega: 30-45 min</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function actualizarCantidad(itemId, cantidad) {
            const formData = new FormData();
            formData.append('item_id', itemId);
            formData.append('cantidad', cantidad);

            fetch('<?php echo BASE_URL; ?>index.php?controller=Portal&action=actualizarCantidad', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }

        function eliminarItem(itemId) {
            if (!confirm('¿Eliminar este producto del carrito?')) return;

            const formData = new FormData();
            formData.append('item_id', itemId);

            fetch('<?php echo BASE_URL; ?>index.php?controller=Portal&action=eliminarItem', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    </script>
</body>
</html>
