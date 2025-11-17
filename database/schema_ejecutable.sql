
DROP DATABASE IF EXISTS napanchita_db;
CREATE DATABASE napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE napanchita_db;
CREATE TABLE usuarios (
CREATE TABLE clientes (
CREATE TABLE categorias (
CREATE TABLE productos (
CREATE TABLE combos (
CREATE TABLE combo_productos (
CREATE TABLE mesas (
CREATE TABLE reservas (
CREATE TABLE pedidos (
CREATE TABLE pedido_items (
CREATE TABLE zonas_delivery (
CREATE TABLE deliveries (
CREATE TABLE metodos_pago (
CREATE TABLE ventas (
CREATE TABLE cierres_caja (
CREATE TABLE logs (
INSERT INTO usuarios (nombre, email, password, telefono, rol) VALUES
INSERT INTO categorias (nombre, descripcion, orden) VALUES
INSERT INTO productos (categoria_id, nombre, descripcion, precio, disponible) 
VALUES
INSERT INTO combos (nombre, descripcion, precio, activo) VALUES
INSERT INTO combo_productos (combo_id, producto_id, cantidad) VALUES
INSERT INTO combo_productos (combo_id, producto_id, cantidad) VALUES
INSERT INTO combo_productos (combo_id, producto_id, cantidad) VALUES
INSERT INTO mesas (numero, capacidad, estado, posicion_x, posicion_y) VALUES
INSERT INTO zonas_delivery (nombre, descripcion, costo_envio, tiempo_estimado) 
VALUES
INSERT INTO metodos_pago (nombre, descripcion) VALUES
INSERT INTO clientes (nombre, telefono, email, direcciones) VALUES
DELIMITER $$
CREATE TRIGGER trg_calcular_total_pedido
DELIMITER ;
DELIMITER $$
CREATE TRIGGER trg_actualizar_mesa_pedido
DELIMITER ;
DELIMITER $$
CREATE TRIGGER trg_liberar_mesa_pedido
DELIMITER ;
CREATE VIEW v_pedidos_completos AS
SELECT 
CREATE VIEW v_ventas_diarias AS
SELECT 
CREATE VIEW v_productos_top AS
SELECT 
DELIMITER $$
CREATE PROCEDURE sp_verificar_disponibilidad_mesa(
DELIMITER ;
DELIMITER $$
CREATE PROCEDURE sp_generar_codigo_confirmacion(
DELIMITER ;
SHOW TABLES;
SELECT 'Usuarios:' AS Tabla, COUNT(*) AS Registros FROM usuarios
SELECT 'Clientes:', COUNT(*) FROM clientes
SELECT 'CategorÃ­as:', COUNT(*) FROM categorias
SELECT 'Productos:', COUNT(*) FROM productos
SELECT 'Combos:', COUNT(*) FROM combos
SELECT 'Mesas:', COUNT(*) FROM mesas
SELECT 'Zonas Delivery:', COUNT(*) FROM zonas_delivery
SELECT 'MÃ©todos Pago:', COUNT(*) FROM metodos_pago;
SELECT 
USE napanchita_db;
SHOW TABLES;
SELECT * FROM usuarios;
SELECT * FROM categorias;
SELECT * FROM productos LIMIT 5;


