-- ============================================
-- SCRIPT DE VERIFICACIÓN - TRIGGERS DE PEDIDOS
-- ============================================

USE napanchita_db;

-- Mostrar todos los triggers de la tabla pedidos
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING,
    ACTION_STATEMENT
FROM information_schema.TRIGGERS
WHERE EVENT_OBJECT_TABLE = 'pedidos'
AND TRIGGER_SCHEMA = 'napanchita_db';

-- Verificar estados de pedidos actuales
SELECT 
    p.id,
    p.tipo,
    p.estado,
    p.mesa_id,
    m.numero as mesa_numero,
    m.estado as mesa_estado
FROM pedidos p
LEFT JOIN mesas m ON p.mesa_id = m.id
WHERE p.tipo = 'mesa'
ORDER BY p.id DESC
LIMIT 10;
