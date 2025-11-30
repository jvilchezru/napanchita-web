-- Migración: Cambiar módulo de productos a platos
-- Fecha: 2025-11-29
-- Sistema Napanchita

USE napanchita_db;

-- 1. Renombrar tabla productos a platos
ALTER TABLE productos RENAME TO platos;

-- 2. Renombrar tabla combo_productos a combo_platos
ALTER TABLE combo_productos RENAME TO combo_platos;

-- 3. Actualizar columna producto_id a plato_id en combo_platos
ALTER TABLE combo_platos CHANGE COLUMN producto_id plato_id INT NOT NULL;

-- 4. Actualizar columna producto_id a plato_id en pedido_items
ALTER TABLE pedido_items CHANGE COLUMN producto_id plato_id INT NULL;

-- 5. Actualizar columna tipo en pedido_items (cambiar 'producto' a 'plato')
UPDATE pedido_items SET tipo = 'plato' WHERE tipo = 'producto';

-- 6. Eliminar foreign keys antiguas y recrearlas con nuevos nombres
-- En combo_platos
ALTER TABLE combo_platos DROP FOREIGN KEY combo_platos_ibfk_1;
ALTER TABLE combo_platos DROP FOREIGN KEY combo_platos_ibfk_2;
ALTER TABLE combo_platos 
    ADD CONSTRAINT fk_combo_platos_combo FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_combo_platos_plato FOREIGN KEY (plato_id) REFERENCES platos(id) ON DELETE CASCADE;

-- En pedido_items
ALTER TABLE pedido_items DROP FOREIGN KEY pedido_items_ibfk_2;
ALTER TABLE pedido_items 
    ADD CONSTRAINT fk_pedido_items_plato FOREIGN KEY (plato_id) REFERENCES platos(id) ON DELETE SET NULL;

-- Verificación
SELECT 'Migración completada exitosamente' AS status;
SELECT COUNT(*) AS total_platos FROM platos;
SELECT COUNT(*) AS total_combo_platos FROM combo_platos;
SELECT COUNT(*) AS total_pedido_items FROM pedido_items WHERE plato_id IS NOT NULL;
