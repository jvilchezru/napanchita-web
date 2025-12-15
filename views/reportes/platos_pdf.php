<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Platos Más Vendidos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            margin: 5px 0;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            border: 2px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }

        .summary-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #FF9800;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .rank {
            background-color: #FFB74D;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-btn:hover {
            background: #0b7dda;
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Imprimir / Guardar PDF
    </button>

    <div class="header">
        <h1>RESTAURANTE NA' PANCHITA</h1>
        <h2>Top 20 Platos Más Vendidos</h2>
        <p>Período: <?php echo date('d/m/Y', strtotime($fecha_desde)); ?> al <?php echo date('d/m/Y', strtotime($fecha_hasta)); ?></p>
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>

    <?php if (!empty($platosMasVendidos)): ?>
        <?php
        $totalPlatos = count($platosMasVendidos);
        $totalCantidad = array_sum(array_column($platosMasVendidos, 'cantidad_vendida'));
        $totalIngresos = array_sum(array_column($platosMasVendidos, 'total_ingresos'));
        ?>

        <div class="summary">
            <div class="summary-card">
                <h3>Platos Diferentes</h3>
                <div class="value"><?php echo $totalPlatos; ?></div>
            </div>
            <div class="summary-card">
                <h3>Unidades Vendidas</h3>
                <div class="value"><?php echo $totalCantidad; ?></div>
            </div>
            <div class="summary-card">
                <h3>Ingresos Totales</h3>
                <div class="value">S/ <?php echo number_format($totalIngresos, 2); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Plato</th>
                    <th>Categoría</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-center">Cant. Vendida</th>
                    <th class="text-right">Total Ingresos</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1;
                foreach ($platosMasVendidos as $plato): ?>
                    <tr>
                        <td class="text-center">
                            <span class="rank"><?php echo $rank++; ?></span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($plato['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($plato['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                        <td class="text-right">S/ <?php echo number_format($plato['precio'] ?? 0, 2); ?></td>
                        <td class="text-center"><?php echo $plato['cantidad_vendida']; ?></td>
                        <td class="text-right"><strong>S/ <?php echo number_format($plato['total_ingresos'], 2); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de platos vendidos para el período seleccionado.</p>
    <?php endif; ?>

    <div class="footer">
        <p>Este documento fue generado automáticamente por el sistema de gestión de Restaurante Na' Panchita</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                // window.print();
            }, 500);
        };
    </script>
</body>

</html>