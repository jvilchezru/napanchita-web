# 📁 Database - Sistema Napanchita

Esta carpeta contiene todos los archivos relacionados con la base de datos del sistema.

## 📄 Archivos Disponibles

### 🔹 `napanchita_db_full_backup.sql`
**Backup completo de la base de datos** con todos los datos actuales del sistema.
- Incluye: Estructura de tablas, datos, triggers, y stored procedures
- Tamaño aproximado: 50KB+
- Fecha: Diciembre 2025

### 🔹 `importar_db.bat` 
**Script automático de importación** para Windows.
- Elimina la base de datos anterior
- Crea una nueva base de datos
- Importa todos los datos
- Verifica la importación

**Uso:** Doble click en el archivo o ejecutar desde CMD

### 🔹 `INSTRUCCIONES_IMPORTACION.md`
**Documentación completa** con instrucciones detalladas de importación.
- 3 métodos diferentes de importación
- Solución de problemas comunes
- Configuración de la aplicación
- Credenciales por defecto

### 🔹 `schema_completo.sql` y `schema_ejecutable.sql`
Scripts antiguos del esquema de la base de datos (pueden estar desactualizados).

## 🚀 Inicio Rápido

### Para importar en un nuevo dispositivo:

**Opción 1 - Más Fácil (Windows):**
```bash
1. Asegúrate de que XAMPP esté corriendo
2. Doble click en importar_db.bat
3. Espera a que termine
4. Accede a: http://localhost/napanchita-web
```

**Opción 2 - phpMyAdmin:**
```bash
1. Abre http://localhost/phpmyadmin
2. Crea base de datos: napanchita_db
3. Importa el archivo: napanchita_db_full_backup.sql
```

**Opción 3 - Línea de Comandos:**
```bash
cd C:\xampp\mysql\bin
mysql -u root -e "CREATE DATABASE napanchita_db;"
mysql -u root napanchita_db < ruta\napanchita_db_full_backup.sql
```

## 🔐 Credenciales por Defecto

**Administrador:**
- Usuario: `admin`
- Contraseña: `admin123`

**Mesero:**
- Usuario: `mesero1`
- Contraseña: `mesero123`

**Repartidor:**
- Usuario: `repartidor1`
- Contraseña: `repartidor123`

⚠️ **IMPORTANTE:** Cambia estas contraseñas después del primer acceso.

## 📊 Contenido de la Base de Datos

- ✅ 20+ tablas del sistema
- ✅ Datos de configuración inicial
- ✅ Usuarios de prueba
- ✅ Categorías de productos
- ✅ Métodos de pago
- ✅ Triggers y procedimientos almacenados

## 🔄 Crear Nuevo Backup

Si necesitas actualizar el backup con datos nuevos:

```bash
cd C:\xampp\mysql\bin
mysqldump -u root --complete-insert --routines --triggers --events --skip-lock-tables ^
--ignore-table=napanchita_db.v_productos_top ^
--ignore-table=napanchita_db.v_pedidos_completos ^
--ignore-table=napanchita_db.v_ventas_diarias ^
napanchita_db > backup_nuevo.sql
```

## ⚠️ Problemas Comunes

### MySQL no se conecta
- Verifica que XAMPP esté corriendo
- Comprueba que el puerto 3306 esté disponible
- Revisa las credenciales en `config/database.php`

### Importación falla
- Asegúrate de tener permisos de administrador
- Verifica que el archivo SQL no esté corrupto
- Intenta desde phpMyAdmin si el batch falla

### Tablas vacías
- Reimporta desde cero
- Verifica que el archivo de backup tenga datos (debe ser >50KB)

## 📝 Notas

- Los backups NO incluyen las vistas `v_productos_top`, `v_pedidos_completos` y `v_ventas_diarias` debido a problemas de dependencias
- Asegúrate de hacer backups regulares de tus datos
- La base de datos usa charset `utf8mb4` para soporte completo de caracteres especiales

---

**Sistema Napanchita v1.0**  
Para más información, consulta `INSTRUCCIONES_IMPORTACION.md`
