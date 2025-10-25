-- Script adicional: Consultas útiles para mantenimiento

-- Ver todos los usuarios
SELECT id, nombre, email, rol, fecha_registro FROM usuarios;

-- Ver productos más pedidos
SELECT p.nombre, COUNT(dp.id) as total_pedidos, SUM(dp.cantidad) as cantidad_total
FROM productos p
INNER JOIN detalles_pedidos dp ON p.id = dp.producto_id
GROUP BY p.id
ORDER BY total_pedidos DESC;

-- Ver pedidos por estado
SELECT estado, COUNT(*) as total, SUM(total) as monto_total
FROM pedidos
GROUP BY estado;

-- Ver clientes con más pedidos
SELECT u.nombre, u.email, COUNT(pe.id) as total_pedidos, SUM(pe.total) as monto_total
FROM usuarios u
INNER JOIN pedidos pe ON u.id = pe.usuario_id
WHERE u.rol = 'cliente'
GROUP BY u.id
ORDER BY total_pedidos DESC;

-- Ver pedidos del día
SELECT pe.id, u.nombre, pe.total, pe.estado, pe.fecha_pedido
FROM pedidos pe
INNER JOIN usuarios u ON pe.usuario_id = u.id
WHERE DATE(pe.fecha_pedido) = CURDATE()
ORDER BY pe.fecha_pedido DESC;

-- Limpiar pedidos antiguos (más de 6 meses)
-- DELETE FROM pedidos WHERE fecha_pedido < DATE_SUB(NOW(), INTERVAL 6 MONTH);

-- Resetear contraseña de usuario (password = "password")
-- UPDATE usuarios 
-- SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
-- WHERE email = 'admin@napanchita.com';

-- Ver estadísticas generales
SELECT 
    (SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente') as total_clientes,
    (SELECT COUNT(*) FROM productos WHERE disponible = TRUE) as productos_disponibles,
    (SELECT COUNT(*) FROM pedidos) as total_pedidos,
    (SELECT SUM(total) FROM pedidos WHERE estado = 'entregado') as ingresos_totales;
