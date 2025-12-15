<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Clientes Frecuentes</title>
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
            background-color: #9C27B0;
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
            background-color: #BA68C8;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .badge-vip {
            background-color: #FFD700;
            color: #333;
        }

        .badge-gold {
            background-color: #FFA500;
        }

        .badge-silver {
            background-color: #C0C0C0;
            color: #333;
        }

        .badge-bronze {
            background-color: #CD7F32;
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
        <h2>Top 20 Clientes Frecuentes</h2>
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>

    <?php if (!empty($clientesFrecuentes)): ?>
        <?php
        $totalClientes = count($clientesFrecuentes);
        $totalPedidos = array_sum(array_column($clientesFrecuentes, 'total_pedidos'));
        $totalGastado = array_sum(array_column($clientesFrecuentes, 'total_gastado'));
        ?>

        <div class="summary">
            <div class="summary-card">
                <h3>Total Clientes Top</h3>
                <div class="value"><?php echo $totalClientes; ?></div>
            </div>
            <div class="summary-card">
                <h3>Total Pedidos</h3>
                <div class="value"><?php echo $totalPedidos; ?></div>
            </div>
            <div class="summary-card">
                <h3>Gasto Total</h3>
                <div class="value">S/ <?php echo number_format($totalGastado, 2); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Cliente</th>
                    <th>Contacto</th>
                    <th class="text-center">Total Pedidos</th>
                    <th class="text-right">Total Gastado</th>
                    <th class="text-right">Gasto Promedio</th>
                    <th class="text-center">Nivel</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                foreach ($clientesFrecuentes as $cliente):
                    $gastoProm = $cliente['total_pedidos'] > 0 ? $cliente['total_gastado'] / $cliente['total_pedidos'] : 0;

                    // Determinar nivel
                    if ($cliente['total_pedidos'] >= 20) {
                        $nivel = 'VIP';
                        $badgeClass = 'badge-vip';
                    } elseif ($cliente['total_pedidos'] >= 15) {
                        $nivel = 'ORO';
                        $badgeClass = 'badge-gold';
                    } elseif ($cliente['total_pedidos'] >= 10) {
                        $nivel = 'PLATA';
                        $badgeClass = 'badge-silver';
                    } else {
                        $nivel = 'BRONCE';
                        $badgeClass = 'badge-bronze';
                    }
                ?>
                    <tr>
                        <td class="text-center">
                            <span class="rank"><?php echo $rank++; ?></span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($cliente['nombre_completo']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($cliente['telefono'] ?? 'N/A'); ?><br>
                            <small><?php echo htmlspecialchars($cliente['email'] ?? 'N/A'); ?></small>
                        </td>
                        <td class="text-center"><?php echo $cliente['total_pedidos']; ?></td>
                        <td class="text-right"><strong>S/ <?php echo number_format($cliente['total_gastado'], 2); ?></strong></td>
                        <td class="text-right">S/ <?php echo number_format($gastoProm, 2); ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $nivel; ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay datos de clientes frecuentes disponibles.</p>
    <?php endif; ?>

    <div class="footer">
        <p>Este documento fue generado automáticamente por el sistema de gestión de Restaurante Na' Panchita</p>
        <p><strong>Niveles de Clientes:</strong> VIP (20+ pedidos) | ORO (15-19 pedidos) | PLATA (10-14 pedidos) | BRONCE (menos de 10 pedidos)</p>
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