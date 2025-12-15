<?php
$pageTitle = 'Platos Más Vendidos';
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

        .progress {
            border: 1px solid #ddd !important;
        }
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chart-bar me-2"></i> Platos Más Vendidos</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Platos Más Vendidos</li>
            </ol>
        </nav>
    </div>
    <?php if (!empty($platosMasVendidos)): ?>
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
                <input type="hidden" name="action" value="reportes_platos">

                <div class="col-md-5">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control"
                        value="<?php echo $_GET['fecha_desde'] ?? date('Y-m-01'); ?>">
                </div>

                <div class="col-md-5">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control"
                        value="<?php echo $_GET['fecha_hasta'] ?? date('Y-m-t'); ?>">
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen -->
    <?php if (!empty($platosMasVendidos)): ?>
        <?php
        $totalVendido = array_sum(array_column($platosMasVendidos, 'cantidad_vendida'));
        $totalIngresos = array_sum(array_column($platosMasVendidos, 'total_ingresos'));
        ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total de Platos Diferentes</h6>
                        <h2><?php echo count($platosMasVendidos); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Total Unidades Vendidas</h6>
                        <h2><?php echo $totalVendido; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Ingresos Totales</h6>
                        <h2>S/ <?php echo number_format($totalIngresos, 2); ?></h2>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Top 20 Platos -->
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-trophy"></i> Top 20 Platos Más Vendidos</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($platosMasVendidos)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Plato</th>
                                <th>Categoría</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Cantidad Vendida</th>
                                <th class="text-end">Total Ingresos</th>
                                <th class="text-center">% del Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ranking = 1;
                            $totalGeneral = array_sum(array_column($platosMasVendidos, 'total_ingresos'));
                            foreach ($platosMasVendidos as $plato):
                                $porcentaje = $totalGeneral > 0 ? ($plato['total_ingresos'] / $totalGeneral * 100) : 0;
                                $badgeClass = $ranking <= 3 ? 'bg-warning' : ($ranking <= 10 ? 'bg-info' : 'bg-secondary');
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badgeClass; ?> rounded-circle" style="width: 35px; height: 35px; line-height: 35px; font-size: 16px;">
                                            <?php echo $ranking; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($plato['nombre']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($plato['categoria_nombre']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        S/ <?php echo number_format($plato['precio'], 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary" style="font-size: 14px;">
                                            <?php echo $plato['cantidad_vendida']; ?> unidades
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">S/ <?php echo number_format($plato['total_ingresos'], 2); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: <?php echo $porcentaje; ?>%;"
                                                aria-valuenow="<?php echo $porcentaje; ?>"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <?php echo number_format($porcentaje, 1); ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                $ranking++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico de barras -->
                <div class="mt-4">
                    <canvas id="chartPlatos" style="max-height: 400px;"></canvas>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay datos de ventas de platos para el período seleccionado.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($platosMasVendidos)): ?>
            const ctxPlatos = document.getElementById('chartPlatos').getContext('2d');
            new Chart(ctxPlatos, {
                type: 'bar',
                data: {
                    labels: [<?php echo implode(',', array_map(function ($p) {
                                    return '"' . addslashes($p['nombre']) . '"';
                                }, array_slice($platosMasVendidos, 0, 10))); ?>],
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: [<?php echo implode(',', array_column(array_slice($platosMasVendidos, 0, 10), 'cantidad_vendida')); ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 10 Platos Más Vendidos'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>