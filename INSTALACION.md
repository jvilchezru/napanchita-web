# INSTRUCCIONES DE INSTALACIÓN RÁPIDA

## Paso 1: Crear la Base de Datos

Abre tu gestor de MySQL (phpMyAdmin, MySQL Workbench, etc.) y ejecuta:

```sql
CREATE DATABASE napanchita_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego importa el archivo: `database/schema.sql`

O ejecuta todo el contenido del archivo schema.sql en tu gestor de MySQL.

## Paso 2: Verificar Configuración de PHP

Asegúrate de tener habilitadas estas extensiones en tu php.ini:
- extension=pdo_mysql
- extension=mysqli

## Paso 3: Iniciar el Servidor

### Opción Rápida (Servidor PHP integrado):

Abre PowerShell o CMD en la carpeta del proyecto y ejecuta:

```powershell
php -S localhost:8000
```

Luego abre tu navegador en: http://localhost:8000

### Opción XAMPP/WAMP:

1. Copia la carpeta `napanchita` a `C:\xampp\htdocs\`
2. Inicia Apache y MySQL desde el panel de XAMPP
3. Abre: http://localhost/napanchita

## Credenciales de Prueba

**Administrador:**
- Email: admin@napanchita.com
- Password: password

**Cliente:**
- Email: juan@email.com
- Password: password

## Solución de Problemas Comunes

### Error: Connection refused
- Verifica que MySQL esté ejecutándose
- Verifica las credenciales en `config/database.php`

### Error: 404 Not Found
- Si usas Apache, verifica que mod_rewrite esté habilitado
- Verifica la ruta en el archivo .htaccess

### Estilos no se cargan
- Verifica que la carpeta `public` tenga permisos de lectura
- Verifica la ruta en el navegador (F12 > Network)

### El carrito no se guarda
- Habilita localStorage en tu navegador
- Verifica la consola del navegador (F12) por errores

## Estructura de URLs

- Página principal: `http://localhost:8000/`
- Login: `http://localhost:8000/index.php?action=login`
- Registro: `http://localhost:8000/index.php?action=registro`
- Dashboard: `http://localhost:8000/index.php?action=dashboard`

¡Listo! Ya puedes usar el sistema Napanchita 🍽️
