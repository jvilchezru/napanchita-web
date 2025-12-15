<?php
$pageTitle = 'Clientes Frecuentes';
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

        .badge {
            border: 1px solid #333 !important;
        }
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-friends me-2"></i> Clientes Frecuentes</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Clientes Frecuentes</li>
            </ol>
        </nav>
    </div>
    <?php if (!empty($clientesFrecuentes)): ?>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Imprimir / Guardar PDF
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="container-fluid">
    <!-- Resumen -->
    <?php if (!empty($clientesFrecuentes)): ?>
        <?php
        $totalPedidos = array_sum(array_column($clientesFrecuentes, 'total_pedidos'));
        $totalGastado = array_sum(array_column($clientesFrecuentes, 'total_gastado'));
        $promedioGasto = count($clientesFrecuentes) > 0 ? $totalGastado / count($clientesFrecuentes) : 0;
        ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total Clientes Top</h6>
                        <h2><?php echo count($clientesFrecuentes); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Total Pedidos</h6>
                        <h2><?php echo $totalPedidos; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Gasto Total</h6>
                        <h2>S/ <?php echo number_format($totalGastado, 2); ?></h2>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Top 20 Clientes -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-star"></i> Top 20 Clientes Frecuentes</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($clientesFrecuentes)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th class="text-center">Total Pedidos</th>
                                <th class="text-end">Total Gastado</th>
                                <th class="text-end">Gasto Promedio</th>
                                <th class="text-center">Nivel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ranking = 1;
                            foreach ($clientesFrecuentes as $cliente):
                                $promedioPorPedido = $cliente['total_pedidos'] > 0 ? $cliente['total_gastado'] / $cliente['total_pedidos'] : 0;

                                // Determinar nivel del cliente
                                if ($cliente['total_pedidos'] >= 20) {
                                    $nivel = 'VIP';
                                    $nivelClass = 'bg-warning text-dark';
                                    $icon = 'fa-crown';
                                } elseif ($cliente['total_pedidos'] >= 10) {
                                    $nivel = 'Oro';
                                    $nivelClass = 'bg-success';
                                    $icon = 'fa-medal';
                                } elseif ($cliente['total_pedidos'] >= 5) {
                                    $nivel = 'Plata';
                                    $nivelClass = 'bg-secondary';
                                    $icon = 'fa-award';
                                } else {
                                    $nivel = 'Bronce';
                                    $nivelClass = 'bg-info';
                                    $icon = 'fa-certificate';
                                }

                                $badgeClass = $ranking <= 3 ? 'bg-warning' : ($ranking <= 10 ? 'bg-info' : 'bg-secondary');
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badgeClass; ?> rounded-circle" style="width: 35px; height: 35px; line-height: 35px; font-size: 16px;">
                                            <?php echo $ranking; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone text-muted"></i>
                                        <?php echo htmlspecialchars($cliente['telefono'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary" style="font-size: 14px;">
                                            <?php echo $cliente['total_pedidos']; ?> pedidos
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">S/ <?php echo number_format($cliente['total_gastado'], 2); ?></strong>
                                    </td>
                                    <td class="text-end text-muted">
                                        S/ <?php echo number_format($promedioPorPedido, 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $nivelClass; ?>" style="font-size: 13px;">
                                            <i class="fas <?php echo $icon; ?>"></i> <?php echo $nivel; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php
                                $ranking++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico -->
                <div class="mt-4">
                    <canvas id="chartClientes" style="max-height: 400px;"></canvas>
                </div>

                <!-- Leyenda de niveles -->
                <div class="alert alert-info mt-4">
                    <h6><i class="fas fa-info-circle"></i> Niveles de Cliente</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <span class="badge bg-warning text-dark"><i class="fas fa-crown"></i> VIP</span>
                            <small>20+ pedidos</small>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-success"><i class="fas fa-medal"></i> Oro</span>
                            <small>10-19 pedidos</small>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-secondary"><i class="fas fa-award"></i> Plata</span>
                            <small>5-9 pedidos</small>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-info"><i class="fas fa-certificate"></i> Bronce</span>
                            <small>1-4 pedidos</small>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay datos de clientes disponibles.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($clientesFrecuentes)): ?>
            const ctxClientes = document.getElementById('chartClientes').getContext('2d');
            new Chart(ctxClientes, {
                type: 'bar',
                data: {
                    labels: [<?php echo implode(',', array_map(function ($c) {
                                    return '"' . addslashes($c['nombre']) . '"';
                                }, array_slice($clientesFrecuentes, 0, 10))); ?>],
                    datasets: [{
                            label: 'Total Pedidos',
                            data: [<?php echo implode(',', array_column(array_slice($clientesFrecuentes, 0, 10), 'total_pedidos')); ?>],
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Gastado (S/)',
                            data: [<?php echo implode(',', array_column(array_slice($clientesFrecuentes, 0, 10), 'total_gastado')); ?>],
                            backgroundColor: 'rgba(75, 192, 192, 0.7)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Top 10 Clientes Frecuentes'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Pedidos'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Gastado (S/)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        <?php endif; ?>
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>