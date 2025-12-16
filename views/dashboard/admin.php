<?php
$pageTitle = 'Dashboard Administrador';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-chart-line me-2"></i> Dashboard Administrador</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<?php
// Obtener datos reales del sistema
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/Mesa.php';

$database = new Database();
$db = $database->getConnection();

$pedidoModel = new Pedido($db);
$clienteModel = new Cliente($db);
$mesaModel = new Mesa($db);

// Estadísticas de pedidos
$estadisticasPedidos = $pedidoModel->obtenerEstadisticas(date('Y-m-d'));
$pedidosHoy = $estadisticasPedidos['total_pedidos'] ?? 0;
$pedidosPendientes = $estadisticasPedidos['pendientes'] ?? 0;

// Clientes registrados esta semana
$fechaInicioSemana = date('Y-m-d', strtotime('monday this week'));
$clientesNuevos = $clienteModel->contarNuevos($fechaInicioSemana);

// Estado de mesas
$estadoMesas = $mesaModel->obtenerEstadisticas();
$mesasDisponibles = $estadoMesas['disponibles'] ?? 0;
$mesasOcupadas = $estadoMesas['ocupadas'] ?? 0;
$mesasReservadas = $estadoMesas['reservadas'] ?? 0;
?>

<!-- Tarjetas de Estadísticas -->
<div class="row">
    <div class="col-md-4">
        <div class="stat-card blue">
            <i class="fas fa-shopping-cart"></i>
            <p>Pedidos Hoy</p>
            <h3><?php echo $pedidosHoy; ?></h3>
            <small><?php echo date('d/m/Y'); ?></small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card orange">
            <i class="fas fa-users"></i>
            <p>Clientes Nuevos</p>
            <h3><?php echo $clientesNuevos; ?></h3>
            <small>Esta semana</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card red">
            <i class="fas fa-clock"></i>
            <p>Pedidos Pendientes</p>
            <h3><?php echo $pedidosPendientes; ?></h3>
            <small>Requieren atención</small>
        </div>
    </div>
</div>

<!-- Gráficos y Tablas -->
<div class="row mt-4">
    <?php
    // Obtener platos más vendidos
    $queryTopPlatos = "SELECT pi.nombre, COUNT(*) as cantidad 
                       FROM pedido_items pi 
                       INNER JOIN pedidos p ON pi.pedido_id = p.id 
                       WHERE p.fecha_pedido >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                       GROUP BY pi.nombre 
                       ORDER BY cantidad DESC 
                       LIMIT 5";
    $stmtTop = $db->query($queryTopPlatos);
    $platosTop = $stmtTop->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <!-- Platos Más Vendidos -->
    <?php if (!empty($platosTop)): ?>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-fire me-2"></i> Platos Más Vendidos (Últimos 7 Días)</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($platosTop as $index => $plato): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong class="text-primary me-2">#<?php echo $index + 1; ?></strong>
                                <?php echo htmlspecialchars($plato['nombre']); ?>
                            </span>
                            <span class="badge bg-primary rounded-pill fs-6"><?php echo $plato['cantidad']; ?> pedidos</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row mt-4">
    <?php
    // Obtener pedidos recientes
    $pedidosRecientes = $pedidoModel->listar(['limit' => 10]);
    ?>
    
    <!-- Pedidos Recientes -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i> Pedidos Recientes</h5>
                <a href="<?php echo BASE_URL; ?>index.php?action=pedidos" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-right me-1"></i> Ver Todos
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente/Mesa</th>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pedidosRecientes)): ?>
                                <?php foreach ($pedidosRecientes as $pedido): ?>
                                    <tr>
                                        <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                        <td>
                                            <?php 
                                            if ($pedido['tipo'] == 'mesa') {
                                                echo 'Mesa ' . ($pedido['mesa_numero'] ?? 'N/A');
                                            } else {
                                                echo htmlspecialchars($pedido['cliente_nombre'] ?? 'Cliente');
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $tipos = [
                                                'mesa' => '<span class="badge bg-info">Mesa</span>',
                                                'delivery' => '<span class="badge bg-warning">Delivery</span>',
                                                'para_llevar' => '<span class="badge bg-secondary">Para Llevar</span>'
                                            ];
                                            echo $tipos[$pedido['tipo']] ?? $pedido['tipo'];
                                            ?>
                                        </td>
                                        <td><strong>S/ <?php echo number_format($pedido['total'], 2); ?></strong></td>
                                        <td>
                                            <?php
                                            $estados = [
                                                'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
                                                'en_preparacion' => '<span class="badge bg-info">En Preparación</span>',
                                                'listo' => '<span class="badge bg-primary">Listo</span>',
                                                'entregado' => '<span class="badge bg-success">Entregado</span>',
                                                'finalizado' => '<span class="badge bg-dark">Finalizado</span>',
                                                'cancelado' => '<span class="badge bg-danger">Cancelado</span>'
                                            ];
                                            echo $estados[$pedido['estado']] ?? $pedido['estado'];
                                            ?>
                                        </td>
                                        <td><?php echo date('d/m H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_ver&id=<?php echo $pedido['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay pedidos registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de Mesas -->
    <div class="col-md-12 mt-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i> Estado de Mesas</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div style="max-width: 250px; margin: 0 auto;">
                            <canvas id="mesasChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-flex justify-content-between p-3 bg-light rounded border border-success">
                                    <span><i class="fas fa-circle text-success me-2"></i> Disponibles</span>
                                    <strong class="fs-4 text-success"><?php echo $mesasDisponibles; ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex justify-content-between p-3 bg-light rounded border border-danger">
                                    <span><i class="fas fa-circle text-danger me-2"></i> Ocupadas</span>
                                    <strong class="fs-4 text-danger"><?php echo $mesasOcupadas; ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex justify-content-between p-3 bg-light rounded border border-warning">
                                    <span><i class="fas fa-circle text-warning me-2"></i> Reservadas</span>
                                    <strong class="fs-4 text-warning"><?php echo $mesasReservadas; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$extraScripts = '
<script>
    // Gráfico de Mesas con datos reales
    const mesasCtx = document.getElementById("mesasChart").getContext("2d");
    const mesasChart = new Chart(mesasCtx, {
        type: "doughnut",
        data: {
            labels: ["Disponibles", "Ocupadas", "Reservadas"],
            datasets: [{
                data: [' . $mesasDisponibles . ', ' . $mesasOcupadas . ', ' . $mesasReservadas . '],
                backgroundColor: [
                    "rgb(40, 167, 69)",
                    "rgb(220, 53, 69)",
                    "rgb(255, 193, 7)"
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>