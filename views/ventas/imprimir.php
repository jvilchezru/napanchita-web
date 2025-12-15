<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta #<?php echo $venta['id']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            max-width: 80mm;
            margin: 0 auto;
        }

        .comprobante {
            border: 2px solid #000;
            padding: 15px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .info-section {
            margin: 10px 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .info-label {
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            margin: 10px 0;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }

        .items-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .item-row {
            margin: 5px 0;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 2px;
        }

        .totales {
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .total-final {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 10px;
        }

        .footer p {
            margin: 3px 0;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .comprobante {
                border: none;
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
            font-family: Arial, sans-serif;
        }

        .print-btn:hover {
            background: #0b7dda;
        }
    </style>
</head>

<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Imprimir
    </button>

    <div class="comprobante">
        <!-- Header -->
        <div class="header">
            <h1>RESTAURANTE NA' PANCHITA</h1>
            <p>RUC: 20123456789</p>
            <p>Av. Principal 123 - Chiclayo</p>
            <p>Tel: (074) 123-456</p>
            <p style="margin-top: 8px; font-size: 13px; font-weight: bold;">
                COMPROBANTE DE VENTA
            </p>
            <p style="font-size: 14px; font-weight: bold;">
                #<?php echo str_pad($venta['id'], 6, '0', STR_PAD_LEFT); ?>
            </p>
        </div>

        <!-- Información de la Venta -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span><?php echo date('d/m/Y H:i:s', strtotime($venta['fecha_venta'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Cajero:</span>
                <span><?php echo htmlspecialchars($venta['usuario_nombre'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Pedido:</span>
                <span>#<?php echo $venta['pedido_id']; ?> (<?php echo ucfirst($venta['pedido_tipo'] ?? 'N/A'); ?>)</span>
            </div>
            <?php if (!empty($pedido['cliente_nombre'])): ?>
                <div class="info-row">
                    <span class="info-label">Cliente:</span>
                    <span><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($pedido['cliente_telefono'])): ?>
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span><?php echo htmlspecialchars($pedido['cliente_telefono']); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Items del Pedido -->
        <div class="items-table">
            <div class="items-header">
                <span>PRODUCTO</span>
                <span>TOTAL</span>
            </div>

            <?php if (!empty($pedido['items'])): ?>
                <?php foreach ($pedido['items'] as $item): ?>
                    <div class="item-row">
                        <div class="item-name"><?php echo htmlspecialchars($item['nombre']); ?></div>
                        <div class="item-details">
                            <span>
                                <?php echo $item['cantidad']; ?> x S/ <?php echo number_format($item['precio_unitario'], 2); ?>
                            </span>
                            <span>S/ <?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                        <?php if (!empty($item['notas'])): ?>
                            <div style="font-size: 10px; font-style: italic; margin-top: 2px;">
                                Nota: <?php echo htmlspecialchars($item['notas']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Totales -->
        <div class="totales">
            <?php
            $subtotal = $venta['total'] + $venta['descuento_aplicado'];
            ?>

            <div class="total-row">
                <span>Subtotal:</span>
                <span>S/ <?php echo number_format($subtotal, 2); ?></span>
            </div>

            <?php if ($venta['descuento_aplicado'] > 0): ?>
                <div class="total-row" style="color: #d32f2f;">
                    <span>
                        Descuento:
                        <?php if ($venta['codigo_descuento']): ?>
                            <small>(<?php echo htmlspecialchars($venta['codigo_descuento']); ?>)</small>
                        <?php endif; ?>
                    </span>
                    <span>- S/ <?php echo number_format($venta['descuento_aplicado'], 2); ?></span>
                </div>
            <?php endif; ?>

            <div class="total-row total-final">
                <span>TOTAL:</span>
                <span>S/ <?php echo number_format($venta['total'], 2); ?></span>
            </div>

            <div class="info-section" style="margin-top: 10px;">
                <div class="total-row">
                    <span>Método de Pago:</span>
                    <span style="font-weight: bold;"><?php echo htmlspecialchars($venta['metodo_pago_nombre'] ?? 'N/A'); ?></span>
                </div>
                <div class="total-row">
                    <span>Monto Recibido:</span>
                    <span>S/ <?php echo number_format($venta['monto_recibido'], 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Cambio:</span>
                    <span>S/ <?php echo number_format($venta['monto_cambio'], 2); ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>¡GRACIAS POR SU PREFERENCIA!</strong></p>
            <p>Esperamos verlo pronto</p>
            <p style="margin-top: 8px;">www.napanchita.com</p>
            <p>Facebook: @NaPanchitaRestaurant</p>
            <p style="margin-top: 8px; font-size: 9px;">
                Este documento no tiene validez tributaria
            </p>
        </div>
    </div>

    <script>
        // Auto-abrir el diálogo de impresión al cargar la página
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>