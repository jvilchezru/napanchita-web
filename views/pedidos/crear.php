<?php
$pageTitle = 'Crear Pedido - POS';
include __DIR__ . '/../layouts/header.php';
?>

<style>
.plato-card { cursor: pointer; transition: transform 0.2s; }
.plato-card:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
.plato-imagen { width: 100%; height: 120px; object-fit: cover; }
.carrito-item { border-bottom: 1px solid #dee2e6; padding: 10px 0; }
#resumen-pedido { position: sticky; top: 20px; }
.nav-tabs .nav-link { color: #495057; background-color: #f8f9fa; border: 1px solid #dee2e6; }
.nav-tabs .nav-link:hover { color: #0d6efd; background-color: #e9ecef; }
.nav-tabs .nav-link.active { color: #fff; background-color: #0d6efd; border-color: #0d6efd; font-weight: 600; }
#listaClientes { 
    box-shadow: 0 4px 12px rgba(0,0,0,0.2); 
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: white;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
}
#listaClientes::-webkit-scrollbar {
    width: 8px;
}
#listaClientes::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 0 0.375rem 0.375rem 0;
}
#listaClientes::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}
#listaClientes::-webkit-scrollbar-thumb:hover {
    background: #555;
}
#listaClientes .cliente-item { 
    cursor: pointer; 
    transition: all 0.2s;
    border: none;
    border-bottom: 1px solid #f0f0f0;
    padding: 0.75rem 1rem;
    background-color: white;
}
#listaClientes .cliente-item:last-child {
    border-bottom: none;
}
#listaClientes .cliente-item:hover,
#listaClientes .cliente-item.active { 
    background-color: #e7f3ff;
    color: #0d6efd;
    transform: translateX(3px);
}
#listaClientes .cliente-item span {
    font-size: 0.95rem;
}
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
            <!-- Panel de platos -->
            <div class="col-md-8">
                <!-- Tipo de pedido y datos -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label"><strong>Tipo de Pedido</strong></label>
                                <select name="tipo" id="tipoPedido" class="form-select" required>
                                    <option value="mesa">Mesa</option>
                                    <!-- <option value="delivery">Delivery</option> -->
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
                                <label class="form-label" id="labelBuscarCliente">Cliente (teléfono)</label>
                                <div class="position-relative">
                                    <div class="input-group">
                                        <input type="text" id="telefonoCliente" class="form-control" placeholder="Buscar por teléfono" autocomplete="off">
                                        <button type="button" class="btn btn-primary" id="btnBuscarCliente">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div id="listaClientes" class="list-group position-absolute" style="left: 0; right: 0; z-index: 1050; display: none;"></div>
                                </div>
                                <input type="hidden" name="cliente_id" id="cliente_id">
                                <small id="clienteInfo" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs de categorías y platos -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active" id="platos-tab-btn" data-bs-toggle="tab" data-bs-target="#platos-tab" role="tab">
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
                            <div class="tab-pane fade show active" id="platos-tab" role="tabpanel">
                                <?php if (!empty($platos)): ?>
                                <!-- Filtros -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fas fa-filter"></i> Filtrar por categoría:</label>
                                        <select id="filtroCategoria" class="form-select">
                                            <option value="">Todas las categorías</option>
                                            <?php foreach ($categorias as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><i class="fas fa-search"></i> Buscar plato:</label>
                                        <input type="text" id="buscadorPlato" class="form-control" placeholder="Escribe el nombre del plato...">
                                    </div>
                                </div>
                                <div class="row g-3" id="platos-container">
                                    <?php foreach ($platos as $prod): ?>
                                        <div class="col-md-3 plato-item" data-categoria="<?php echo $prod['categoria_id']; ?>" data-nombre="<?php echo strtolower($prod['nombre']); ?>">
                                            <div class="card plato-card" onclick="agregarAlCarrito('plato', <?php echo $prod['id']; ?>, '<?php echo addslashes($prod['nombre']); ?>', <?php echo $prod['precio']; ?>)">
                                                <?php if ($prod['imagen_url']): ?>
                                                    <img src="<?php echo BASE_URL . $prod['imagen_url']; ?>" class="card-img-top plato-imagen" alt="<?php echo $prod['nombre']; ?>">
                                                <?php else: ?>
                                                    <div class="bg-secondary text-white plato-imagen d-flex align-items-center justify-content-center">
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
                                    <p>No hay platos disponibles</p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Combos -->
                            <div class="tab-pane fade" id="combos-tab" role="tabpanel">
                                <?php if (!empty($combos)): ?>
                                <!-- Buscador de combos -->
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-search"></i> Buscar combo:</label>
                                    <input type="text" id="buscadorCombo" class="form-control" placeholder="Escribe el nombre del combo..." style="max-width: 400px;">
                                </div>
                                <div class="row g-3" id="combos-container">
                                    <?php foreach ($combos as $combo): ?>
                                        <div class="col-md-3 combo-item" data-nombre="<?php echo strtolower($combo['nombre']); ?>">
                                            <div class="card plato-card" onclick="agregarAlCarrito('combo', <?php echo $combo['id']; ?>, '<?php echo addslashes($combo['nombre']); ?>', <?php echo $combo['precio']; ?>)">
                                                <?php if ($combo['imagen_url']): ?>
                                                    <img src="<?php echo BASE_URL . $combo['imagen_url']; ?>" class="card-img-top plato-imagen" alt="<?php echo $combo['nombre']; ?>">
                                                <?php else: ?>
                                                    <div class="bg-info text-white plato-imagen d-flex align-items-center justify-content-center">
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
        
        // Actualizar labels según tipo de pedido
        if (tipo === "para_llevar") {
            $("#labelBuscarCliente").text("Cliente (nombre)");
            $("#telefonoCliente").attr("placeholder", "Buscar por nombre");
        } else {
            $("#labelBuscarCliente").text("Cliente (teléfono)");
            $("#telefonoCliente").attr("placeholder", "Buscar por teléfono");
        }
        
        // Limpiar búsqueda anterior
        $("#telefonoCliente").val("");
        $("#cliente_id").val("");
        $("#clienteInfo").text("");
        
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

// Autocompletado de clientes mientras se escribe
let timeoutBusqueda;
$("#telefonoCliente").on("input", function() {
    clearTimeout(timeoutBusqueda);
    const valorBusqueda = $(this).val().trim();
    const tipoPedido = $("#tipoPedido").val();
    
    if (valorBusqueda.length < 2) {
        $("#listaClientes").hide().empty();
        return;
    }
    
    timeoutBusqueda = setTimeout(function() {
        const parametro = (tipoPedido === "para_llevar") ? "nombre" : "telefono";
        const url = "index.php?action=pedidos_buscarClientesAutocomplete&" + parametro + "=" + encodeURIComponent(valorBusqueda);
        
        $.get(url, function(response) {
            if (response.success && response.data.length > 0) {
                console.log("Clientes encontrados:", response.data.length);
                let html = "";
                response.data.forEach(function(cliente) {
                    const displayText = tipoPedido === "para_llevar" 
                        ? cliente.nombre + (cliente.telefono ? " - " + cliente.telefono : "")
                        : cliente.telefono + " - " + cliente.nombre;
                    
                    html += `<a href="#" class="list-group-item list-group-item-action cliente-item py-2" 
                             data-id="${cliente.id}" 
                             data-nombre="${cliente.nombre}" 
                             data-telefono="${cliente.telefono || ""}">
                                <i class="fas fa-user me-2 text-primary"></i><span>${displayText}</span>
                             </a>`;
                });
                $("#listaClientes").html(html);
                
                // Determinar si abrir hacia arriba o abajo según espacio disponible
                const inputOffset = $("#telefonoCliente").offset();
                const inputHeight = $("#telefonoCliente").outerHeight();
                const listaHeight = Math.min(response.data.length * 60, 300); // Estimado
                const windowHeight = $(window).height();
                const spaceBelow = windowHeight - (inputOffset.top + inputHeight);
                const spaceAbove = inputOffset.top;
                
                if (spaceBelow < listaHeight && spaceAbove > spaceBelow) {
                    // Abrir hacia arriba
                    $("#listaClientes").css({
                        "top": "auto",
                        "bottom": "100%",
                        "margin-bottom": "2px"
                    });
                } else {
                    // Abrir hacia abajo (por defecto)
                    $("#listaClientes").css({
                        "top": "100%",
                        "bottom": "auto",
                        "margin-top": "2px"
                    });
                }
                
                $("#listaClientes").show();
                console.log("Lista mostrada con", response.data.length, "elementos");
            } else {
                console.log("No se encontraron clientes");
                $("#listaClientes").hide().empty();
            }
        }, "json").fail(function(xhr, status, error) {
            console.error("Error en búsqueda:", error, xhr.responseText);
            $("#listaClientes").hide().empty();
        });
    }, 300);
});

// Seleccionar cliente de la lista
$(document).on("click", ".cliente-item", function(e) {
    e.preventDefault();
    const id = $(this).data("id");
    const nombre = $(this).data("nombre");
    const telefono = $(this).data("telefono");
    
    $("#cliente_id").val(id);
    $("#telefonoCliente").val(nombre);
    const infoCliente = nombre + (telefono ? " - " + telefono : "");
    $("#clienteInfo").html("<strong>" + infoCliente + "</strong>");
    $("#listaClientes").hide().empty();
});

// Ocultar lista al hacer clic fuera
$(document).on("click", function(e) {
    if (!$(e.target).closest("#clienteSelect").length) {
        $("#listaClientes").hide();
    }
});

// Prevenir submit al presionar Enter en campo de búsqueda
$("#telefonoCliente").on("keypress", function(e) {
    if (e.which === 13) {
        e.preventDefault();
        const primeraOpcion = $(".cliente-item").first();
        if (primeraOpcion.length > 0) {
            primeraOpcion.click();
        } else {
            $("#btnBuscarCliente").click();
        }
        return false;
    }
});

// Navegación con teclado en la lista
$("#telefonoCliente").on("keydown", function(e) {
    const items = $(".cliente-item");
    const selected = $(".cliente-item.active");
    
    if (e.which === 40) { // Flecha abajo
        e.preventDefault();
        if (selected.length === 0) {
            items.first().addClass("active");
        } else {
            selected.removeClass("active").next(".cliente-item").addClass("active");
        }
    } else if (e.which === 38) { // Flecha arriba
        e.preventDefault();
        if (selected.length > 0) {
            selected.removeClass("active").prev(".cliente-item").addClass("active");
        }
    }
});

// Buscar cliente (mantener funcionalidad del botón)
$("#btnBuscarCliente").on("click", function() {
    const valorBusqueda = $("#telefonoCliente").val();
    const tipoPedido = $("#tipoPedido").val();
    
    if (!valorBusqueda) {
        const campo = (tipoPedido === "para_llevar") ? "nombre" : "teléfono";
        Swal.fire("Error", "Ingrese un " + campo, "error");
        return;
    }
    
    // Determinar parámetro según tipo de pedido
    const parametro = (tipoPedido === "para_llevar") ? "nombre" : "telefono";
    const url = "index.php?action=pedidos_buscarCliente&" + parametro + "=" + encodeURIComponent(valorBusqueda);
    
    $.get(url, function(response) {
        if (response.success) {
            $("#cliente_id").val(response.data.id);
            const infoCliente = response.data.nombre + (response.data.telefono ? " - " + response.data.telefono : "");
            $("#clienteInfo").html("<strong>" + infoCliente + "</strong>");
            Swal.fire("Encontrado", "Cliente: " + response.data.nombre, "success");
        } else {
            Swal.fire("No encontrado", "Cliente no existe. Puede crear uno nuevo.", "info");
            $("#cliente_id").val("");
            $("#clienteInfo").text("");
        }
    }, "json").fail(function(xhr, status, error) {
        console.error("Error en búsqueda:", error);
        Swal.fire("Error", "Error al buscar cliente. Por favor intente nuevamente.", "error");
    });
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
    filtrarPlatos();
});

// Buscador de platos
$("#buscadorPlato").on("keyup", function() {
    filtrarPlatos();
});

function filtrarPlatos() {
    const categoriaId = $("#filtroCategoria").val();
    const textoBusqueda = $("#buscadorPlato").val().toLowerCase();
    let contadorVisible = 0;
    
    $(".plato-item").each(function() {
        const categoria = $(this).data("categoria");
        const nombre = $(this).data("nombre");
        let mostrar = true;
        
        // Filtrar por categoría
        if (categoriaId && categoria != categoriaId) {
            mostrar = false;
        }
        
        // Filtrar por búsqueda
        if (textoBusqueda && nombre.indexOf(textoBusqueda) === -1) {
            mostrar = false;
        }
        
        if (mostrar) {
            $(this).show();
            contadorVisible++;
        } else {
            $(this).hide();
        }
    });
    
    // Mostrar mensaje si no hay resultados
    if (contadorVisible === 0) {
        if ($("#mensajeNoPlatos").length === 0) {
            $("#platos-container").append("<div id=\"mensajeNoPlatos\" class=\"col-12 text-center text-muted py-4\"><i class=\"fas fa-search fa-2x mb-2\"></i><p>No se encontraron platos</p></div>");
        }
    } else {
        $("#mensajeNoPlatos").remove();
    }
}

// Buscador de combos
$("#buscadorCombo").on("keyup", function() {
    const textoBusqueda = $(this).val().toLowerCase();
    let contadorVisible = 0;
    
    $(".combo-item").each(function() {
        const nombre = $(this).data("nombre");
        
        if (!textoBusqueda || nombre.indexOf(textoBusqueda) !== -1) {
            $(this).show();
            contadorVisible++;
        } else {
            $(this).hide();
        }
    });
    
    // Mostrar mensaje si no hay resultados
    if (contadorVisible === 0) {
        if ($("#mensajeNoCombos").length === 0) {
            $("#combos-container").append("<div id=\"mensajeNoCombos\" class=\"col-12 text-center text-muted py-4\"><i class=\"fas fa-search fa-2x mb-2\"></i><p>No se encontraron combos</p></div>");
        }
    } else {
        $("#mensajeNoCombos").remove();
    }
});

// Validar antes de enviar
$("#formPedido").on("submit", function(e) {
    if (carrito.length === 0) {
        e.preventDefault();
        Swal.fire("Error", "Agregue al menos un plato al pedido", "error");
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
