<?php
$pageTitle = 'Vista de Cocina';
include __DIR__ . '/../layouts/header.php';
?>

<style>
.pedido-card {
    border-left: 5px solid;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.pedido-pendiente { 
    border-left-color: #ffc107; 
    background-color: #fff9e6;
}
.pedido-preparacion { 
    border-left-color: #0dcaf0; 
    background-color: #e8f9fd;
}
.pedido-card .card-header {
    background-color: rgba(0,0,0,0.03);
    border-bottom: 1px solid rgba(0,0,0,0.125);
}
.tiempo-alerta { 
    color: #dc3545; 
    font-weight: bold;
    background-color: #ffe0e0;
    padding: 3px 8px;
    border-radius: 4px;
}
.tiempo-normal {
    color: #198754;
    font-weight: 600;
}
.pedido-card ul li {
    padding: 5px 0;
    border-bottom: 1px dashed #e0e0e0;
    color: #212529;
}
.pedido-card ul li:last-child {
    border-bottom: none;
}
.pedido-card .badge {
    font-size: 0.9em;
    margin-right: 5px;
}
.info-pedido {
    color: #212529;
    font-weight: 600;
}
</style>

<div class="page-header">
    <h1><i class="fas fa-utensils me-2"></i> Vista de Cocina</h1>
    <button class="btn btn-secondary btn-sm" onclick="toggleRefresh()">
        <i class="fas fa-sync"></i> <span id="autoRefreshText">Auto-refresh: ON</span>
    </button>
</div>

<div class="container-fluid">
    <div class="row" id="tablero-cocina">
        <div class="col-md-6">
            <h4 class="text-warning"><i class="fas fa-clock"></i> Pendientes</h4>
            <div id="pedidos-pendientes"></div>
        </div>
        <div class="col-md-6">
            <h4 class="text-info"><i class="fas fa-fire"></i> En Preparación</h4>
            <div id="pedidos-preparacion"></div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
let autoRefresh = true;
let refreshInterval;

function cargarPedidos() {
    $.get("index.php?action=pedidos_obtenerPendientes", function(response) {
        if (response.success) {
            mostrarPedidos(response.data);
        }
    }, "json");
}

function mostrarPedidos(pedidos) {
    const pendientes = pedidos.filter(p => p.estado === "pendiente");
    const enPreparacion = pedidos.filter(p => p.estado === "en_preparacion");
    
    $("#pedidos-pendientes").html(generarTarjetas(pendientes, "pendiente"));
    $("#pedidos-preparacion").html(generarTarjetas(enPreparacion, "preparacion"));
}

function generarTarjetas(pedidos, clase) {
    if (pedidos.length === 0) {
        return \'<div class="alert alert-secondary"><i class="fas fa-check-circle"></i> Sin pedidos en esta sección</div>\';
    }
    
    let html = "";
    pedidos.forEach(pedido => {
        const tiempoClase = pedido.minutos_transcurridos > 15 ? "tiempo-alerta" : "tiempo-normal";
        const mesaCliente = pedido.tipo === "mesa" 
            ? \'<i class="fas fa-chair"></i> Mesa \' + pedido.mesa_numero 
            : \'<i class="fas fa-user"></i> \' + (pedido.cliente_nombre || "Cliente");
        
        let items = "";
        pedido.items.forEach(item => {
            items += \'<li><span class="badge bg-dark">\' + item.cantidad + \'x</span> <strong>\' + item.nombre + \'</strong></li>\';
        });
        
        const notasHtml = pedido.notas ? \'<div class="alert alert-warning py-2 mb-3"><i class="fas fa-comment"></i> <strong>Nota:</strong> \' + pedido.notas + \'</div>\' : "";
        
        const botonPendiente = pedido.estado === "pendiente" ? 
            \'<button class="btn btn-info btn-lg" onclick="cambiarEstado(\' + pedido.id + \', \\\'en_preparacion\\\')">\' +
                \'<i class="fas fa-fire"></i> Iniciar Preparación\' +
            \'</button>\' : "";
            
        const botonPreparacion = pedido.estado === "en_preparacion" ? 
            \'<button class="btn btn-success btn-lg" onclick="cambiarEstado(\' + pedido.id + \', \\\'listo\\\')">\' +
                \'<i class="fas fa-check"></i> Marcar Listo\' +
            \'</button>\' : "";
        
        html += \'<div class="card pedido-card pedido-\' + clase + \'">\' +
            \'<div class="card-header d-flex justify-content-between align-items-center">\' +
                \'<h5 class="mb-0 info-pedido">Pedido #\' + pedido.id + \'</h5>\' +
                \'<span class="\' + tiempoClase + \'">⏱️ \' + pedido.minutos_transcurridos + \' min</span>\' +
            \'</div>\' +
            \'<div class="card-body">\' +
                \'<p class="mb-3 info-pedido">\' + mesaCliente + \'</p>\' +
                \'<hr class="my-2">\' +
                \'<strong class="d-block mb-2" style="color: #495057;">Items del pedido:</strong>\' +
                \'<ul class="list-unstyled mb-3">\' + items + \'</ul>\' +
                notasHtml +
                \'<div class="btn-group w-100 mt-2">\' + botonPendiente + botonPreparacion + \'</div>\' +
            \'</div>\' +
        \'</div>\';
    });
    return html;
}

function cambiarEstado(id, estado) {
    $.post("index.php?action=pedidos_cambiarEstado", {id: id, estado: estado}, function(response) {
        if (response.success) {
            cargarPedidos();
        } else {
            alert("Error: " + response.message);
        }
    }, "json");
}

function toggleRefresh() {
    autoRefresh = !autoRefresh;
    $("#autoRefreshText").text("Auto-refresh: " + (autoRefresh ? "ON" : "OFF"));
    
    if (autoRefresh) {
        refreshInterval = setInterval(cargarPedidos, 5000);
    } else {
        clearInterval(refreshInterval);
    }
}

$(document).ready(function() {
    cargarPedidos();
    refreshInterval = setInterval(cargarPedidos, 5000);
});
</script>
';
include __DIR__ . '/../layouts/footer.php';
?>
