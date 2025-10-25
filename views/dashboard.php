<?php
// La sesión ya está iniciada en index.php
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?action=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Napanchita</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <!-- Header Dashboard -->
    <header class="dashboard-header">
        <div class="container">
            <div class="dashboard-nav">
                <h1>🍽️ Napanchita</h1>
                <div class="user-menu">
                    <span>👤 <?php echo $_SESSION['usuario_nombre']; ?></span>
                    <a href="index.php?action=logout" class="btn btn-small">Salir</a>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="active" data-section="menu">🍽️ Menú</a></li>
                <?php if ($_SESSION['usuario_rol'] !== 'admin'): ?>
                <li><a href="#" data-section="carrito">🛒 Carrito (<span id="cartCount">0</span>)</a></li>
                <li><a href="#" data-section="pedidos">📦 Mis Pedidos</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                <li><a href="#" data-section="admin-pedidos">🔧 Gestionar Pedidos</a></li>
                <li><a href="#" data-section="admin-productos">📝 Gestionar Productos</a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Sección Menú -->
            <section id="section-menu" class="dashboard-section active">
                <div class="section-header">
                    <h2>Menú de Productos</h2>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Buscar productos...">
                    </div>
                </div>
                <div id="productosContainer" class="productos-grid">
                    <!-- Se llena dinámicamente -->
                </div>
            </section>

            <?php if ($_SESSION['usuario_rol'] !== 'admin'): ?>
            <!-- Sección Carrito -->
            <section id="section-carrito" class="dashboard-section">
                <h2>Mi Carrito</h2>
                <div id="carritoContainer" class="carrito-container">
                    <div id="carritoItems"></div>
                    <div class="carrito-total">
                        <h3>Total: Bs. <span id="carritoTotal">0.00</span></h3>
                        <button id="btnFinalizarPedido" class="btn btn-primary btn-large">Finalizar Pedido</button>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($_SESSION['usuario_rol'] !== 'admin'): ?>
            <!-- Sección Mis Pedidos -->
            <section id="section-pedidos" class="dashboard-section">
                <h2>Mis Pedidos</h2>
                <div id="pedidosContainer" class="pedidos-container">
                    <!-- Se llena dinámicamente -->
                </div>
            </section>
            <?php endif; ?>

            <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
            <!-- Sección Admin: Gestionar Pedidos -->
            <section id="section-admin-pedidos" class="dashboard-section">
                <h2>Gestión de Pedidos</h2>
                <div id="adminPedidosContainer" class="admin-pedidos-container">
                    <!-- Se llena dinámicamente -->
                </div>
            </section>

            <!-- Sección Admin: Gestionar Productos -->
            <section id="section-admin-productos" class="dashboard-section">
                <h2>Gestión de Productos</h2>
                <button id="btnNuevoProducto" class="btn btn-primary mb-20">Nuevo Producto</button>
                <div id="adminProductosContainer" class="admin-productos-container">
                    <!-- Se llena dinámicamente -->
                </div>
            </section>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal Finalizar Pedido -->
    <div id="modalPedido" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Finalizar Pedido</h2>
            <form id="formPedido">
                <div class="form-group">
                    <label>Dirección de Entrega</label>
                    <textarea id="pedidoDireccion" required></textarea>
                </div>
                <div class="form-group">
                    <label>Teléfono de Contacto</label>
                    <input type="tel" id="pedidoTelefono" required>
                </div>
                <div class="form-group">
                    <label>Notas Adicionales</label>
                    <textarea id="pedidoNotas"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Producto (Crear/Editar) -->
    <div id="modalProducto" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="modalProductoTitulo">Nuevo Producto</h2>
            <form id="formProducto">
                <input type="hidden" id="productoId">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="productoNombre" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="productoDescripcion" required></textarea>
                </div>
                <div class="form-group">
                    <label>Precio (Bs.)</label>
                    <input type="number" step="0.01" id="productoPrecio" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select id="productoCategoria" required>
                        <option value="1">Hamburguesas</option>
                        <option value="2">Pizzas</option>
                        <option value="3">Bebidas</option>
                        <option value="4">Postres</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Disponible</label>
                    <select id="productoDisponible">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pasar datos de PHP a JavaScript
        const usuarioRol = '<?php echo $_SESSION['usuario_rol']; ?>';
    </script>
    <script src="public/js/dashboard.js"></script>
</body>
</html>
