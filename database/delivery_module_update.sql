-- =============================================
-- MÓDULO DE DELIVERY - ACTUALIZACIÓN DE BD
-- Sistema Napanchita
-- Fecha: 2026-02-04
-- =============================================

USE napanchita_db;

-- =============================================
-- 1. EXTENDER TABLA CLIENTES PARA AUTENTICACIÓN
-- =============================================

-- Agregar columnas solo si no existen (MySQL 8.0+)
SET @dbname = DATABASE();
SET @tablename = 'clientes';

-- Password
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'password') > 0,
  'SELECT ''Column password already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN password VARCHAR(255) DEFAULT NULL COMMENT ''Hash del password para clientes con cuenta web'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- tiene_cuenta
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'tiene_cuenta') > 0,
  'SELECT ''Column tiene_cuenta already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN tiene_cuenta TINYINT(1) DEFAULT 0 COMMENT ''1=Cuenta web activa, 0=Solo cliente telefónico'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- token_verificacion
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'token_verificacion') > 0,
  'SELECT ''Column token_verificacion already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN token_verificacion VARCHAR(100) DEFAULT NULL COMMENT ''Token para verificar email'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- email_verificado
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'email_verificado') > 0,
  'SELECT ''Column email_verificado already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN email_verificado TINYINT(1) DEFAULT 0 COMMENT ''1=Email verificado, 0=Pendiente'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- ultimo_acceso
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'ultimo_acceso') > 0,
  'SELECT ''Column ultimo_acceso already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN ultimo_acceso TIMESTAMP NULL DEFAULT NULL COMMENT ''Última vez que inició sesión'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- token_recuperacion
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'token_recuperacion') > 0,
  'SELECT ''Column token_recuperacion already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN token_recuperacion VARCHAR(100) DEFAULT NULL COMMENT ''Token para recuperar contraseña'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- token_expira
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'token_expira') > 0,
  'SELECT ''Column token_expira already exists'' AS message;',
  'ALTER TABLE clientes ADD COLUMN token_expira TIMESTAMP NULL DEFAULT NULL COMMENT ''Expiración del token de recuperación'';'
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- =============================================
-- 2. ASEGURAR TABLA ZONAS_DELIVERY
-- =============================================
CREATE TABLE IF NOT EXISTS `zonas_delivery` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `costo_envio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tiempo_estimado` INT(11) DEFAULT NULL COMMENT 'Tiempo en minutos',
  `activo` TINYINT(1) DEFAULT 1,
  `orden` INT(11) DEFAULT 0 COMMENT 'Orden de visualización',
  PRIMARY KEY (`id`),
  KEY `idx_zonas_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Agregar columna orden si no existe
ALTER TABLE `zonas_delivery` 
ADD COLUMN IF NOT EXISTS `orden` INT(11) DEFAULT 0 COMMENT 'Orden de visualización';

-- Insertar zonas por defecto si no existen
INSERT INTO `zonas_delivery` (`nombre`, `descripcion`, `costo_envio`, `tiempo_estimado`, `activo`, `orden`) 
VALUES 
('Centro de Sechura', 'Zona céntrica de la ciudad', 5.00, 20, 1, 1),
('Parachique', 'Playa Parachique y alrededores', 8.00, 35, 1, 2),
('Vice', 'Distrito de Vice', 10.00, 40, 1, 3),
('Bernal', 'Distrito de Bernal', 12.00, 45, 1, 4),
('Cristo Nos Valga', 'Puerto Cristo Nos Valga', 15.00, 50, 1, 5)
ON DUPLICATE KEY UPDATE `nombre`=`nombre`;

-- =============================================
-- 3. ASEGURAR TABLA DELIVERIES
-- =============================================
CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` INT(11) NOT NULL,
  `direccion` TEXT NOT NULL,
  `referencia` TEXT DEFAULT NULL,
  `zona_id` INT(11) NOT NULL,
  `repartidor_id` INT(11) DEFAULT NULL,
  `estado` ENUM('pendiente','asignado','en_camino','entregado','fallido') DEFAULT 'pendiente',
  `fecha_asignacion` TIMESTAMP NULL DEFAULT NULL,
  `fecha_entrega` TIMESTAMP NULL DEFAULT NULL,
  `observaciones` TEXT DEFAULT NULL,
  `latitud` DECIMAL(10,8) DEFAULT NULL COMMENT 'Para tracking GPS',
  `longitud` DECIMAL(11,8) DEFAULT NULL COMMENT 'Para tracking GPS',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedido_id` (`pedido_id`),
  KEY `idx_deliveries_pedido` (`pedido_id`),
  KEY `idx_deliveries_repartidor` (`repartidor_id`),
  KEY `idx_deliveries_zona` (`zona_id`),
  KEY `idx_deliveries_estado` (`estado`),
  CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`zona_id`) REFERENCES `zonas_delivery` (`id`),
  CONSTRAINT `deliveries_ibfk_3` FOREIGN KEY (`repartidor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 4. TABLA DE CARRITO DE COMPRAS (TEMPORAL)
-- =============================================
CREATE TABLE IF NOT EXISTS `carrito` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` INT(11) DEFAULT NULL COMMENT 'NULL para invitados con session_id',
  `session_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID de sesión para invitados',
  `tipo_producto` ENUM('plato','combo') NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL DEFAULT 1,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `notas` TEXT DEFAULT NULL COMMENT 'Instrucciones especiales',
  `fecha_agregado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_carrito_cliente` (`cliente_id`),
  KEY `idx_carrito_session` (`session_id`),
  KEY `idx_carrito_fecha` (`fecha_agregado`),
  CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 5. TABLA DE FAVORITOS DE CLIENTES
-- =============================================
CREATE TABLE IF NOT EXISTS `cliente_favoritos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` INT(11) NOT NULL,
  `tipo_producto` ENUM('plato','combo') NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `fecha_agregado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_favorito` (`cliente_id`, `tipo_producto`, `producto_id`),
  KEY `idx_favoritos_cliente` (`cliente_id`),
  CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- 6. TABLA DE CUPONES/CÓDIGOS DE DESCUENTO
-- =============================================
CREATE TABLE IF NOT EXISTS `cupones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(50) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `tipo_descuento` ENUM('porcentaje','monto_fijo') NOT NULL DEFAULT 'porcentaje',
  `valor_descuento` DECIMAL(10,2) NOT NULL,
  `monto_minimo` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Monto mínimo de compra',
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `usos_maximos` INT(11) DEFAULT NULL COMMENT 'NULL = ilimitado',
  `usos_actuales` INT(11) DEFAULT 0,
  `activo` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `idx_cupones_activo` (`activo`),
  KEY `idx_cupones_fechas` (`fecha_inicio`, `fecha_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar cupones de ejemplo
INSERT INTO `cupones` (`codigo`, `descripcion`, `tipo_descuento`, `valor_descuento`, `monto_minimo`, `fecha_inicio`, `fecha_fin`, `usos_maximos`, `activo`) 
VALUES 
('BIENVENIDO10', 'Descuento de bienvenida para nuevos clientes', 'porcentaje', 10.00, 30.00, '2026-01-01', '2026-12-31', NULL, 1),
('PRIMERACOMPRA', 'Primera compra con 15% de descuento', 'porcentaje', 15.00, 50.00, '2026-01-01', '2026-12-31', 1, 1),
('DELIVERY5', 'S/5 de descuento en delivery', 'monto_fijo', 5.00, 25.00, '2026-01-01', '2026-12-31', NULL, 1)
ON DUPLICATE KEY UPDATE `codigo`=`codigo`;

-- =============================================
-- 7. ACTUALIZAR CONFIGURACIÓN PARA DELIVERY
-- =============================================
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`) 
VALUES 
('delivery_habilitado', '1', 'Habilitar módulo de delivery: 1=Activo, 0=Inactivo'),
('pedido_online_habilitado', '1', 'Permitir pedidos online de clientes: 1=Sí, 0=No'),
('requiere_verificacion_email', '0', 'Requiere verificar email para hacer pedidos: 1=Sí, 0=No'),
('tiempo_max_entrega', '90', 'Tiempo máximo estimado de entrega en minutos'),
('radio_entrega_km', '15', 'Radio de cobertura de delivery en kilómetros'),
('horario_delivery_inicio', '10:00', 'Hora de inicio de delivery'),
('horario_delivery_fin', '22:00', 'Hora de fin de delivery'),
('whatsapp_contacto', '51987654321', 'Número de WhatsApp para contacto (con código país)')
ON DUPLICATE KEY UPDATE `clave`=`clave`;

-- =============================================
-- COMPLETADO
-- =============================================
SELECT 'Base de datos actualizada correctamente para módulo de delivery' AS mensaje;
