-- ============================================
-- ACTUALIZAR ESTADO DE PEDIDOS - AGREGAR 'finalizado'
-- ============================================

USE napanchita_db;

-- Modificar la columna estado para incluir 'finalizado'
ALTER TABLE pedidos 
MODIFY COLUMN estado ENUM('pendiente', 'en_preparacion', 'listo', 'entregado', 'finalizado', 'cancelado') 
DEFAULT 'pendiente';

-- Verificar el cambio
DESCRIBE pedidos;

-- Mostrar pedidos actuales
SELECT id, tipo, estado, total FROM pedidos ORDER BY id DESC LIMIT 10;
