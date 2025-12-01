# Instrucciones de Importación - Base de Datos Napanchita

## 📋 Requisitos Previos
- XAMPP instalado (Apache + MySQL/MariaDB)
- Puerto MySQL: 3306 (por defecto)
- Usuario: root (sin contraseña por defecto)

## 🚀 Pasos para Importar en un Nuevo Dispositivo

### Método 1: Importar desde phpMyAdmin (Recomendado)

1. **Iniciar XAMPP**
   - Abre el panel de control de XAMPP
   - Inicia Apache y MySQL

2. **Acceder a phpMyAdmin**
   - Abre tu navegador y ve a: `http://localhost/phpmyadmin`

3. **Crear la Base de Datos**
   - Click en "Nueva" en el panel izquierdo
   - Nombre de la base de datos: `napanchita_db`
   - Cotejamiento: `utf8mb4_general_ci`
   - Click en "Crear"

4. **Importar el Script**
   - Selecciona la base de datos `napanchita_db`
   - Click en la pestaña "Importar"
   - Click en "Seleccionar archivo"
   - Selecciona: `napanchita_db_full_backup.sql`
   - Click en "Continuar"
   - Espera a que termine la importación

### Método 2: Importar desde Línea de Comandos

1. **Abrir Terminal/CMD**
   - Windows: Presiona Win+R, escribe `cmd` y Enter
   - Navega a la carpeta de XAMPP: `cd C:\xampp\mysql\bin`

2. **Crear la Base de Datos**
   ```bash
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
   ```

3. **Importar el Archivo SQL**
   ```bash
   mysql -u root napanchita_db < C:\xampp\htdocs\napanchita-web\database\napanchita_db_full_backup.sql
   ```

4. **Verificar la Importación**
   ```bash
   mysql -u root -e "USE napanchita_db; SHOW TABLES;"
   ```

### Método 3: Importar Automáticamente con Script Batch (Windows)

1. **Crear archivo** `importar_db.bat` en la carpeta del proyecto:
   ```batch
   @echo off
   echo ========================================
   echo   IMPORTACION BASE DE DATOS NAPANCHITA
   echo ========================================
   echo.
   
   cd /d C:\xampp\mysql\bin
   
   echo Creando base de datos...
   mysql -u root -e "DROP DATABASE IF EXISTS napanchita_db;"
   mysql -u root -e "CREATE DATABASE napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
   
   echo Importando datos...
   mysql -u root napanchita_db < C:\xampp\htdocs\napanchita-web\database\napanchita_db_full_backup.sql
   
   echo.
   echo ========================================
   echo   IMPORTACION COMPLETADA
   echo ========================================
   echo.
   pause
   ```

2. **Ejecutar el archivo** `importar_db.bat` con doble click

## 🔧 Configuración de la Aplicación

Después de importar la base de datos, verifica el archivo de configuración:

**Archivo:** `config/database.php`

```php
<?php
class Database {
    private $host = "localhost";
    private $db_name = "napanchita_db";
    private $username = "root";
    private $password = "";  // Vacío por defecto en XAMPP
    private $charset = "utf8mb4";
    public $conn;
    
    public function getConnection() {
        // ... código de conexión
    }
}
```

## 📊 Contenido de la Base de Datos

El backup incluye:

### Tablas Principales
- ✅ `categorias` - Categorías de productos
- ✅ `clientes` - Clientes del restaurante
- ✅ `platos` - Menú de platos
- ✅ `combos` - Combos especiales
- ✅ `mesas` - Mesas del restaurante
- ✅ `pedidos` - Pedidos realizados
- ✅ `pedido_items` - Items de cada pedido
- ✅ `reservas` - Reservas de mesas
- ✅ `usuarios` - Usuarios del sistema
- ✅ `ventas` - Registro de ventas
- ✅ `metodos_pago` - Métodos de pago
- ✅ `configuracion` - Configuración del sistema
- ✅ `deliveries` - Entregas a domicilio
- ✅ `zonas_delivery` - Zonas de reparto
- ✅ `cierres_caja` - Cierres de caja
- ✅ `logs` - Logs del sistema

### Datos Iniciales

**Usuario Administrador:**
- Usuario: `admin`
- Contraseña: `admin123`

**Usuario Mesero:**
- Usuario: `mesero1`
- Contraseña: `mesero123`

**Usuario Repartidor:**
- Usuario: `repartidor1`
- Contraseña: `repartidor123`

## ⚠️ Solución de Problemas

### Error: "Access denied for user 'root'"
- Verifica que MySQL esté corriendo en XAMPP
- Asegúrate de que el usuario sea `root` sin contraseña
- Si tienes contraseña configurada, agrégala al comando: `mysql -u root -p`

### Error: "Database already exists"
- Elimina la base de datos existente primero:
  ```bash
  mysql -u root -e "DROP DATABASE napanchita_db;"
  ```

### Error: "Unknown database 'napanchita_db'"
- Crea la base de datos primero antes de importar

### Tablas vacías después de importar
- Verifica que el archivo `napanchita_db_full_backup.sql` no esté corrupto
- Revisa el tamaño del archivo (debe ser aprox. 50KB o más)
- Intenta importar nuevamente desde phpMyAdmin

## 📝 Notas Importantes

1. **Backup Regular**: Se recomienda hacer backups periódicos de la base de datos
2. **Contraseñas**: Cambia las contraseñas por defecto después de la instalación
3. **Permisos**: Asegúrate de que la carpeta `public/images/` tenga permisos de escritura
4. **Timezone**: Verifica la zona horaria en `php.ini`: `date.timezone = America/Lima`

## 🔄 Crear Nuevo Backup

Para crear un nuevo backup actualizado:

```bash
cd C:\xampp\mysql\bin
mysqldump -u root --complete-insert --routines --triggers --events --skip-lock-tables --ignore-table=napanchita_db.v_productos_top --ignore-table=napanchita_db.v_pedidos_completos --ignore-table=napanchita_db.v_ventas_diarias napanchita_db > backup.sql
```

---

**Sistema Napanchita v1.0**  
Fecha del backup: Diciembre 2025
