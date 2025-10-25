-- Mejoras al sistema Napanchita

-- 1. Modificar tabla pedidos para agregar estado 'finalizado' y campo de pago
ALTER TABLE pedidos 
MODIFY COLUMN estado ENUM('pendiente', 'preparando', 'enviado', 'entregado', 'finalizado', 'cancelado') DEFAULT 'pendiente';

ALTER TABLE pedidos 
ADD COLUMN metodo_pago VARCHAR(50) DEFAULT NULL AFTER notas,
ADD COLUMN estado_pago ENUM('pendiente', 'pagado', 'rechazado') DEFAULT 'pendiente' AFTER metodo_pago,
ADD COLUMN fecha_pago TIMESTAMP NULL DEFAULT NULL AFTER estado_pago;

-- 2. Crear tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pedido_id INT,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Agregar índices para mejor rendimiento
CREATE INDEX idx_pedidos_estado ON pedidos(estado);
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id, leida);
