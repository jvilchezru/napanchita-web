# SCRIPT SQL COMPLETO - SISTEMA NAPANCHITA

## Base de Datos: napanchita_db

```sql
-- ============================================
-- CREACIÓN DE BASE DE DATOS
-- ============================================

DROP DATABASE IF EXISTS napanchita_db;
CREATE DATABASE napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE napanchita_db;

-- ============================================
-- TABLA: USUARIOS (Personal del Sistema)
-- ============================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol ENUM('admin', 'mesero', 'repartidor') NOT NULL DEFAULT 'mesero',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_usuarios_email (email),
    INDEX idx_usuarios_rol (rol),
    INDEX idx_usuarios_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Personal con acceso al sistema';

-- ============================================
-- TABLA: CLIENTES (Clientes Externos)
-- ============================================

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    direcciones JSON COMMENT 'Array de direcciones del cliente',
    notas TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_clientes_telefono (telefono),
    INDEX idx_clientes_email (email),
    INDEX idx_clientes_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Clientes sin acceso al sistema';

-- ============================================
-- TABLA: CATEGORIAS
-- ============================================

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    orden INT DEFAULT 0 COMMENT 'Orden de visualización',
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_categorias_activo (activo),
    INDEX idx_categorias_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: PRODUCTOS
-- ============================================

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL CHECK (precio > 0),
    imagen_url VARCHAR(255),
    disponible BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    
    INDEX idx_productos_categoria (categoria_id),
    INDEX idx_productos_disponible (disponible),
    INDEX idx_productos_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: COMBOS
-- ============================================

CREATE TABLE combos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL CHECK (precio > 0),
    imagen_url VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_combos_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: COMBO_PRODUCTOS (Relación N:M)
-- ============================================

CREATE TABLE combo_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    combo_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1 CHECK (cantidad > 0),
    
    FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    
    UNIQUE KEY uk_combo_producto (combo_id, producto_id),
    INDEX idx_combo_productos_combo (combo_id),
    INDEX idx_combo_productos_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: MESAS
-- ============================================

CREATE TABLE mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10) UNIQUE NOT NULL,
    capacidad INT NOT NULL CHECK (capacidad > 0),
    estado ENUM('disponible', 'ocupada', 'reservada', 'inactiva') DEFAULT 'disponible',
    posicion_x INT DEFAULT 0 COMMENT 'Posición X en layout visual',
    posicion_y INT DEFAULT 0 COMMENT 'Posición Y en layout visual',
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_mesas_estado (estado),
    INDEX idx_mesas_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: RESERVAS
-- ============================================

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    mesa_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personas INT NOT NULL CHECK (personas > 0),
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada', 'no_show') DEFAULT 'pendiente',
    codigo_confirmacion VARCHAR(20) UNIQUE NOT NULL,
    notas TEXT,
    creado_por_usuario_id INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE RESTRICT,
    FOREIGN KEY (creado_por_usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    
    INDEX idx_reservas_cliente (cliente_id),
    INDEX idx_reservas_mesa (mesa_id),
    INDEX idx_reservas_fecha (fecha, hora),
    INDEX idx_reservas_estado (estado),
    INDEX idx_reservas_codigo (codigo_confirmacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: PEDIDOS
-- ============================================

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    mesa_id INT,
    usuario_id INT NOT NULL COMMENT 'Usuario que registró el pedido',
    tipo ENUM('mesa', 'delivery', 'para_llevar') NOT NULL,
    estado ENUM('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado') DEFAULT 'pendiente',
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0,
    costo_envio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    descuento DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0,
    notas TEXT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    
    INDEX idx_pedidos_cliente (cliente_id),
    INDEX idx_pedidos_mesa (mesa_id),
    INDEX idx_pedidos_usuario (usuario_id),
    INDEX idx_pedidos_tipo (tipo),
    INDEX idx_pedidos_estado (estado),
    INDEX idx_pedidos_fecha (fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: PEDIDO_ITEMS (Detalles del Pedido)
-- ============================================

CREATE TABLE pedido_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT,
    combo_id INT,
    tipo ENUM('producto', 'combo') NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'Snapshot del nombre para histórico',
    cantidad INT NOT NULL DEFAULT 1 CHECK (cantidad > 0),
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    notas TEXT,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL,
    FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE SET NULL,
    
    INDEX idx_pedido_items_pedido (pedido_id),
    INDEX idx_pedido_items_producto (producto_id),
    INDEX idx_pedido_items_combo (combo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: ZONAS_DELIVERY
-- ============================================

CREATE TABLE zonas_delivery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    costo_envio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    tiempo_estimado INT COMMENT 'Tiempo en minutos',
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_zonas_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: DELIVERIES
-- ============================================

CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNIQUE NOT NULL,
    direccion TEXT NOT NULL,
    referencia TEXT,
    zona_id INT NOT NULL,
    repartidor_id INT,
    estado ENUM('pendiente', 'asignado', 'en_camino', 'entregado', 'fallido') DEFAULT 'pendiente',
    fecha_asignacion TIMESTAMP NULL,
    fecha_entrega TIMESTAMP NULL,
    observaciones TEXT,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (zona_id) REFERENCES zonas_delivery(id) ON DELETE RESTRICT,
    FOREIGN KEY (repartidor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_deliveries_pedido (pedido_id),
    INDEX idx_deliveries_repartidor (repartidor_id),
    INDEX idx_deliveries_zona (zona_id),
    INDEX idx_deliveries_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: METODOS_PAGO
-- ============================================

CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    
    INDEX idx_metodos_pago_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: VENTAS
-- ============================================

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNIQUE NOT NULL,
    metodo_pago_id INT NOT NULL,
    monto_recibido DECIMAL(10, 2) NOT NULL,
    monto_cambio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    descuento_aplicado DECIMAL(10, 2) DEFAULT 0,
    codigo_descuento VARCHAR(50),
    usuario_id INT NOT NULL COMMENT 'Cajero que registró',
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ticket_generado BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE RESTRICT,
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    
    INDEX idx_ventas_pedido (pedido_id),
    INDEX idx_ventas_fecha (fecha_venta),
    INDEX idx_ventas_metodo_pago (metodo_pago_id),
    INDEX idx_ventas_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: CIERRES_CAJA
-- ============================================

CREATE TABLE cierres_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_apertura TIME,
    hora_cierre TIME,
    monto_inicial DECIMAL(10, 2) DEFAULT 0,
    total_efectivo_sistema DECIMAL(10, 2) DEFAULT 0,
    total_efectivo_fisico DECIMAL(10, 2) DEFAULT 0,
    total_tarjeta DECIMAL(10, 2) DEFAULT 0,
    total_digital DECIMAL(10, 2) DEFAULT 0 COMMENT 'Yape, Plin, etc.',
    total_ventas DECIMAL(10, 2) DEFAULT 0,
    diferencia DECIMAL(10, 2) DEFAULT 0,
    observaciones TEXT,
    fecha_cierre TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    
    INDEX idx_cierres_usuario (usuario_id),
    INDEX idx_cierres_fecha (fecha),
    UNIQUE KEY uk_cierre_fecha_usuario (fecha, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLA: LOGS (Opcional - para auditoría)
-- ============================================

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla VARCHAR(50),
    registro_id INT,
    detalles TEXT,
    ip VARCHAR(45),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_logs_usuario (usuario_id),
    INDEX idx_logs_fecha (fecha),
    INDEX idx_logs_accion (accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Insertar usuarios de sistema
-- Password: "password123" hasheado con bcrypt
INSERT INTO usuarios (nombre, email, password, telefono, rol) VALUES
('Administrador', 'admin@napanchita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654321', 'admin'),
('Carlos Mesero', 'mesero@napanchita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654322', 'mesero'),
('Luis Repartidor', 'repartidor@napanchita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '987654323', 'repartidor');

-- Insertar categorías
INSERT INTO categorias (nombre, descripcion, orden) VALUES
('Ceviches', 'Frescos ceviches peruanos', 1),
('Chicharrones', 'Chicharrones y frituras', 2),
('Jalea', 'Jaleas mixtas', 3),
('Arroces', 'Arroces marinos', 4),
('Bebidas', 'Bebidas frías y calientes', 5),
('Entradas', 'Aperitivos y entradas', 6),
('Postres', 'Postres tradicionales', 7);

-- Insertar productos
INSERT INTO productos (categoria_id, nombre, descripcion, precio, disponible) VALUES
-- Ceviches
(1, 'Ceviche de Pescado', 'Ceviche clásico de pescado fresco', 25.00, TRUE),
(1, 'Ceviche Mixto', 'Ceviche con pescado, pulpo y calamares', 30.00, TRUE),
(1, 'Ceviche de Conchas Negras', 'Exquisito ceviche de conchas negras', 35.00, TRUE),
(1, 'Leche de Tigre', 'Ceviche licuado bien concentrado', 15.00, TRUE),

-- Chicharrones
(2, 'Chicharrón de Pescado', 'Pescado frito crujiente', 28.00, TRUE),
(2, 'Chicharrón de Calamar', 'Anillos de calamar fritos', 32.00, TRUE),
(2, 'Chicharrón Mixto', 'Mix de mariscos fritos', 35.00, TRUE),

-- Jalea
(3, 'Jalea Mixta Personal', 'Porción individual', 30.00, TRUE),
(3, 'Jalea Mixta Familiar', 'Para compartir (3-4 personas)', 80.00, TRUE),

-- Arroces
(4, 'Arroz con Mariscos', 'Arroz con variedad de mariscos', 28.00, TRUE),
(4, 'Arroz con Conchas Negras', 'Arroz con conchas negras', 35.00, TRUE),

-- Bebidas
(5, 'Chicha Morada 1L', 'Bebida tradicional peruana', 8.00, TRUE),
(5, 'Inka Kola 1.5L', 'Gaseosa nacional', 7.00, TRUE),
(5, 'Cerveza Cristal', 'Cerveza nacional', 8.00, TRUE),
(5, 'Limonada Frozen', 'Limonada helada', 10.00, TRUE),

-- Entradas
(6, 'Causa Rellena', 'Causa de pollo o atún', 15.00, TRUE),
(6, 'Papa a la Huancaína', 'Papas con salsa huancaína', 12.00, TRUE),
(6, 'Tequeños', 'Tequeños de queso (6 unid.)', 18.00, TRUE),

-- Postres
(7, 'Suspiro Limeño', 'Postre tradicional', 12.00, TRUE),
(7, 'Mazamorra Morada', 'Postre tradicional', 8.00, TRUE);

-- Insertar combos
INSERT INTO combos (nombre, descripcion, precio, activo) VALUES
('Combo Cevichero', 'Ceviche de pescado + Chicha morada 1L', 28.00, TRUE),
('Combo Familiar', 'Jalea familiar + 2 Inka Kola 1.5L', 85.00, TRUE),
('Combo Ejecutivo', 'Arroz con mariscos + Bebida + Postre', 38.00, TRUE);

-- Relacionar productos con combos
-- Combo 1: Cevichero
INSERT INTO combo_productos (combo_id, producto_id, cantidad) VALUES
(1, 1, 1),  -- Ceviche de pescado
(1, 12, 1); -- Chicha morada

-- Combo 2: Familiar
INSERT INTO combo_productos (combo_id, producto_id, cantidad) VALUES
(2, 9, 1),  -- Jalea familiar
(2, 13, 2); -- Inka Kola

-- Combo 3: Ejecutivo
INSERT INTO combo_productos (combo_id, producto_id, cantidad) VALUES
(3, 10, 1), -- Arroz con mariscos
(3, 15, 1), -- Limonada
(3, 19, 1); -- Suspiro limeño

-- Insertar mesas
INSERT INTO mesas (numero, capacidad, estado, posicion_x, posicion_y) VALUES
('M01', 2, 'disponible', 50, 50),
('M02', 2, 'disponible', 150, 50),
('M03', 4, 'disponible', 250, 50),
('M04', 4, 'disponible', 350, 50),
('M05', 4, 'disponible', 50, 150),
('M06', 4, 'disponible', 150, 150),
('M07', 6, 'disponible', 250, 150),
('M08', 6, 'disponible', 350, 150),
('M09', 8, 'disponible', 150, 250),
('M10', 8, 'disponible', 250, 250);

-- Insertar zonas de delivery
INSERT INTO zonas_delivery (nombre, descripcion, costo_envio, tiempo_estimado) VALUES
('Centro', 'Centro de la ciudad', 5.00, 30),
('Norte', 'Zona norte', 8.00, 45),
('Sur', 'Zona sur', 8.00, 45),
('Este', 'Zona este', 10.00, 60),
('Oeste', 'Zona oeste', 10.00, 60);

-- Insertar métodos de pago
INSERT INTO metodos_pago (nombre, descripcion) VALUES
('Efectivo', 'Pago en efectivo'),
('Tarjeta', 'Tarjeta de débito o crédito'),
('Yape', 'Pago por Yape'),
('Plin', 'Pago por Plin'),
('Transferencia', 'Transferencia bancaria');

-- Insertar clientes de ejemplo
INSERT INTO clientes (nombre, telefono, email, direcciones) VALUES
('Juan Pérez', '987123456', 'juan.perez@email.com', 
 '[{"id":1,"direccion":"Av. Arequipa 1234","referencia":"Edificio azul","zona_id":1,"principal":true}]'),
('María García', '987234567', 'maria.garcia@email.com',
 '[{"id":1,"direccion":"Jr. Lima 567","referencia":"Casa blanca","zona_id":1,"principal":true}]'),
('Carlos López', '987345678', 'carlos.lopez@email.com',
 '[{"id":1,"direccion":"Calle Los Olivos 890","referencia":"Frente al parque","zona_id":2,"principal":true}]');

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger para actualizar total del pedido
DELIMITER $$

CREATE TRIGGER trg_calcular_total_pedido
AFTER INSERT ON pedido_items
FOR EACH ROW
BEGIN
    UPDATE pedidos 
    SET subtotal = (
        SELECT IFNULL(SUM(subtotal), 0) 
        FROM pedido_items 
        WHERE pedido_id = NEW.pedido_id
    ),
    total = subtotal + costo_envio - descuento
    WHERE id = NEW.pedido_id;
END$$

DELIMITER ;

-- Trigger para actualizar estado de mesa al crear pedido
DELIMITER $$

CREATE TRIGGER trg_actualizar_mesa_pedido
AFTER INSERT ON pedidos
FOR EACH ROW
BEGIN
    IF NEW.tipo = 'mesa' AND NEW.mesa_id IS NOT NULL THEN
        UPDATE mesas 
        SET estado = 'ocupada' 
        WHERE id = NEW.mesa_id;
    END IF;
END$$

DELIMITER ;

-- Trigger para liberar mesa al completar pedido
DELIMITER $$

CREATE TRIGGER trg_liberar_mesa_pedido
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    IF NEW.tipo = 'mesa' AND NEW.estado IN ('entregado', 'cancelado') 
       AND OLD.estado NOT IN ('entregado', 'cancelado') THEN
        UPDATE mesas 
        SET estado = 'disponible' 
        WHERE id = NEW.mesa_id;
    END IF;
END$$

DELIMITER ;

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista de pedidos con detalles
CREATE VIEW v_pedidos_completos AS
SELECT 
    p.id,
    p.tipo,
    p.estado,
    p.total,
    p.fecha_pedido,
    c.nombre AS cliente_nombre,
    c.telefono AS cliente_telefono,
    m.numero AS mesa_numero,
    u.nombre AS usuario_nombre,
    COUNT(pi.id) AS total_items
FROM pedidos p
LEFT JOIN clientes c ON p.cliente_id = c.id
LEFT JOIN mesas m ON p.mesa_id = m.id
LEFT JOIN usuarios u ON p.usuario_id = u.id
LEFT JOIN pedido_items pi ON p.id = pi.pedido_id
GROUP BY p.id;

-- Vista de ventas diarias
CREATE VIEW v_ventas_diarias AS
SELECT 
    DATE(v.fecha_venta) AS fecha,
    COUNT(v.id) AS total_ventas,
    SUM(v.total) AS monto_total,
    mp.nombre AS metodo_pago,
    SUM(CASE WHEN mp.nombre = 'Efectivo' THEN v.total ELSE 0 END) AS total_efectivo,
    SUM(CASE WHEN mp.nombre = 'Tarjeta' THEN v.total ELSE 0 END) AS total_tarjeta,
    SUM(CASE WHEN mp.nombre IN ('Yape', 'Plin', 'Transferencia') THEN v.total ELSE 0 END) AS total_digital
FROM ventas v
JOIN metodos_pago mp ON v.metodo_pago_id = mp.id
GROUP BY DATE(v.fecha_venta), mp.nombre;

-- Vista de productos más vendidos
CREATE VIEW v_productos_top AS
SELECT 
    p.id,
    p.nombre,
    c.nombre AS categoria,
    COUNT(pi.id) AS veces_vendido,
    SUM(pi.cantidad) AS cantidad_total,
    SUM(pi.subtotal) AS ingresos_totales
FROM productos p
JOIN categorias c ON p.categoria_id = c.id
JOIN pedido_items pi ON p.id = pi.producto_id
JOIN pedidos ped ON pi.pedido_id = ped.id
WHERE ped.estado != 'cancelado'
GROUP BY p.id
ORDER BY cantidad_total DESC;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

-- Procedimiento para obtener disponibilidad de mesas
DELIMITER $$

CREATE PROCEDURE sp_verificar_disponibilidad_mesa(
    IN p_fecha DATE,
    IN p_hora TIME,
    IN p_personas INT
)
BEGIN
    SELECT 
        m.id,
        m.numero,
        m.capacidad,
        m.estado,
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM reservas r 
                WHERE r.mesa_id = m.id 
                AND r.fecha = p_fecha 
                AND r.estado IN ('confirmada', 'pendiente')
                AND ABS(TIME_TO_SEC(TIMEDIFF(r.hora, p_hora))) < 7200
            ) THEN 'Reservada'
            WHEN m.estado = 'ocupada' THEN 'Ocupada'
            ELSE 'Disponible'
        END AS disponibilidad
    FROM mesas m
    WHERE m.capacidad >= p_personas 
    AND m.activo = TRUE
    ORDER BY m.capacidad ASC;
END$$

DELIMITER ;

-- Procedimiento para generar código de confirmación único
DELIMITER $$

CREATE PROCEDURE sp_generar_codigo_confirmacion(
    OUT p_codigo VARCHAR(20)
)
BEGIN
    DECLARE v_existe INT;
    REPEAT
        SET p_codigo = CONCAT(
            'RES',
            LPAD(FLOOR(RAND() * 999999), 6, '0')
        );
        SELECT COUNT(*) INTO v_existe 
        FROM reservas 
        WHERE codigo_confirmacion = p_codigo;
    UNTIL v_existe = 0 END REPEAT;
END$$

DELIMITER ;

-- ============================================
-- PERMISOS Y SEGURIDAD
-- ============================================

-- Crear usuario para la aplicación (en producción)
-- CREATE USER 'napanchita_user'@'localhost' IDENTIFIED BY 'password_seguro_aqui';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON napanchita_db.* TO 'napanchita_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- FINALIZACIÓN
-- ============================================

-- Verificar tablas creadas
SHOW TABLES;

-- Verificar datos iniciales
SELECT 'Usuarios:' AS Tabla, COUNT(*) AS Registros FROM usuarios
UNION ALL
SELECT 'Clientes:', COUNT(*) FROM clientes
UNION ALL
SELECT 'Categorías:', COUNT(*) FROM categorias
UNION ALL
SELECT 'Productos:', COUNT(*) FROM productos
UNION ALL
SELECT 'Combos:', COUNT(*) FROM combos
UNION ALL
SELECT 'Mesas:', COUNT(*) FROM mesas
UNION ALL
SELECT 'Zonas Delivery:', COUNT(*) FROM zonas_delivery
UNION ALL
SELECT 'Métodos Pago:', COUNT(*) FROM metodos_pago;

-- Mensaje de finalización
SELECT 
    'Base de datos napanchita_db creada exitosamente!' AS Status,
    '15 tablas creadas' AS Tablas,
    '3 usuarios, 20 productos, 10 mesas, 5 zonas' AS Datos_Iniciales;
```

---

## Instrucciones de Uso

### 1. Ejecutar el Script

**Desde phpMyAdmin:**
1. Abrir phpMyAdmin
2. Ir a la pestaña "SQL"
3. Copiar y pegar todo el script
4. Click en "Continuar"

**Desde línea de comandos:**
```bash
mysql -u root -p < schema_completo.sql
```

### 2. Credenciales de Acceso

**Usuario Admin:**
- Email: `admin@napanchita.com`
- Password: `password123`
- Rol: admin

**Usuario Mesero:**
- Email: `mesero@napanchita.com`
- Password: `password123`
- Rol: mesero

**Usuario Repartidor:**
- Email: `repartidor@napanchita.com`
- Password: `password123`
- Rol: repartidor

### 3. Verificación

```sql
-- Verificar estructura
USE napanchita_db;
SHOW TABLES;

-- Verificar datos
SELECT * FROM usuarios;
SELECT * FROM categorias;
SELECT * FROM productos LIMIT 5;
```

---

## Resumen de la Base de Datos

### Tablas Creadas: 16
1. usuarios
2. clientes
3. categorias
4. productos
5. combos
6. combo_productos
7. mesas
8. reservas
9. pedidos
10. pedido_items
11. zonas_delivery
12. deliveries
13. metodos_pago
14. ventas
15. cierres_caja
16. logs

### Vistas Creadas: 3
1. v_pedidos_completos
2. v_ventas_diarias
3. v_productos_top

### Procedimientos: 2
1. sp_verificar_disponibilidad_mesa
2. sp_generar_codigo_confirmacion

### Triggers: 3
1. trg_calcular_total_pedido
2. trg_actualizar_mesa_pedido
3. trg_liberar_mesa_pedido

### Datos Iniciales:
- ✅ 3 usuarios del sistema
- ✅ 7 categorías
- ✅ 20 productos
- ✅ 3 combos
- ✅ 10 mesas
- ✅ 5 zonas de delivery
- ✅ 5 métodos de pago
- ✅ 3 clientes de ejemplo

---

**Elaborado:** 16/11/2025  
**Versión:** 1.0  
**Motor:** MySQL 8.0+  
**Charset:** utf8mb4
