-- =============================================
-- TABLA DE RESEÑAS DE CLIENTES
-- Sistema Napanchita
-- =============================================

USE napanchita_db;

-- Crear tabla de reseñas
CREATE TABLE IF NOT EXISTS `resenas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` INT(11) NOT NULL,
  `calificacion` TINYINT(1) NOT NULL COMMENT '1-5 estrellas',
  `comentario` TEXT NOT NULL,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` TINYINT(1) DEFAULT 1 COMMENT 'Para moderar reseñas',
  `destacado` TINYINT(1) DEFAULT 0 COMMENT 'Reseñas destacadas para mostrar primero',
  PRIMARY KEY (`id`),
  KEY `idx_resenas_cliente` (`cliente_id`),
  KEY `idx_resenas_activo` (`activo`, `destacado`),
  CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar reseñas de ejemplo
INSERT INTO `resenas` (`cliente_id`, `calificacion`, `comentario`, `activo`, `destacado`) VALUES 
(1, 5, '¡Excelente ceviche! Los mariscos fresquísimos y el servicio de primera. Definitivamente volveré.', 1, 1),
(2, 5, 'El mejor ceviche de la zona. La atención es rápida y el sabor inigualable. 100% recomendado.', 1, 1),
(3, 4, 'Muy buenos platos, porciones generosas. El delivery llegó a tiempo y bien empaquetado.', 1, 0)
ON DUPLICATE KEY UPDATE `comentario`=`comentario`;

SELECT 'Tabla de reseñas creada correctamente' AS mensaje;
