<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
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

        .info-section {
            margin-bottom: 20px;
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
            background-color: #4CAF50;
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

        .total-row {
            background-color: #e8f5e9 !important;
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
        <h2>Reporte de Ventas por Día</h2>
        <p>Período: <?php echo date('d/m/Y', strtotime($fecha_desde)); ?> al <?php echo date('d/m/Y', strtotime($fecha_hasta)); ?></p>
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>

    <?php if (!empty($ventasPorPeriodo)): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-center">Cantidad de Ventas</th>
                    <th class="text-right">Total Vendido</th>
                    <th class="text-right">Promedio por Venta</th>
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
                        <td class="text-center"><?php echo $periodo['cantidad_ventas']; ?></td>
                        <td class="text-right">S/ <?php echo number_format($periodo['total_ventas'], 2); ?></td>
                        <td class="text-right">S/ <?php echo number_format($promedio, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-center"><strong><?php echo $totalVentas; ?></strong></td>
                    <td class="text-right"><strong>S/ <?php echo number_format($totalMonto, 2); ?></strong></td>
                    <td class="text-right"><strong>S/ <?php echo number_format($totalVentas > 0 ? $totalMonto / $totalVentas : 0, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de ventas para el período seleccionado.</p>
    <?php endif; ?>

    <div class="footer">
        <p>Este documento fue generado automáticamente por el sistema de gestión de Restaurante Na' Panchita</p>
    </div>

    <script>
        // Auto-abrir el diálogo de impresión al cargar la página
        window.onload = function() {
            // Pequeño delay para que el contenido se renderice completamente
            setTimeout(function() {
                // window.print();
            }, 500);
        };
    </script>
</body>

</html>