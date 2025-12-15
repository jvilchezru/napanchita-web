<?php
$pageTitle = 'Reporte de Ventas';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    @media print {

        .sidebar,
        .page-header nav,
        .card-header .btn,
        .no-print,
        button {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 20px !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .card-header {
            background: white !important;
            color: black !important;
            border-bottom: 2px solid #333 !important;
            padding: 10px !important;
        }

        .page-header h1 {
            font-size: 24px !important;
            margin-bottom: 20px !important;
        }

        canvas {
            display: none !important;
        }

        table {
            font-size: 11px !important;
        }
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chart-line me-2"></i> Reporte de Ventas</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Reporte de Ventas</li>
            </ol>
        </nav>
    </div>
    <?php if (!empty($ventasPorPeriodo)): ?>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Imprimir / Guardar PDF
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="container-fluid">
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Consulta</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="action" value="reportes_ventas">

                <div class="col-md-4">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control"
                        value="<?php echo $_GET['fecha_desde'] ?? date('Y-m-01'); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control"
                        value="<?php echo $_GET['fecha_hasta'] ?? date('Y-m-t'); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ventas por Período -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Ventas por Día</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($ventasPorPeriodo)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th class="text-center">Cantidad de Ventas</th>
                                <th class="text-end">Total Vendido</th>
                                <th class="text-end">Promedio por Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalVentas = 0;
                            $totalMonto = 0;
                            foreach ($ventasPorPeriodo as $periodo):
                                $totalVentas += $periodo['cantidad_ventas'];
                                $totalMonto += $periodo['total_ventas'];
                                $promedio = $periodo['cantidad_ventas'] > 0 ? $periodo['total_ventas'] / $periodo['cantidad_ventas'] : 0;
                            ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($periodo['periodo'])); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?php echo $periodo['cantidad_ventas']; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <strong>S/ <?php echo number_format($periodo['total_ventas'], 2); ?></strong>
                                    </td>
                                    <td class="text-end text-muted">
                                        S/ <?php echo number_format($promedio, 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-info">
                                <td><strong>TOTAL</strong></td>
                                <td class="text-center">
                                    <strong><?php echo $totalVentas; ?></strong>
                                </td>
                                <td class="text-end">
                                    <strong>S/ <?php echo number_format($totalMonto, 2); ?></strong>
                                </td>
                                <td class="text-end">
                                    <strong>S/ <?php echo number_format($totalVentas > 0 ? $totalMonto / $totalVentas : 0, 2); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico -->
                <div class="mt-4">
                    <canvas id="chartVentas" style="max-height: 300px;"></canvas>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay datos de ventas para el período seleccionado.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($ventasPorPeriodo)): ?>
            // Gráfico de Ventas por Día
            const ctxVentas = document.getElementById('chartVentas').getContext('2d');
            new Chart(ctxVentas, {
                type: 'line',
                data: {
                    labels: [<?php echo implode(',', array_map(function ($p) {
                                    return '"' . date('d/m', strtotime($p['periodo'])) . '"';
                                }, $ventasPorPeriodo)); ?>],
                    datasets: [{
                        label: 'Total Ventas (S/)',
                        data: [<?php echo implode(',', array_column($ventasPorPeriodo, 'total_ventas')); ?>],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>