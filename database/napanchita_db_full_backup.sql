-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: napanchita_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) DEFAULT 0 COMMENT 'Orden de visualizaci??n',
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_categorias_activo` (`activo`),
  KEY `idx_categorias_orden` (`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `orden`, `activo`) VALUES (1,'Ceviches','Frescos ceviches norteños',1,1),(2,'Chicharrones','Chicharrones y frituras',2,1),(3,'Sudados','Sudados mixtos',3,1),(4,'Arroces','Arroces marinos',4,1),(5,'Bebidas','Bebidas frías y calientes',5,1),(6,'Entradas','Aperitivos y entradas',6,1),(9,'Guarniciones','Acompañantes',8,NULL),(10,'pescado frito','exquisito plato',1,1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cierres_caja`
--

DROP TABLE IF EXISTS `cierres_caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cierres_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_apertura` time DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  `monto_inicial` decimal(10,2) DEFAULT 0.00,
  `total_efectivo_sistema` decimal(10,2) DEFAULT 0.00,
  `total_efectivo_fisico` decimal(10,2) DEFAULT 0.00,
  `total_tarjeta` decimal(10,2) DEFAULT 0.00,
  `total_digital` decimal(10,2) DEFAULT 0.00 COMMENT 'Yape, Plin, etc.',
  `total_ventas` decimal(10,2) DEFAULT 0.00,
  `diferencia` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `fecha_cierre` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cierre_fecha_usuario` (`fecha`,`usuario_id`),
  KEY `idx_cierres_usuario` (`usuario_id`),
  KEY `idx_cierres_fecha` (`fecha`),
  CONSTRAINT `cierres_caja_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cierres_caja`
--

LOCK TABLES `cierres_caja` WRITE;
/*!40000 ALTER TABLE `cierres_caja` DISABLE KEYS */;
/*!40000 ALTER TABLE `cierres_caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direcciones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array de direcciones del cliente' CHECK (json_valid(`direcciones`)),
  `notas` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_clientes_telefono` (`telefono`),
  KEY `idx_clientes_email` (`email`),
  KEY `idx_clientes_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Clientes sin acceso al sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `email`, `direcciones`, `notas`, `fecha_registro`, `activo`) VALUES (1,'Juan Pérez','987123456','juan.perez@email.com','[{\"id\":1,\"direccion\":\"Av. Arequipa 1234\",\"referencia\":\"Edificio azul\",\"zona_id\":1,\"principal\":true}]',NULL,'2025-11-17 04:33:23',1),(2,'Maria Garcia','987234567','maria.garcia@email.com','[{\"id\":1,\"direccion\":\"Jr. Lima 567\",\"referencia\":\"Casa blanca\",\"zona_id\":1,\"principal\":true}]',NULL,'2025-11-17 04:33:23',1),(3,'Carlos Lopez','987345678','carlos.lopez@email.com','[{\"id\":1,\"direccion\":\"Calle Los Olivos 890\",\"referencia\":\"Frente al parque\",\"zona_id\":2,\"principal\":true}]',NULL,'2025-11-17 04:33:23',1),(4,'Armando Vilchez','987167784','armando@gmail.com','[{\"id\":1,\"direccion\":\"A.H nueva esperanza\",\"referencia\":\"Avenida Brasil\",\"principal\":true}]',NULL,'2025-11-27 13:34:12',1),(5,'Arianna Galán Vílchez','970456134','ariannasofi@gmail.com','[{\"id\":1,\"direccion\":\"A.H nueva esperanza\",\"referencia\":\"Avenida Brasil\",\"principal\":true}]',NULL,'2025-11-28 16:53:43',1),(6,'Ruth Panta Chapilliquen','933457952','ruthpanta@gmail.com','[{\"id\":1,\"direccion\":\"Parachique - la bocana\",\"referencia\":\"A espaldas de la comisaria\",\"principal\":true}]',NULL,'2025-11-28 17:35:10',1),(7,'Armando Paredes','987654321',NULL,'[{\"id\":1,\"direccion\":\"Sechura\",\"referencia\":\"\",\"principal\":true}]',NULL,'2025-11-30 06:44:14',1);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `combo_platos`
--

DROP TABLE IF EXISTS `combo_platos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `combo_platos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `combo_id` int(11) NOT NULL,
  `plato_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1 CHECK (`cantidad` > 0),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_combo_producto` (`combo_id`,`plato_id`),
  KEY `idx_combo_productos_combo` (`combo_id`),
  KEY `idx_combo_productos_producto` (`plato_id`),
  CONSTRAINT `fk_combo_platos_combo` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_combo_platos_plato` FOREIGN KEY (`plato_id`) REFERENCES `platos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `combo_platos`
--

LOCK TABLES `combo_platos` WRITE;
/*!40000 ALTER TABLE `combo_platos` DISABLE KEYS */;
INSERT INTO `combo_platos` (`id`, `combo_id`, `plato_id`, `cantidad`) VALUES (11,1,1,1),(12,1,5,1),(13,1,6,1),(19,2,3,1),(20,2,1,1),(21,2,5,1),(22,2,10,1),(23,2,6,1),(36,3,3,1),(37,3,2,1),(38,3,7,1),(39,3,10,1),(40,3,12,1),(41,3,13,1);
/*!40000 ALTER TABLE `combo_platos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `combos`
--

DROP TABLE IF EXISTS `combos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `combos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` > 0),
  `imagen_url` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_combos_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `combos`
--

LOCK TABLES `combos` WRITE;
/*!40000 ALTER TABLE `combos` DISABLE KEYS */;
INSERT INTO `combos` (`id`, `nombre`, `descripcion`, `precio`, `imagen_url`, `activo`, `fecha_creacion`) VALUES (1,'Trio Marino','Ceviche de pescado + chicharrón + arroz con mariscos',50.00,'public/images/combos/combo_691fb0e309524.webp',1,'2025-11-17 04:33:23'),(2,'Duo Marino','ceviche + chicharrón (arroz con mariscos) usted elige los uno de los tres platos',35.00,'public/images/combos/combo_691fb1777cdee.jpeg',1,'2025-11-17 04:33:23'),(3,'Combo Familiar','Arroz con mariscos + ceviche + chicharrón + bebida',60.00,'public/images/combos/combo_691fb52548b9f.webp',1,'2025-11-17 04:33:23');
/*!40000 ALTER TABLE `combos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_modificacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`),
  KEY `idx_configuracion_clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configuraci├│n general del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` (`id`, `clave`, `valor`, `descripcion`, `fecha_modificacion`) VALUES (1,'nombre_restaurante','Cevichería Ñapanchita','Nombre del restaurante','2025-11-30 15:23:32'),(2,'ruc','20123456789','RUC del establecimiento','2025-11-30 15:20:04'),(3,'direccion','Sechura','Direcci├│n del local','2025-11-30 15:23:43'),(4,'telefono','987654321','Tel├®fono de contacto','2025-11-30 15:20:04'),(5,'email','contacto@napanchita.com','Email de contacto','2025-11-30 15:20:04'),(6,'logo','logo.png','Nombre del archivo del logo','2025-11-30 15:20:04'),(7,'costo_delivery','5.00','Costo de env├¡o a domicilio en soles','2025-11-30 15:20:04'),(8,'monto_minimo_delivery','20.00','Monto m├¡nimo para delivery en soles','2025-11-30 15:20:04'),(9,'tiempo_preparacion','30','Tiempo estimado de preparaci├│n en minutos','2025-11-30 15:20:04'),(10,'tiempo_max_reserva','2','Tiempo m├íximo de reserva en horas','2025-11-30 15:20:04'),(11,'anticipacion_minima_reserva','1','Anticipaci├│n m├¡nima para reservar en horas','2025-11-30 15:20:04'),(12,'igv','18','Porcentaje de IGV','2025-11-30 15:20:04'),(13,'aplicar_igv','1','Aplicar IGV: 1=S├¡, 0=No','2025-11-30 15:20:04'),(14,'modo_mantenimiento','0','Modo mantenimiento: 1=Activado, 0=Desactivado','2025-11-30 15:20:04'),(15,'zona_horaria','America/Lima','Zona horaria del sistema','2025-11-30 15:20:04'),(16,'moneda','PEN','C├│digo de moneda (PEN, USD, etc.)','2025-11-30 15:20:04'),(17,'simbolo_moneda','S/','S├¡mbolo de la moneda','2025-11-30 15:20:04');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `direccion` text NOT NULL,
  `referencia` text DEFAULT NULL,
  `zona_id` int(11) NOT NULL,
  `repartidor_id` int(11) DEFAULT NULL,
  `estado` enum('pendiente','asignado','en_camino','entregado','fallido') DEFAULT 'pendiente',
  `fecha_asignacion` timestamp NULL DEFAULT NULL,
  `fecha_entrega` timestamp NULL DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedido_id` (`pedido_id`),
  KEY `idx_deliveries_pedido` (`pedido_id`),
  KEY `idx_deliveries_repartidor` (`repartidor_id`),
  KEY `idx_deliveries_zona` (`zona_id`),
  KEY `idx_deliveries_estado` (`estado`),
  CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`zona_id`) REFERENCES `zonas_delivery` (`id`),
  CONSTRAINT `deliveries_ibfk_3` FOREIGN KEY (`repartidor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deliveries`
--

LOCK TABLES `deliveries` WRITE;
/*!40000 ALTER TABLE `deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_logs_usuario` (`usuario_id`),
  KEY `idx_logs_fecha` (`fecha`),
  KEY `idx_logs_accion` (`accion`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` (`id`, `usuario_id`, `accion`, `tabla`, `registro_id`, `detalles`, `ip`, `fecha`) VALUES (1,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 04:37:53'),(2,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 04:40:22'),(3,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-17 04:40:52'),(4,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-17 04:41:49'),(5,3,'LOGIN','usuarios',3,'Inicio de sesión exitoso','::1','2025-11-17 04:42:01'),(6,3,'LOGOUT','usuarios',3,'Cierre de sesión','::1','2025-11-17 04:42:32'),(7,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 04:42:46'),(8,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario desactivado','::1','2025-11-17 04:46:31'),(9,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario activado','::1','2025-11-17 04:46:41'),(10,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario desactivado','::1','2025-11-17 04:50:20'),(11,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 04:50:29'),(12,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 04:50:45'),(13,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 04:50:50'),(14,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario desactivado','::1','2025-11-17 04:50:54'),(15,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 04:50:57'),(16,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 04:51:00'),(17,3,'LOGIN','usuarios',3,'Inicio de sesión exitoso','::1','2025-11-17 04:51:10'),(18,3,'LOGOUT','usuarios',3,'Cierre de sesión','::1','2025-11-17 04:51:14'),(19,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 04:51:21'),(20,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario desactivado','::1','2025-11-17 04:54:45'),(21,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 04:54:49'),(22,1,'ACTUALIZAR_USUARIO','usuarios',0,'Usuario: Luis Pardes','::1','2025-11-17 04:55:34'),(23,1,'ACTUALIZAR_USUARIO','usuarios',0,'Usuario: Luis Pardes','::1','2025-11-17 04:55:43'),(24,1,'ACTUALIZAR_USUARIO','usuarios',0,'Usuario: Luis Paredes','::1','2025-11-17 04:57:35'),(25,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: Luis Paredes','::1','2025-11-17 04:58:22'),(26,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 04:58:29'),(27,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz','::1','2025-11-17 04:58:44'),(28,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 04:59:34'),(29,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz Yarleque','::1','2025-11-17 04:59:47'),(30,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 05:00:13'),(31,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz','::1','2025-11-17 05:00:23'),(32,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:00:37'),(33,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 05:00:49'),(34,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:01:42'),(35,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-17 05:01:48'),(36,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-17 05:02:13'),(37,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 05:02:18'),(38,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario desactivado','::1','2025-11-17 05:02:31'),(39,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:02:36'),(40,3,'LOGIN','usuarios',3,'Inicio de sesión exitoso','::1','2025-11-17 05:02:49'),(41,3,'LOGOUT','usuarios',3,'Cierre de sesión','::1','2025-11-17 05:03:06'),(42,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 05:03:12'),(43,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario activado','::1','2025-11-17 05:11:53'),(44,1,'ACTUALIZAR_USUARIO','usuarios',2,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:12:32'),(45,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:13:21'),(46,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-17 05:14:59'),(47,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 05:15:22'),(48,1,'ACTUALIZAR_USUARIO','usuarios',2,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:15:39'),(49,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:16:14'),(50,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-17 05:16:17'),(51,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-17 05:16:24'),(52,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-17 05:16:31'),(53,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 05:16:41'),(54,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz','::1','2025-11-17 05:17:43'),(55,1,'ACTUALIZAR_USUARIO','usuarios',2,'Usuario: Jesus Vilchez','::1','2025-11-17 05:18:18'),(56,1,'ACTUALIZAR_USUARIO','usuarios',1,'Usuario: Administrador','::1','2025-11-17 05:18:53'),(57,1,'CREAR_USUARIO','usuarios',4,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:22:41'),(58,NULL,'LOGIN','usuarios',4,'Inicio de sesión exitoso','::1','2025-11-17 05:23:10'),(59,NULL,'LOGOUT','usuarios',4,'Cierre de sesión','::1','2025-11-17 05:23:17'),(60,1,'ELIMINAR_USUARIO','usuarios',4,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:23:24'),(61,1,'ELIMINAR_USUARIO','usuarios',4,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:25:20'),(62,1,'CREAR_USUARIO','usuarios',5,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:27:51'),(63,1,'ELIMINAR_USUARIO','usuarios',5,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:28:05'),(64,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario desactivado','::1','2025-11-17 05:28:40'),(65,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario desactivado','::1','2025-11-17 05:28:51'),(66,1,'CAMBIAR_ESTADO_USUARIO','usuarios',5,'Usuario activado','::1','2025-11-17 05:30:22'),(67,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 05:30:25'),(68,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario activado','::1','2025-11-17 05:30:27'),(69,1,'CAMBIAR_ESTADO_USUARIO','usuarios',5,'Usuario desactivado','::1','2025-11-17 05:30:31'),(70,1,'ELIMINAR_USUARIO','usuarios',5,'Usuario: Dayanna Vilchez','::1','2025-11-17 05:31:09'),(71,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:53:30'),(72,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 05:55:41'),(73,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 05:56:23'),(74,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 06:02:17'),(75,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 06:03:50'),(76,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 06:05:27'),(77,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 06:12:55'),(78,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 06:13:02'),(79,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 06:16:40'),(80,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 06:16:47'),(81,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz','::1','2025-11-17 06:30:21'),(82,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario activado','::1','2025-11-17 06:30:24'),(83,1,'CAMBIAR_ESTADO_USUARIO','usuarios',3,'Usuario desactivado','::1','2025-11-17 06:32:04'),(84,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz','::1','2025-11-17 06:32:09'),(85,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 06:56:43'),(86,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 06:56:51'),(87,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 13:39:43'),(88,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz Yarleque','::1','2025-11-17 13:43:53'),(89,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-17 14:01:34'),(90,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 14:01:59'),(91,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-17 18:12:10'),(92,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-18 15:12:59'),(93,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-19 18:01:27'),(94,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz Yarlequeee','::1','2025-11-19 18:02:54'),(95,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-20 13:23:24'),(96,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-20 22:11:32'),(97,1,'ACTUALIZAR_USUARIO','usuarios',3,'Usuario: María Ruiz Yarlequeee','::1','2025-11-20 22:11:50'),(98,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario desactivado','::1','2025-11-20 22:12:07'),(99,1,'CAMBIAR_ESTADO_USUARIO','usuarios',2,'Usuario activado','::1','2025-11-20 22:12:15'),(100,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-20 23:43:23'),(101,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-21 01:19:19'),(102,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-21 01:26:28'),(103,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-21 03:14:26'),(104,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-21 05:11:49'),(105,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-21 13:58:23'),(106,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-21 16:35:42'),(107,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-21 18:04:59'),(108,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-21 18:47:07'),(109,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-24 17:31:35'),(110,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-25 16:43:05'),(111,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-27 13:21:06'),(112,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-28 16:44:01'),(113,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-30 03:34:43'),(114,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-30 03:49:00'),(115,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 03:50:07'),(116,2,'CREAR_PEDIDO','pedidos',8,'Pedido tipo: mesa, Total: S/ 30','::1','2025-11-30 03:58:23'),(117,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-30 04:03:23'),(118,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 04:03:32'),(119,2,'CREAR_PEDIDO','pedidos',9,'Pedido tipo: mesa, Total: S/ 40','::1','2025-11-30 04:04:45'),(120,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-30 04:08:22'),(121,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',8,'Nuevo estado: en_preparacion','::1','2025-11-30 04:15:14'),(122,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',8,'Nuevo estado: listo','::1','2025-11-30 04:16:31'),(123,2,'CANCELAR_PEDIDO','pedidos',9,NULL,'::1','2025-11-30 04:17:08'),(124,2,'CREAR_PEDIDO','pedidos',10,'Pedido tipo: mesa, Total: S/ 50','::1','2025-11-30 04:17:45'),(125,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',10,'Nuevo estado: en_preparacion','::1','2025-11-30 04:17:56'),(126,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',10,'Nuevo estado: listo','::1','2025-11-30 04:18:08'),(127,2,'CAMBIAR_ESTADO_PEDIDO','pedidos',10,'Nuevo estado: entregado','::1','2025-11-30 04:19:28'),(128,2,'CAMBIAR_ESTADO_PEDIDO','pedidos',8,'Nuevo estado: entregado','::1','2025-11-30 04:19:34'),(129,2,'CREAR_PEDIDO','pedidos',11,'Pedido tipo: mesa, Total: S/ 32','::1','2025-11-30 04:36:24'),(130,2,'CREAR_PEDIDO','pedidos',12,'Pedido tipo: mesa, Total: S/ 35','::1','2025-11-30 04:40:06'),(131,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',11,'Nuevo estado: en_preparacion','::1','2025-11-30 06:20:44'),(132,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',12,'Nuevo estado: en_preparacion','::1','2025-11-30 06:20:47'),(133,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',11,'Nuevo estado: listo','::1','2025-11-30 06:20:50'),(134,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',12,'Nuevo estado: listo','::1','2025-11-30 06:20:51'),(135,2,'CAMBIAR_ESTADO_PEDIDO','pedidos',12,'Nuevo estado: entregado','::1','2025-11-30 06:21:06'),(136,2,'CAMBIAR_ESTADO_PEDIDO','pedidos',11,'Nuevo estado: entregado','::1','2025-11-30 06:21:13'),(137,2,'CREAR_PEDIDO','pedidos',13,'Pedido tipo: mesa, Total: S/ 89','::1','2025-11-30 06:25:34'),(138,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',13,'Nuevo estado: en_preparacion','::1','2025-11-30 06:26:08'),(139,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',13,'Nuevo estado: listo','::1','2025-11-30 06:26:18'),(140,2,'CAMBIAR_ESTADO_PEDIDO','pedidos',13,'Nuevo estado: entregado','::1','2025-11-30 06:26:27'),(141,2,'CREAR_PEDIDO','pedidos',14,'Pedido tipo: para_llevar, Total: S/ 40','::1','2025-11-30 06:31:05'),(142,2,'CREAR_PEDIDO','pedidos',15,'Pedido tipo: para_llevar, Total: S/ 40','::1','2025-11-30 06:31:45'),(143,2,'FINALIZAR_PEDIDO','pedidos',13,NULL,'::1','2025-11-30 06:34:56'),(144,2,'CREAR_CLIENTE_RAPIDO','clientes',7,NULL,'::1','2025-11-30 06:44:14'),(145,2,'CREAR_PEDIDO','pedidos',16,'Pedido tipo: para_llevar, Total: S/ 40','::1','2025-11-30 06:49:49'),(146,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',16,'Nuevo estado: en_preparacion','::1','2025-11-30 06:50:51'),(147,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',16,'Nuevo estado: listo','::1','2025-11-30 06:51:01'),(148,1,'CAMBIAR_ESTADO_PEDIDO','pedidos',16,'Nuevo estado: entregado','::1','2025-11-30 06:51:14'),(149,1,'FINALIZAR_PEDIDO','pedidos',16,NULL,'::1','2025-11-30 06:51:21'),(150,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 14:29:12'),(151,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-30 14:33:58'),(152,2,'CREAR_PEDIDO','pedidos',17,'Pedido tipo: mesa, Total: S/ 50','::1','2025-11-30 14:36:51'),(153,2,'CREAR_PEDIDO','pedidos',18,'Pedido tipo: mesa, Total: S/ 50','::1','2025-11-30 14:55:14'),(154,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-30 14:55:47'),(155,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 14:55:56'),(156,2,'ACTUALIZAR_PERFIL','usuarios',2,NULL,'::1','2025-11-30 15:12:46'),(157,2,'CAMBIAR_PASSWORD','usuarios',2,NULL,'::1','2025-11-30 15:13:12'),(158,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-30 15:13:19'),(159,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 15:13:38'),(160,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-30 15:47:58'),(161,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 15:48:17'),(162,2,'CREAR_PEDIDO','pedidos',19,'Pedido tipo: mesa, Total: S/ 30','::1','2025-11-30 15:50:44'),(163,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 16:04:20'),(164,2,'CANCELAR_PEDIDO','pedidos',19,NULL,'::1','2025-11-30 16:38:06'),(165,2,'LOGOUT','usuarios',2,'Cierre de sesión','::1','2025-11-30 16:38:17'),(166,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-11-30 16:38:20'),(167,1,'LOGOUT','usuarios',1,'Cierre de sesión','::1','2025-11-30 16:45:56'),(168,2,'LOGIN','usuarios',2,'Inicio de sesión exitoso','::1','2025-11-30 16:46:04'),(169,1,'LOGIN','usuarios',1,'Inicio de sesión exitoso','::1','2025-12-01 15:09:06');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesas`
--

DROP TABLE IF EXISTS `mesas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) NOT NULL,
  `capacidad` int(11) NOT NULL CHECK (`capacidad` > 0),
  `estado` enum('disponible','ocupada','reservada','inactiva') DEFAULT 'disponible',
  `posicion_x` int(11) DEFAULT 0 COMMENT 'Posici??n X en layout visual',
  `posicion_y` int(11) DEFAULT 0 COMMENT 'Posici??n Y en layout visual',
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `idx_mesas_estado` (`estado`),
  KEY `idx_mesas_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesas`
--

LOCK TABLES `mesas` WRITE;
/*!40000 ALTER TABLE `mesas` DISABLE KEYS */;
INSERT INTO `mesas` (`id`, `numero`, `capacidad`, `estado`, `posicion_x`, `posicion_y`, `activo`) VALUES (1,'M01',3,'ocupada',50,50,1),(2,'M02',2,'disponible',232,54,1),(3,'M03',4,'ocupada',424,53,1),(4,'M04',5,'disponible',616,56,1),(5,'M05',4,'disponible',798,49,1),(6,'M06',4,'disponible',58,332,1),(7,'M07',6,'disponible',238,329,1),(8,'M08',6,'disponible',427,326,1),(9,'M09',8,'disponible',621,321,1),(10,'M10',8,'disponible',806,315,1);
/*!40000 ALTER TABLE `mesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodos_pago`
--

DROP TABLE IF EXISTS `metodos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metodos_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_metodos_pago_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_pago`
--

LOCK TABLES `metodos_pago` WRITE;
/*!40000 ALTER TABLE `metodos_pago` DISABLE KEYS */;
INSERT INTO `metodos_pago` (`id`, `nombre`, `descripcion`, `activo`) VALUES (1,'Efectivo','Pago en efectivo',1),(2,'Tarjeta','Tarjeta de débito o crédito',1),(3,'Yape','Pago por Yape',1);
/*!40000 ALTER TABLE `metodos_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_items`
--

DROP TABLE IF EXISTS `pedido_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `plato_id` int(11) DEFAULT NULL,
  `combo_id` int(11) DEFAULT NULL,
  `tipo` enum('producto','combo') NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Snapshot del nombre para hist??rico',
  `cantidad` int(11) NOT NULL DEFAULT 1 CHECK (`cantidad` > 0),
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `notas` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedido_items_pedido` (`pedido_id`),
  KEY `idx_pedido_items_producto` (`plato_id`),
  KEY `idx_pedido_items_combo` (`combo_id`),
  CONSTRAINT `fk_pedido_items_plato` FOREIGN KEY (`plato_id`) REFERENCES `platos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pedido_items_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedido_items_ibfk_3` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_items`
--

LOCK TABLES `pedido_items` WRITE;
/*!40000 ALTER TABLE `pedido_items` DISABLE KEYS */;
INSERT INTO `pedido_items` (`id`, `pedido_id`, `plato_id`, `combo_id`, `tipo`, `nombre`, `cantidad`, `precio_unitario`, `subtotal`, `notas`) VALUES (7,13,NULL,NULL,'','Ceviche de caballa',1,40.00,40.00,''),(8,13,NULL,NULL,'','Chicharrón Mixto',1,35.00,35.00,''),(9,13,NULL,NULL,'','Inka Kola 1.5L',2,7.00,14.00,''),(12,16,NULL,NULL,'','Ceviche de caballa',1,40.00,40.00,''),(13,17,NULL,NULL,'','Leche de Tigre',1,15.00,15.00,''),(14,17,NULL,NULL,'','Chicharrón Mixto',1,35.00,35.00,''),(15,18,NULL,3,'combo','Combo Familiar',1,60.00,60.00,''),(16,19,NULL,NULL,'','Ceviche Mixto',1,30.00,30.00,'');
/*!40000 ALTER TABLE `pedido_items` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_calcular_total_pedido` AFTER INSERT ON `pedido_items` FOR EACH ROW BEGIN
    UPDATE pedidos 
    SET subtotal = (
        SELECT IFNULL(SUM(subtotal), 0) 
        FROM pedido_items 
        WHERE pedido_id = NEW.pedido_id
    ),
    total = subtotal + costo_envio - descuento
    WHERE id = NEW.pedido_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `mesa_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'Usuario que registr?? el pedido',
  `tipo` enum('mesa','delivery','para_llevar') NOT NULL,
  `estado` enum('pendiente','en_preparacion','listo','entregado','finalizado','cancelado') DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pedidos_cliente` (`cliente_id`),
  KEY `idx_pedidos_mesa` (`mesa_id`),
  KEY `idx_pedidos_usuario` (`usuario_id`),
  KEY `idx_pedidos_tipo` (`tipo`),
  KEY `idx_pedidos_estado` (`estado`),
  KEY `idx_pedidos_fecha` (`fecha_pedido`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` (`id`, `cliente_id`, `mesa_id`, `usuario_id`, `tipo`, `estado`, `subtotal`, `costo_envio`, `descuento`, `total`, `notas`, `fecha_pedido`, `fecha_actualizacion`) VALUES (13,NULL,1,2,'mesa','finalizado',89.00,0.00,0.00,89.00,'El ceviche con poco ají','2025-11-30 06:25:34','2025-11-30 06:34:56'),(16,4,NULL,2,'para_llevar','finalizado',40.00,0.00,0.00,40.00,NULL,'2025-11-30 06:49:49','2025-11-30 06:51:21'),(17,NULL,3,2,'mesa','pendiente',50.00,0.00,0.00,50.00,NULL,'2025-11-30 14:36:51','2025-11-30 14:36:51'),(18,NULL,1,2,'mesa','pendiente',60.00,0.00,10.00,50.00,NULL,'2025-11-30 14:55:14','2025-11-30 14:55:14'),(19,NULL,9,2,'mesa','cancelado',30.00,0.00,0.00,30.00,NULL,'2025-11-30 15:50:44','2025-11-30 16:38:06');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_actualizar_mesa_pedido` AFTER INSERT ON `pedidos` FOR EACH ROW BEGIN
    IF NEW.tipo = 'mesa' AND NEW.mesa_id IS NOT NULL THEN
        UPDATE mesas 
        SET estado = 'ocupada' 
        WHERE id = NEW.mesa_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_liberar_mesa_pedido
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    
    
    IF NEW.tipo = 'mesa' AND NEW.estado IN ('finalizado', 'cancelado')
       AND OLD.estado NOT IN ('finalizado', 'cancelado') THEN
        UPDATE mesas
        SET estado = 'disponible'
        WHERE id = NEW.mesa_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `platos`
--

DROP TABLE IF EXISTS `platos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` > 0),
  `imagen_url` varchar(255) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_productos_categoria` (`categoria_id`),
  KEY `idx_productos_disponible` (`disponible`),
  KEY `idx_productos_nombre` (`nombre`),
  CONSTRAINT `platos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platos`
--

LOCK TABLES `platos` WRITE;
/*!40000 ALTER TABLE `platos` DISABLE KEYS */;
INSERT INTO `platos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `imagen_url`, `disponible`, `fecha_creacion`) VALUES (1,1,'Ceviche de Filete','Ceviche clásico de pescado fileteado',25.00,'public/images/platos/prod_691b33b54916b.jpeg',0,'2025-11-17 04:33:23'),(2,1,'Ceviche Mixto','Ceviche con pescado, pulpo y calamares',30.00,'public/images/platos/prod_691b35e184fdf.jpeg',1,'2025-11-17 04:33:23'),(3,1,'Ceviche de caballa','Exquisito ceviche de caballa cordelera.',40.00,'public/images/platos/prod_691b355f32c89.jpeg',1,'2025-11-17 04:33:23'),(4,1,'Leche de Tigre','Ceviche licuado bien concentrado',15.00,'public/images/platos/prod_691b33d897064.jpeg',1,'2025-11-17 04:33:23'),(5,2,'Chicharrón de Pescado','Pescado frito crujiente',28.00,'public/images/platos/prod_691b33ebba53c.jpeg',0,'2025-11-17 04:33:23'),(6,4,'Arroz con Mariscos','exquisito plato de mariscos',32.00,'public/images/platos/prod_691b3467a4d9d.jpeg',1,'2025-11-17 04:33:23'),(7,2,'Chicharrón Mixto','Mix de mariscos fritos',35.00,'public/images/platos/prod_691b38d16fb5a.jpeg',1,'2025-11-17 04:33:23'),(9,3,'Sudado de Pescado','Para compartir (3-4 personas)',80.00,'public/images/platos/prod_691b3a3ac5463.webp',1,'2025-11-17 04:33:23'),(10,4,'Arroz Chaufa','Arroz con variedad de mariscos',28.00,'public/images/platos/prod_691b3697149ee.webp',1,'2025-11-17 04:33:23'),(12,5,'Chicha Morada 1L','Bebida tradicional peruana',8.00,'public/images/platos/prod_691b37f1a7e34.jpg',1,'2025-11-17 04:33:23'),(13,5,'Inka Kola 1.5L','Gaseosa nacional',7.00,'public/images/platos/prod_691b3c146f1ab.png',1,'2025-11-17 04:33:23'),(14,5,'Cerveza Cristal','Cerveza nacional',8.00,'public/images/platos/prod_691b3bb13a78f.jpg',0,'2025-11-17 04:33:23'),(17,4,'Ajicito de Junta','ajicito tradicional de Sechura',15.00,'public/images/platos/prod_691f9353d3f98.jpg',1,'2025-11-17 04:33:23'),(18,6,'Tequeños','Tequeños de queso (6 unid.)',18.00,'public/images/platos/prod_691b3cdad1f99.webp',0,'2025-11-17 04:33:23'),(21,9,'Chifles','Chifles',5.00,'public/images/platos/prod_691f931e627db.jpg',0,'2025-11-17 07:11:40');
/*!40000 ALTER TABLE `platos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `mesa_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `personas` int(11) NOT NULL CHECK (`personas` > 0),
  `estado` enum('pendiente','confirmada','completada','cancelada','no_show') DEFAULT 'pendiente',
  `codigo_confirmacion` varchar(20) NOT NULL,
  `notas` text DEFAULT NULL,
  `creado_por_usuario_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_confirmacion` (`codigo_confirmacion`),
  KEY `creado_por_usuario_id` (`creado_por_usuario_id`),
  KEY `idx_reservas_cliente` (`cliente_id`),
  KEY `idx_reservas_mesa` (`mesa_id`),
  KEY `idx_reservas_fecha` (`fecha`,`hora`),
  KEY `idx_reservas_estado` (`estado`),
  KEY `idx_reservas_codigo` (`codigo_confirmacion`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`),
  CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`creado_por_usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` (`id`, `cliente_id`, `mesa_id`, `fecha`, `hora`, `personas`, `estado`, `codigo_confirmacion`, `notas`, `creado_por_usuario_id`, `fecha_creacion`, `fecha_actualizacion`) VALUES (6,5,3,'2025-11-30','09:00:00',4,'completada','RES-CD05D7','',2,'2025-11-30 07:25:16','2025-11-30 14:34:38'),(7,7,1,'2025-11-30','08:00:00',2,'cancelada','RES-32BB65','',2,'2025-11-30 07:25:39','2025-11-30 07:25:54'),(8,7,10,'2025-11-30','08:00:00',8,'no_show','RES-8828B7','',2,'2025-11-30 07:31:36','2025-11-30 14:34:15'),(9,6,1,'2025-11-30','09:50:00',3,'completada','RES-9ABFE9','Motivo de cumpleaños',2,'2025-11-30 14:42:17','2025-11-30 14:54:44'),(10,3,2,'2025-11-30','09:50:00',2,'no_show','RES-7252D5','',2,'2025-11-30 14:43:35','2025-11-30 14:52:42'),(11,5,8,'2025-12-01','16:00:00',5,'pendiente','RES-5C9700','',1,'2025-12-01 15:09:57','2025-12-01 15:10:08');
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('admin','mesero','repartidor') NOT NULL DEFAULT 'mesero',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuarios_email` (`email`),
  KEY `idx_usuarios_rol` (`rol`),
  KEY `idx_usuarios_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Personal con acceso al sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `telefono`, `rol`, `fecha_registro`, `activo`) VALUES (1,'Administrador','admin@napanchita.com','$2y$10$fl1w.z2i69t9pJDiCLagNeDXvdlvLWIoKb7X3xGPio4p/2FuqggU.','970335519','admin','2025-11-17 04:33:23',1),(2,'Jesus Prieto','jesus@napanchita.com','$2y$10$AZxtxnMTfIr0hG1uKo2uEO2VXlWL21OB0uQ78LdZCpFPF812Scrsq','918782390','mesero','2025-11-17 04:33:23',1),(3,'María Ruiz Yarlequeee','maria@napanchita.com','$2y$10$2EF9vSTn.pHM2ILhg84Cy.w0MHkRC6WNwjgXM.mq8Y4RRhXGBZFn.','936626757','repartidor','2025-11-17 04:33:23',0);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `metodo_pago_id` int(11) NOT NULL,
  `monto_recibido` decimal(10,2) NOT NULL,
  `monto_cambio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `descuento_aplicado` decimal(10,2) DEFAULT 0.00,
  `codigo_descuento` varchar(50) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'Cajero que registr??',
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp(),
  `ticket_generado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedido_id` (`pedido_id`),
  KEY `idx_ventas_pedido` (`pedido_id`),
  KEY `idx_ventas_fecha` (`fecha_venta`),
  KEY `idx_ventas_metodo_pago` (`metodo_pago_id`),
  KEY `idx_ventas_usuario` (`usuario_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`),
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` (`id`, `pedido_id`, `metodo_pago_id`, `monto_recibido`, `monto_cambio`, `total`, `descuento_aplicado`, `codigo_descuento`, `usuario_id`, `fecha_venta`, `ticket_generado`) VALUES (1,13,1,100.00,11.00,89.00,0.00,NULL,2,'2025-11-30 06:34:56',0),(2,16,3,40.00,0.00,40.00,0.00,NULL,1,'2025-11-30 06:51:21',0);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zonas_delivery`
--

DROP TABLE IF EXISTS `zonas_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zonas_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tiempo_estimado` int(11) DEFAULT NULL COMMENT 'Tiempo en minutos',
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_zonas_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zonas_delivery`
--

LOCK TABLES `zonas_delivery` WRITE;
/*!40000 ALTER TABLE `zonas_delivery` DISABLE KEYS */;
INSERT INTO `zonas_delivery` (`id`, `nombre`, `descripcion`, `costo_envio`, `tiempo_estimado`, `activo`) VALUES (1,'Centro','Centro de la ciudad',5.00,30,1),(2,'Norte','Zona norte',8.00,45,1),(3,'Sur','Zona sur',8.00,45,1),(4,'Este','Zona este',10.00,60,1),(5,'Oeste','Zona oeste',10.00,60,1);
/*!40000 ALTER TABLE `zonas_delivery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'napanchita_db'
--

--
-- Dumping routines for database 'napanchita_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 10:17:39
