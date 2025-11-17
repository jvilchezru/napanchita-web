# 🚀 INSTALACIÓN RÁPIDA - Sistema Napanchita

## Requisitos del Sistema

- **XAMPP** con:
  - Apache 2.4+
  - PHP 8.0+ (mínimo PHP 7.4)
  - MySQL 8.0+ (o MariaDB 10.4+)
- Navegador web moderno (Chrome, Firefox, Edge)
- Editor de código (recomendado: VS Code)

---

## Pasos de Instalación

### 1️⃣ Copiar Archivos

Copiar la carpeta `napanchita-web` en:

```
C:\xampp\htdocs\napanchita-web
```

### 2️⃣ Iniciar XAMPP

1. Abrir **XAMPP Control Panel**
2. Iniciar **Apache**
3. Iniciar **MySQL**

### 3️⃣ Crear Base de Datos

**Opción A: Desde phpMyAdmin (Recomendado para principiantes)**

1. Abrir navegador: `http://localhost/phpmyadmin`
2. Click en pestaña **SQL**
3. Abrir el archivo: `database/schema_completo.sql`
4. Copiar TODO el contenido
5. Pegar en el área de texto
6. Click en **Continuar**
7. Esperar a que termine (puede tomar 10-20 segundos)

**Opción B: Desde línea de comandos (Más rápido)**

```bash
# Abrir terminal en la carpeta del proyecto
cd C:\xampp\htdocs\napanchita-web

# Ejecutar script SQL
C:\xampp\mysql\bin\mysql -u root -p < database\schema_completo.sql

# Cuando pida contraseña, presionar ENTER (XAMPP no tiene contraseña por defecto)
```

### 4️⃣ Verificar Configuración

Editar el archivo: `config/config.php`

```php
// Línea 10 - Verificar que la URL sea correcta
define('BASE_URL', 'http://localhost/napanchita-web/');

// Línea 20 - Modo de desarrollo (cambiar a 'production' en servidor real)
define('ENVIRONMENT', 'development');
```

Editar el archivo: `config/database.php`

```php
// Líneas 10-13 - Verificar credenciales de base de datos
private $host = "localhost";
private $db_name = "napanchita";
private $username = "root";
private $password = "";  // En XAMPP por defecto está vacío
```

### 5️⃣ Probar Conexión

Abrir en navegador: `http://localhost/napanchita-web/test_conexion.php`

**Resultado esperado:** ✅ "Conexión exitosa a la base de datos"

### 6️⃣ Acceder al Sistema

**URL principal:** `http://localhost/napanchita-web/`

**Credenciales de prueba:**

| Rol               | Email                     | Contraseña  |
| ----------------- | ------------------------- | ----------- |
| **Administrador** | admin@napanchita.com      | password123 |
| **Mesero**        | mesero@napanchita.com     | password123 |
| **Repartidor**    | repartidor@napanchita.com | password123 |

---

## ✅ Verificación de Instalación

### Checklist de Pruebas

- [ ] Servidor Apache corriendo (http://localhost muestra XAMPP)
- [ ] MySQL corriendo (puede conectarse a phpMyAdmin)
- [ ] Base de datos `napanchita` existe (ver en phpMyAdmin)
- [ ] Hay 16 tablas creadas (usuarios, clientes, productos, etc.)
- [ ] test_conexion.php muestra "Conexión exitosa"
- [ ] Página de login carga correctamente
- [ ] Login con admin@napanchita.com funciona
- [ ] Dashboard de administrador se muestra
- [ ] Sidebar tiene todas las opciones
- [ ] Gestión de Usuarios abre correctamente

### Posibles Errores y Soluciones

#### ❌ "Call to undefined function password_hash()"

**Causa:** PHP muy antiguo  
**Solución:** Actualizar XAMPP a versión con PHP 7.4+

#### ❌ "Access denied for user 'root'@'localhost'"

**Causa:** Contraseña de MySQL incorrecta  
**Solución:**

1. Verificar contraseña en phpMyAdmin
2. Actualizar `config/database.php` con la contraseña correcta

#### ❌ "Table 'napanchita.usuarios' doesn't exist"

**Causa:** Schema SQL no se ejecutó correctamente  
**Solución:** Repetir paso 3 de instalación

#### ❌ "Cannot modify header information - headers already sent"

**Causa:** Espacios en blanco antes de `<?php`  
**Solución:** Verificar que no haya espacios al inicio de archivos PHP

#### ❌ Error 404 en assets (CSS/JS no cargan)

**Causa:** BASE_URL incorrecta  
**Solución:** Verificar `config/config.php` línea 10

#### ❌ "Session not working"

**Causa:** Permisos de carpeta temporal  
**Solución:**

```bash
# En Windows, verificar que existe:
C:\xampp\tmp
```

---

## 🔧 Configuración Adicional (Opcional)

### Cambiar Puerto de Apache

Si el puerto 80 está ocupado:

1. Editar: `C:\xampp\apache\conf\httpd.conf`
2. Buscar: `Listen 80`
3. Cambiar a: `Listen 8080`
4. Reiniciar Apache
5. Acceder con: `http://localhost:8080/napanchita-web/`

### Habilitar Logs de Errores PHP

Editar: `C:\xampp\php\php.ini`

```ini
display_errors = On
error_reporting = E_ALL
log_errors = On
error_log = "C:/xampp/php/logs/php_error_log.txt"
```

Reiniciar Apache después de cambios.

### Configurar Zona Horaria

En `php.ini` buscar:

```ini
[Date]
date.timezone = America/Lima
```

### Aumentar Límites de Upload

Para permitir imágenes más grandes:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

---

## 📚 Documentación del Proyecto

| Archivo                    | Descripción                      |
| -------------------------- | -------------------------------- |
| `README.md`                | Información general del proyecto |
| `PLAN_DE_TRABAJO.md`       | Plan completo de 6 sprints       |
| `PRUEBAS_SPRINT1.md`       | Guía de pruebas detallada        |
| `SPRINT1_COMPLETADO.md`    | Resumen de lo implementado       |
| `diagramas/`               | Todos los diagramas técnicos     |
| `DOCUMENTACION_TECNICA.md` | Documentación técnica completa   |

---

## 🎓 Para Desarrollo (Estudiantes)

### Estructura de Carpetas

```
napanchita-web/
├── config/          # Configuración del sistema
├── models/          # Modelos de datos (Active Record)
├── controllers/     # Lógica de negocio
├── views/           # Presentación (HTML + PHP)
├── public/          # Assets públicos (CSS, JS, imágenes)
├── database/        # Scripts SQL
└── index.php        # Front Controller (punto de entrada)
```

### Patrón MVC Implementado

**Flujo de ejecución:**

1. Usuario accede: `index.php?action=usuarios`
2. `index.php` (Front Controller) recibe request
3. Carga controlador: `UsuarioController`
4. Controlador usa modelo: `Usuario`
5. Modelo consulta base de datos
6. Controlador pasa datos a vista: `views/usuarios/index.php`
7. Vista renderiza HTML y envía al navegador

### Agregar Nueva Funcionalidad

**Ejemplo: Crear módulo de Categorías**

1. **Crear Modelo:** `models/Categoria.php`

```php
<?php
class Categoria {
    public function listar() {
        // Código para listar categorías
    }
}
```

2. **Crear Controlador:** `controllers/CategoriaController.php`

```php
<?php
class CategoriaController {
    public function index() {
        $modelo = new Categoria();
        $categorias = $modelo->listar();
        include 'views/categorias/index.php';
    }
}
```

3. **Agregar Ruta en index.php:**

```php
case 'categorias':
    require_once 'controllers/CategoriaController.php';
    $controller = new CategoriaController();
    $controller->index();
    break;
```

4. **Crear Vista:** `views/categorias/index.php`

---

## 🐛 Debugging

### Ver Errores PHP

1. Activar modo development en `config/config.php`
2. Ver logs en: `C:\xampp\php\logs\php_error_log.txt`

### Ver Consultas SQL

En `models/Usuario.php` (u otro modelo), agregar después de ejecutar query:

```php
$stmt->execute();
var_dump($stmt->errorInfo());  // Muestra errores SQL
```

### Ver Variables de Sesión

Agregar al final de cualquier archivo:

```php
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

---

## 📱 Contacto y Soporte

Para dudas sobre el código:

1. Revisar `DOCUMENTACION_TECNICA.md`
2. Revisar comentarios en el código fuente
3. Consultar diagramas en carpeta `diagramas/`

---

## 🎯 Siguientes Pasos

Una vez que todo funcione:

1. ✅ Familiarizarse con la estructura MVC
2. ✅ Probar todas las funcionalidades del Sprint 1
3. ✅ Revisar el código de los modelos y controladores
4. ✅ Leer el plan de trabajo para siguiente sprint
5. ✅ Preparar ambiente para Sprint 2 (Productos)

---

**¡Listo para desarrollar! 🚀**

Sistema Napanchita v1.0 - Sprint 1 Completado
