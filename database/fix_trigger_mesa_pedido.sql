-- ============================================
-- ACTUALIZAR TRIGGER PARA LIBERAR MESA SOLO AL FINALIZAR
-- ============================================

USE napanchita_db;

-- Eliminar el trigger existente
DROP TRIGGER IF EXISTS trg_liberar_mesa_pedido;

-- Crear el nuevo trigger que solo libera cuando está finalizado o cancelado
DELIMITER $$

CREATE TRIGGER trg_liberar_mesa_pedido
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    -- Solo liberar mesa cuando el estado cambia a 'finalizado' o 'cancelado'
    -- No cuando está 'entregado'
    IF NEW.tipo = 'mesa' AND NEW.estado IN ('finalizado', 'cancelado')
       AND OLD.estado NOT IN ('finalizado', 'cancelado') THEN
        UPDATE mesas
        SET estado = 'disponible'
        WHERE id = NEW.mesa_id;
    END IF;
END$$

DELIMITER ;

-- Verificar el trigger actualizado
SHOW TRIGGERS WHERE `Table` = 'pedidos';
