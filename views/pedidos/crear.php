<?php
$pageTitle = 'Crear Pedido - POS';
include __DIR__ . '/../layouts/header.php';
?>

<style>
.producto-card { cursor: pointer; transition: transform 0.2s; }
.producto-card:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
.producto-imagen { width: 100%; height: 120px; object-fit: cover; }
.carrito-item { border-bottom: 1px solid #dee2e6; padding: 10px 0; }
#resumen-pedido { position: sticky; top: 20px; }
.nav-tabs .nav-link { color: #495057; background-color: #f8f9fa; border: 1px solid #dee2e6; }
.nav-tabs .nav-link:hover { color: #0d6efd; background-color: #e9ecef; }
.nav-tabs .nav-link.active { color: #fff; background-color: #0d6efd; border-color: #0d6efd; font-weight: 600; }
</style>

<div class="page-header">
    <h1><i class="fas fa-cash-register me-2"></i> Punto de Venta - Nuevo Pedido</h1>
</div>

<div class="container-fluid">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form id="formPedido" method="POST" action="index.php?action=pedidos_guardar">
        <div class="row">
            <!-- Panel de productos -->
            <div class="col-md-8">
                <!-- Tipo de pedido y datos -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label"><strong>Tipo de Pedido</strong></label>
                                <select name="tipo" id="tipoPedido" class="form-select" required>
                                    <option value="mesa">Mesa</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="para_llevar">Para Llevar</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="mesaSelect">
                                <label class="form-label">Mesa</label>
                                <select name="mesa_id" id="mesa_id" class="form-select">
                                    <option value="">Seleccionar mesa</option>
                                    <?php foreach ($mesas as $mesa): ?>
                                        <option value="<?php echo $mesa['id']; ?>">Mesa <?php echo $mesa['numero']; ?> (<?php echo $mesa['capacidad']; ?> pers.)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6" id="clienteSelect" style="display:none;">
                                <label class="form-label">Cliente (teléfono)</label>
                                <div class="input-group">
                                    <input type="text" id="telefonoCliente" class="form-control" placeholder="Buscar por teléfono">
                                    <button type="button" class="btn btn-primary" id="btnBuscarCliente">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="cliente_id" id="cliente_id">
                                <small id="clienteInfo" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs de categorías y productos -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active" id="productos-tab-btn" data-bs-toggle="tab" data-bs-target="#productos-tab" role="tab">
                                    <i class="fas fa-fish"></i> Productos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" id="combos-tab-btn" data-bs-toggle="tab" data-bs-target="#combos-tab" role="tab">
                                    <i class="fas fa-box-open"></i> Combos
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Productos -->
                            <div class="tab-pane fade show active" id="productos-tab" role="tabpanel">
                                <?php if (!empty($productos)): ?>
                                <!-- Filtro de categorías -->
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-filter"></i> Filtrar por categoría:</label>
                                    <select id="filtroCategoria" class="form-select" style="max-width: 300px;">
                                        <option value="">Todas las categorías</option>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row g-3" id="productos-container">
                                    <?php foreach ($productos as $prod): ?>
                                        <div class="col-md-3 producto-item" data-categoria="<?php echo $prod['categoria_id']; ?>">
                                            <div class="card producto-card" onclick="agregarAlCarrito('producto', <?php echo $prod['id']; ?>, '<?php echo addslashes($prod['nombre']); ?>', <?php echo $prod['precio']; ?>)">
                                                <?php if ($prod['imagen_url']): ?>
                                                    <img src="<?php echo BASE_URL . $prod['imagen_url']; ?>" class="card-img-top producto-imagen" alt="<?php echo $prod['nombre']; ?>">
                                                <?php else: ?>
                                                    <div class="bg-secondary text-white producto-imagen d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-fish fa-3x"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-1"><?php echo $prod['nombre']; ?></h6>
                                                    <p class="card-text mb-0"><strong>S/ <?php echo number_format($prod['precio'], 2); ?></strong></p>
                                                    <small class="text-muted"><?php echo $prod['categoria_nombre']; ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-fish fa-3x mb-3"></i>
                                    <p>No hay productos disponibles</p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Combos -->
                            <div class="tab-pane fade" id="combos-tab" role="tabpanel">
                                <?php if (!empty($combos)): ?>
                                <div class="row g-3">
                                    <?php foreach ($combos as $combo): ?>
                                        <div class="col-md-3">
                                            <div class="card producto-card" onclick="agregarAlCarrito('combo', <?php echo $combo['id']; ?>, '<?php echo addslashes($combo['nombre']); ?>', <?php echo $combo['precio']; ?>)">
                                                <?php if ($combo['imagen_url']): ?>
                                                    <img src="<?php echo BASE_URL . $combo['imagen_url']; ?>" class="card-img-top producto-imagen" alt="<?php echo $combo['nombre']; ?>">
                                                <?php else: ?>
                                                    <div class="bg-info text-white producto-imagen d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-box-open fa-3x"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-1"><?php echo $combo['nombre']; ?></h6>
                                                    <p class="card-text mb-0"><strong>S/ <?php echo number_format($combo['precio'], 2); ?></strong></p>
                                                    <small class="text-muted">Combo</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                    <p>No hay combos disponibles</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de resumen -->
            <div class="col-md-4">
                <div id="resumen-pedido">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Carrito</h5>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <div id="carrito-items">
                                <p class="text-muted text-center">Carrito vacío</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="mb-2">
                                <strong>Subtotal:</strong>
                                <span class="float-end">S/ <span id="subtotal">0.00</span></span>
                            </div>
                            <div class="mb-2" id="costoEnvioDiv" style="display:none;">
                                <label>Costo Envío:</label>
                                <input type="number" name="costo_envio" id="costo_envio" class="form-control form-control-sm" value="0" step="0.01" min="0">
                            </div>
                            <div class="mb-2">
                                <label>Descuento:</label>
                                <input type="number" name="descuento" id="descuento" class="form-control form-control-sm" value="0" step="0.01" min="0">
                            </div>
                            <hr>
                            <div class="mb-3">
                                <h4><strong>Total:</strong> <span class="float-end text-success">S/ <span id="total">0.00</span></span></h4>
                            </div>
                            <div class="mb-2">
                                <label>Notas:</label>
                                <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones del pedido"></textarea>
                            </div>
                            <input type="hidden" name="items" id="items">
                            <button type="submit" class="btn btn-success w-100" id="btnGuardarPedido">
                                <i class="fas fa-check"></i> Crear Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoCliente">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre*</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono*</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia</label>
                        <input type="text" name="referencia" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Crear Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
let carrito = [];

// Cambiar tipo de pedido
$("#tipoPedido").on("change", function() {
    const tipo = $(this).val();
    if (tipo === "mesa") {
        $("#mesaSelect").show();
        $("#clienteSelect").hide();
        $("#costoEnvioDiv").hide();
        $("#mesa_id").prop("required", true);
    } else {
        $("#mesaSelect").hide();
        $("#clienteSelect").show();
        $("#mesa_id").prop("required", false);
        if (tipo === "delivery") {
            $("#costoEnvioDiv").show();
        } else {
            $("#costoEnvioDiv").hide();
        }
    }
});

// Agregar al carrito
function agregarAlCarrito(tipo, id, nombre, precio) {
    const existe = carrito.find(item => item.tipo === tipo && item.id === id);
    
    if (existe) {
        existe.cantidad++;
    } else {
        carrito.push({
            tipo: tipo,
            id: id,
            nombre: nombre,
            precio_unitario: precio,
            cantidad: 1,
            notas: ""
        });
    }
    
    actualizarCarrito();
}

// Actualizar carrito
function actualizarCarrito() {
    const contenedor = $("#carrito-items");
    contenedor.empty();
    
    if (carrito.length === 0) {
        contenedor.html("<p class=\"text-muted text-center\">Carrito vacío</p>");
        $("#items").val("");
        calcularTotales();
        return;
    }
    
    carrito.forEach((item, index) => {
        const subtotal = item.precio_unitario * item.cantidad;
        item.subtotal = subtotal;
        
        const html = `
            <div class="carrito-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>${item.nombre}</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="input-group input-group-sm mb-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
                    <input type="number" class="form-control text-center" value="${item.cantidad}" min="1" readonly>
                    <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
                </div>
                <div class="d-flex justify-content-between">
                    <small>S/ ${item.precio_unitario.toFixed(2)} c/u</small>
                    <strong>S/ ${subtotal.toFixed(2)}</strong>
                </div>
            </div>
        `;
        contenedor.append(html);
    });
    
    $("#items").val(JSON.stringify(carrito));
    calcularTotales();
}

function cambiarCantidad(index, cambio) {
    carrito[index].cantidad += cambio;
    if (carrito[index].cantidad < 1) carrito[index].cantidad = 1;
    actualizarCarrito();
}

function eliminarItem(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

function calcularTotales() {
    const subtotal = carrito.reduce((sum, item) => sum + item.subtotal, 0);
    const costoEnvio = parseFloat($("#costo_envio").val()) || 0;
    const descuento = parseFloat($("#descuento").val()) || 0;
    const total = subtotal + costoEnvio - descuento;
    
    $("#subtotal").text(subtotal.toFixed(2));
    $("#total").text(total.toFixed(2));
}

$("#costo_envio, #descuento").on("input", calcularTotales);

// Buscar cliente
$("#btnBuscarCliente").on("click", function() {
    const telefono = $("#telefonoCliente").val();
    if (!telefono) {
        Swal.fire("Error", "Ingrese un teléfono", "error");
        return;
    }
    
    $.get("index.php?action=pedidos_buscarCliente&telefono=" + telefono, function(response) {
        if (response.success) {
            $("#cliente_id").val(response.data.id);
            $("#clienteInfo").html("<strong>" + response.data.nombre + "</strong>");
            Swal.fire("Encontrado", "Cliente: " + response.data.nombre, "success");
        } else {
            Swal.fire("No encontrado", "Cliente no existe. Puede crear uno nuevo.", "info");
            $("#cliente_id").val("");
            $("#clienteInfo").text("");
        }
    }, "json");
});

// Crear cliente rápido
$("#formNuevoCliente").on("submit", function(e) {
    e.preventDefault();
    
    $.post("index.php?action=pedidos_crearClienteRapido", $(this).serialize(), function(response) {
        if (response.success) {
            $("#cliente_id").val(response.data.id);
            $("#telefonoCliente").val(response.data.telefono);
            $("#clienteInfo").html("<strong>" + response.data.nombre + "</strong>");
            $("#modalNuevoCliente").modal("hide");
            $("#formNuevoCliente")[0].reset();
            Swal.fire("Éxito", response.message, "success");
        } else {
            Swal.fire("Error", response.message, "error");
        }
    }, "json");
});

// Filtro de categorías
$("#filtroCategoria").on("change", function() {
    const categoriaId = $(this).val();
    
    if (categoriaId === "") {
        // Mostrar todos los productos
        $(".producto-item").show();
    } else {
        // Ocultar todos
        $(".producto-item").hide();
        // Mostrar solo los de la categoría seleccionada
        $(".producto-item[data-categoria=\'" + categoriaId + "\']").show();
    }
});

// Validar antes de enviar
$("#formPedido").on("submit", function(e) {
    if (carrito.length === 0) {
        e.preventDefault();
        Swal.fire("Error", "Agregue al menos un producto al pedido", "error");
        return false;
    }
    
    const tipo = $("#tipoPedido").val();
    if (tipo === "mesa" && !$("#mesa_id").val()) {
        e.preventDefault();
        Swal.fire("Error", "Seleccione una mesa", "error");
        return false;
    }
    
    if (tipo === "delivery" && !$("#cliente_id").val()) {
        e.preventDefault();
        Swal.fire("Error", "Busque o cree un cliente", "error");
        return false;
    }
    
    return true;
});
</script>
';
include __DIR__ . '/../layouts/footer.php';
?>
