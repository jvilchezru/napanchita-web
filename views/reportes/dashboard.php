<?php
$pageTitle = 'Dashboard - Reportes';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-chart-line me-2"></i> Dashboard de Reportes</h1>
</div>

<div class="container-fluid">
    <!-- Selector de fecha -->
    <div class="row mb-4">
        <div class="col-md-3">
            <form method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="action" value="reportes">
                <div class="input-group">
                    <input type="date" name="fecha" class="form-control" value="<?php echo $_GET['fecha'] ?? date('Y-m-d'); ?>">
                    <button type="submit" class="btn btn-primary">Ver</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas del Día</h5>
                    <h2>S/ <?php echo number_format($metricas['ventas']['total_ingresos'] ?? 0, 2); ?></h2>
                    <small><?php echo $metricas['ventas']['total_ventas'] ?? 0; ?> transacciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Pedidos</h5>
                    <h2><?php 
                        $totalPedidos = 0;
                        foreach ($metricas['pedidos'] as $p) {
                            $totalPedidos += $p['cantidad'];
                        }
                        echo $totalPedidos;
                    ?></h2>
                    <small>Total del día</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Mesas Ocupadas</h5>
                    <h2><?php echo $metricas['mesas_ocupadas'] ?? 0; ?></h2>
                    <small>En este momento</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Reservas Hoy</h5>
                    <h2><?php echo $metricas['reservas_hoy'] ?? 0; ?></h2>
                    <small>Pendientes/Confirmadas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos por estado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pedidos por Estado</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPedidos" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Enlaces Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="<?php echo BASE_URL; ?>index.php?action=reportes_ventas" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar"></i> Reporte de Ventas
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=reportes_platos" class="list-group-item list-group-item-action">
                            <i class="fas fa-utensils"></i> Platos Más Vendidos
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=cierre_caja" class="list-group-item list-group-item-action">
                            <i class="fas fa-cash-register"></i> Cierre de Caja
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?action=reservas" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar"></i> Gestión de Reservas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de pedidos
const ctxPedidos = document.getElementById('chartPedidos').getContext('2d');
const dataPedidos = <?php echo json_encode($metricas['pedidos']); ?>;

const labels = dataPedidos.map(p => p.estado);
const valores = dataPedidos.map(p => p.cantidad);

new Chart(ctxPedidos, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Cantidad de Pedidos',
            data: valores,
            backgroundColor: [
                'rgba(255, 206, 86, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 99, 132, 0.6)'
            ],
            borderColor: [
                'rgba(255, 206, 86, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
