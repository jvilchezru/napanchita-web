-- ============================================
-- TABLA: CONFIGURACIÓN DEL SISTEMA
-- ============================================

CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion VARCHAR(255),
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_configuracion_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Configuración general del sistema';

-- ============================================
-- DATOS INICIALES DE CONFIGURACIÓN
-- ============================================

INSERT INTO configuracion (clave, valor, descripcion) VALUES
-- Información General
('nombre_restaurante', 'Cevichería Ñapanchita', 'Nombre del restaurante'),
('ruc', '20123456789', 'RUC del establecimiento'),
('direccion', 'Av. Principal 123, Distrito, Ciudad', 'Dirección del local'),
('telefono', '987654321', 'Teléfono de contacto'),
('email', 'contacto@napanchita.com', 'Email de contacto'),
('logo', 'logo.png', 'Nombre del archivo del logo'),

-- Delivery
('costo_delivery', '5.00', 'Costo de envío a domicilio en soles'),
('monto_minimo_delivery', '20.00', 'Monto mínimo para delivery en soles'),
('tiempo_preparacion', '30', 'Tiempo estimado de preparación en minutos'),

-- Reservas
('tiempo_max_reserva', '2', 'Tiempo máximo de reserva en horas'),
('anticipacion_minima_reserva', '1', 'Anticipación mínima para reservar en horas'),

-- Impuestos
('igv', '18', 'Porcentaje de IGV'),
('aplicar_igv', '1', 'Aplicar IGV: 1=Sí, 0=No'),

-- Sistema
('modo_mantenimiento', '0', 'Modo mantenimiento: 1=Activado, 0=Desactivado'),
('zona_horaria', 'America/Lima', 'Zona horaria del sistema'),
('moneda', 'PEN', 'Código de moneda (PEN, USD, etc.)'),
('simbolo_moneda', 'S/', 'Símbolo de la moneda');
