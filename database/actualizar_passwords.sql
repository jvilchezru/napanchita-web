-- Actualizar contraseñas de usuarios a 'password123'
USE napanchita_db;

UPDATE usuarios 
SET password = '$2y$10$crYWbFzDIFl83Jja684S3OdG98aThRkC40exeQme0axjmWJRqf8tq';

-- Verificar actualización
SELECT id, nombre, email, rol, 
       LEFT(password, 30) as password_hash 
FROM usuarios;

SELECT 'Contraseñas actualizadas correctamente!' as Status,
       'Usar: password123 para todos los usuarios' as Password;
