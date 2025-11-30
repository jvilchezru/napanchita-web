<?php
$pageTitle = 'Dashboard Mesero';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-concierge-bell me-2"></i> Dashboard Mesero</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Accesos Rápidos -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_crear" class="text-decoration-none">
            <div class="stat-card blue">
                <i class="fas fa-plus-circle"></i>
                <p>Nuevo Pedido</p>
                <h3><i class="fas fa-shopping-cart"></i></h3>
                <small>Crear pedido rápido</small>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="<?php echo BASE_URL; ?>index.php?action=reservas_crear" class="text-decoration-none">
            <div class="stat-card green">
                <i class="fas fa-calendar-plus"></i>
                <p>Nueva Reserva</p>
                <h3><i class="fas fa-calendar-check"></i></h3>
                <small>Reservar mesa</small>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <div class="stat-card orange">
            <i class="fas fa-table"></i>
            <p>Mesas Ocupadas</p>
            <h3><?php echo count(array_filter($mesas, fn($m) => $m['estado'] == 'ocupada')); ?></h3>
            <small>En servicio</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card red">
            <i class="fas fa-clock"></i>
            <p>Mis Pedidos</p>
            <h3><?php echo count($pedidos_activos); ?></h3>
            <small>En proceso</small>
        </div>
    </div>
</div>

<!-- Mesas del Restaurante -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chair me-2"></i> Estado de Mesas</h5>
                <div>
                    <span class="badge bg-success me-2"><i class="fas fa-circle"></i> Disponible</span>
                    <span class="badge bg-danger me-2"><i class="fas fa-circle"></i> Ocupada</span>
                    <span class="badge bg-light text-dark"><i class="fas fa-circle"></i> Reservada</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="mesasGrid">
                    <?php foreach ($mesas as $mesa): 
                        $colorClass = 'success';
                        $textColor = 'text-success';
                        $estadoTexto = 'Disponible';
                        
                        switch($mesa['estado']) {
                            case 'ocupada':
                                $colorClass = 'danger';
                                $textColor = 'text-danger';
                                $estadoTexto = 'Ocupada';
                                break;
                            case 'reservada':
                                $colorClass = 'warning';
                                $textColor = 'text-warning';
                                $estadoTexto = 'Reservada';
                                break;
                        }
                    ?>
                        <div class="col-md-2 col-sm-4 mb-3">
                            <div class="card text-center mesa-card" data-mesa="<?php echo $mesa['id']; ?>" data-estado="<?php echo $mesa['estado']; ?>">
                                <div class="card-body">
                                    <i class="fas fa-table fa-3x <?php echo $textColor; ?> mb-2"></i>
                                    <h5>Mesa <?php echo $mesa['numero']; ?></h5>
                                    <p class="mb-1"><small><?php echo $mesa['capacidad']; ?> personas</small></p>
                                    <span class="badge bg-<?php echo $colorClass; ?>"><?php echo $estadoTexto; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Activos -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i> Mis Pedidos Activos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaPedidosActivos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mesa/Cliente</th>
                                <th>Tipo</th>
                                <th>Hora</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidos_activos)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No hay pedidos activos</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pedidos_activos as $pedido): 
                                    $estadoClass = 'secondary';
                                    switch($pedido['estado']) {
                                        case 'en_preparacion': $estadoClass = 'warning'; break;
                                        case 'listo': $estadoClass = 'success'; break;
                                    }
                                ?>
                                    <tr>
                                        <td>#<?php echo $pedido['id']; ?></td>
                                        <td>
                                            <?php if ($pedido['tipo'] == 'mesa'): ?>
                                                <i class="fas fa-table"></i> Mesa <?php echo $pedido['mesa_numero']; ?>
                                            <?php else: ?>
                                                <i class="fas fa-user"></i> <?php echo $pedido['cliente_nombre'] ?? 'Cliente'; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $tipoTexto = [
                                                'mesa' => 'Mesa',
                                                'para_llevar' => 'Para Llevar',
                                                'delivery' => 'Delivery'
                                            ];
                                            echo $tipoTexto[$pedido['tipo']] ?? $pedido['tipo'];
                                            ?>
                                        </td>
                                        <td><?php echo date('H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                        <td><?php echo $pedido['total_items']; ?></td>
                                        <td>S/ <?php echo number_format($pedido['total'], 2); ?></td>
                                        <td><span class="badge bg-<?php echo $estadoClass; ?>"><?php echo ucfirst($pedido['estado']); ?></span></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_ver&id=<?php echo $pedido['id']; ?>&t=<?php echo time(); ?>" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reservas Pendientes -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Reservas del Día</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Mesa</th>
                                <th>Hora</th>
                                <th>Personas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaReservas">
                            <?php if (empty($reservas_hoy)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p>No hay reservas para hoy</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservas_hoy as $reserva): 
                                    $estadoClass = 'secondary';
                                    $estadoTexto = ucfirst($reserva['estado']);
                                    switch($reserva['estado']) {
                                        case 'pendiente': 
                                            $estadoClass = 'warning'; 
                                            $estadoTexto = 'Pendiente';
                                            break;
                                        case 'confirmada': 
                                            $estadoClass = 'info'; 
                                            $estadoTexto = 'Confirmada';
                                            break;
                                        case 'completada': 
                                            $estadoClass = 'success'; 
                                            $estadoTexto = 'Completada';
                                            break;
                                        case 'cancelada': 
                                            $estadoClass = 'danger'; 
                                            $estadoTexto = 'Cancelada';
                                            break;
                                        case 'no_show': 
                                            $estadoClass = 'dark'; 
                                            $estadoTexto = 'No Show';
                                            break;
                                    }
                                ?>
                                    <tr>
                                        <td>#<?php echo $reserva['id']; ?></td>
                                        <td><?php echo htmlspecialchars($reserva['cliente_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['cliente_telefono'] ?? 'N/A'); ?></td>
                                        <td>Mesa <?php echo $reserva['mesa_numero']; ?></td>
                                        <td><?php echo date('H:i', strtotime($reserva['hora'])); ?></td>
                                        <td><?php echo $reserva['personas']; ?></td>
                                        <td><span class="badge bg-<?php echo $estadoClass; ?>"><?php echo $estadoTexto; ?></span></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>index.php?action=reservas_editar&id=<?php echo $reserva['id']; ?>" 
                                               class="btn btn-sm btn-primary" title="Ver/Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Crear mapeo de mesas a pedidos activos para el script
$mesaPedidoMap = [];
foreach ($pedidos_activos as $pedido) {
    if ($pedido['tipo'] == 'mesa' && !empty($pedido['mesa_id'])) {
        $mesaPedidoMap[$pedido['mesa_id']] = $pedido['id'];
    }
}
$mesaPedidoMapJson = json_encode($mesaPedidoMap);

$extraScripts = '
<script>
    // Mapeo de mesas a pedidos activos
    const mesaPedidoMap = ' . $mesaPedidoMapJson . ';
    
    // Click en mesa para ver detalles o asignar pedido
    document.querySelectorAll(".mesa-card").forEach(card => {
        card.style.cursor = "pointer";
        card.addEventListener("click", function() {
            const mesaId = this.dataset.mesa;
            const estado = this.dataset.estado;
            
            if (estado === "disponible") {
                // Si está disponible, ir directo a crear pedido
                window.location.href = "' . BASE_URL . 'index.php?action=pedidos_crear&mesa_id=" + mesaId;
            } else if (estado === "ocupada") {
                // Si está ocupada, buscar el pedido activo y redirigir directamente
                const pedidoId = mesaPedidoMap[mesaId];
                if (pedidoId) {
                    window.location.href = "' . BASE_URL . 'index.php?action=pedidos_ver&id=" + pedidoId;
                } else {
                    Swal.fire({
                        title: "Mesa ocupada",
                        text: "No se encontró el pedido activo de esta mesa",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                }
            } else if (estado === "reservada") {
                Swal.fire({
                    title: "Mesa reservada",
                    text: "Esta mesa tiene una reserva activa",
                    icon: "warning",
                    confirmButtonText: "Ver Reserva",
                    showCancelButton: true,
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "' . BASE_URL . 'index.php?action=reservas";
                    }
                });
            }
        });
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>