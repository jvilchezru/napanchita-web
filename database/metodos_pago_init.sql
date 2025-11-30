-- ============================================
-- SCRIPT DE INICIALIZACIÓN MÉTODOS DE PAGO
-- ============================================

USE napanchita_db;

-- Crear tabla si no existe
CREATE TABLE IF NOT EXISTS metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_metodos_pago_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Métodos de pago disponibles';

-- Insertar métodos de pago por defecto
INSERT INTO metodos_pago (nombre, descripcion, activo) VALUES
('Efectivo', 'Pago en efectivo', 1),
('Tarjeta de Crédito', 'Pago con tarjeta de crédito', 1),
('Tarjeta de Débito', 'Pago con tarjeta de débito', 1),
('Yape', 'Pago mediante aplicación Yape', 1),
('Plin', 'Pago mediante aplicación Plin', 1),
('Transferencia Bancaria', 'Transferencia bancaria directa', 1)
ON DUPLICATE KEY UPDATE nombre=nombre;

-- Verificar datos insertados
SELECT * FROM metodos_pago;
