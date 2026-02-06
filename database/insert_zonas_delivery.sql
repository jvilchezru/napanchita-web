-- =============================================
-- INSERTAR ZONAS DE DELIVERY
-- Sistema Napanchita
-- =============================================

USE napanchita_db;

-- Limpiar zonas existentes (opcional)
-- DELETE FROM zonas_delivery;

-- Insertar zonas de delivery
INSERT INTO `zonas_delivery` (`nombre`, `descripcion`, `costo_envio`, `tiempo_estimado`, `activo`, `orden`) 
VALUES 
('Centro de Sechura', 'Zona céntrica de la ciudad', 5.00, 20, 1, 1),
('Parachique', 'Playa Parachique y alrededores', 8.00, 35, 1, 2),
('Vice', 'Distrito de Vice', 10.00, 40, 1, 3),
('Bernal', 'Distrito de Bernal', 12.00, 45, 1, 4),
('Cristo Nos Valga', 'Puerto Cristo Nos Valga', 15.00, 50, 1, 5);

-- Verificar que se insertaron correctamente
SELECT * FROM zonas_delivery;

SELECT 'Zonas de delivery insertadas correctamente' AS mensaje;
