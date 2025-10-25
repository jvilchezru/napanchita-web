-- Base de datos para sistema de pedidos Napanchita
CREATE DATABASE IF NOT EXISTS napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE napanchita_db;

-- Tabla de usuarios (clientes y administradores)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de categorías de productos
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de productos del menú
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    categoria_id INT,
    imagen VARCHAR(255),
    disponible BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'preparando', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    direccion_entrega TEXT NOT NULL,
    telefono_contacto VARCHAR(20) NOT NULL,
    notas TEXT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de detalles de pedidos
CREATE TABLE IF NOT EXISTS detalles_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar datos de ejemplo
INSERT INTO usuarios (nombre, email, password, telefono, rol) VALUES 
('Administrador', 'admin@napanchita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0001', 'admin'),
('Juan Pérez', 'juan@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0002', 'cliente');
-- Contraseña para ambos: "password"

INSERT INTO categorias (nombre, descripcion) VALUES 
('Entradas', 'Aperitivos y entradas'),
('Platos Principales', 'Platos fuertes del menú'),
('Bebidas', 'Bebidas frías y calientes'),
('Postres', 'Dulces y postres');

INSERT INTO productos (nombre, descripcion, precio, categoria_id, imagen) VALUES 
('Empanadas Salteñas', 'Deliciosas empanadas tradicionales (3 unidades)', 8.50, 1, 'empanadas.jpg'),
('Pique Macho', 'Plato abundante con carne, papas y verduras', 25.00, 2, 'pique.jpg'),
('Silpancho', 'Milanesa con arroz, papa y ensalada', 22.00, 2, 'silpancho.jpg'),
('Chicharrón de Cerdo', 'Chicharrón crujiente con mote y llajua', 28.00, 2, 'chicharron.jpg'),
('Api con Pastel', 'Bebida tradicional con pastel de queso', 6.00, 3, 'api.jpg'),
('Refresco Natural', 'Jugo natural de frutas de temporada', 5.00, 3, 'refresco.jpg'),
('Helado de Canela', 'Postre tradicional boliviano', 7.00, 4, 'helado.jpg');
