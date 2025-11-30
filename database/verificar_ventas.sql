-- Verificar estructura de tabla ventas
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'napanchita_db' 
  AND TABLE_NAME = 'ventas'
ORDER BY ORDINAL_POSITION;

-- Si la tabla no existe, usar este script para crearla:
/*
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNIQUE NOT NULL,
    metodo_pago_id INT NOT NULL,
    monto_recibido DECIMAL(10, 2) NOT NULL,
    monto_cambio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    descuento_aplicado DECIMAL(10, 2) DEFAULT 0,
    codigo_descuento VARCHAR(50),
    usuario_id INT NOT NULL COMMENT 'Cajero que registró',
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ticket_generado BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE RESTRICT,
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    
    INDEX idx_ventas_pedido (pedido_id),
    INDEX idx_ventas_fecha (fecha_venta),
    INDEX idx_ventas_metodo_pago (metodo_pago_id),
    INDEX idx_ventas_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/
