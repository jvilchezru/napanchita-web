# 🔧 DOCUMENTACIÓN TÉCNICA - NAPANCHITA

## Arquitectura del Sistema

### Patrón MVC Implementado

```
Request → index.php (Router) → Controller → Model → Database
                                     ↓
                                   View → Response
```

---

## 📂 Detalle de Componentes

### 1. MODELOS (models/)

#### Usuario.php
```php
Métodos principales:
- crear()           // Registrar nuevo usuario
- login()           // Autenticar usuario
- obtenerPorId()    // Obtener datos de usuario
- listar()          // Listar todos los usuarios
- actualizar()      // Actualizar datos
```

#### Producto.php
```php
Métodos principales:
- listar()          // Productos disponibles
- obtenerPorId()    // Detalle de producto
- crear()           // Agregar producto
- actualizar()      // Modificar producto
- buscar()          // Buscar por término
```

#### Pedido.php
```php
Métodos principales:
- crear()           // Crear pedido + detalles
- listarPorUsuario()// Pedidos del cliente
- listarTodos()     // Todos los pedidos (admin)
- obtenerDetalles() // Items del pedido
- actualizarEstado()// Cambiar estado
```

---

### 2. CONTROLADORES (controllers/)

#### AuthController.php
```php
Responsabilidades:
- Gestión de sesiones
- Login y logout
- Registro de usuarios
- Verificación de permisos
- Protección de rutas

Métodos estáticos:
- verificarSesion()
- verificarAdmin()
```

#### ProductoController.php
```php
Responsabilidades:
- CRUD de productos
- API JSON para frontend
- Búsqueda de productos
- Validación de permisos admin

Endpoints:
- api_productos (GET)
- api_producto?id=X (GET)
- api_buscar_producto?q=X (GET)
- api_crear_producto (POST)
- api_actualizar_producto (POST)
```

#### PedidoController.php
```php
Responsabilidades:
- Creación de pedidos
- Consulta de pedidos
- Actualización de estados
- Validación de permisos

Endpoints:
- api_crear_pedido (POST JSON)
- api_mis_pedidos (GET)
- api_todos_pedidos (GET)
- api_detalle_pedido?id=X (GET)
- api_actualizar_estado (POST)
```

---

### 3. VISTAS (views/)

#### home.php
```html
Secciones:
- Header con navegación
- Hero section
- Features (características)
- Menú preview (dinámico)
- Sobre nosotros
- Contacto
- Footer

JavaScript: main.js
```

#### login.php
```html
Elementos:
- Formulario de login
- Validación frontend
- Link a registro
- Credenciales demo

JavaScript: auth.js
```

#### registro.php
```html
Elementos:
- Formulario completo
- Validación de passwords
- Confirmación de contraseña

JavaScript: auth.js
```

#### dashboard.php
```html
Secciones:
- Header con usuario
- Sidebar navegación
- Múltiples secciones:
  * Menú de productos
  * Carrito
  * Mis pedidos
  * Admin: Gestión pedidos
  * Admin: Gestión productos
- Modal de finalizar pedido

JavaScript: dashboard.js
```

---

### 4. FRONTEND (public/)

#### CSS (style.css)
```css
Organización:
1. Variables CSS
2. Reset y base
3. Header y navegación
4. Hero section
5. Características
6. Menú y productos
7. Autenticación
8. Dashboard
9. Carrito y pedidos
10. Modal
11. Utilidades
12. Animaciones
13. Media queries responsive

Total: ~1100 líneas
```

#### JavaScript (main.js)
```javascript
Funcionalidades:
- Navegación móvil
- Smooth scroll
- Animaciones on scroll
- Cargar productos preview
- Header sticky effects

Dependencias: Ninguna (Vanilla JS)
```

#### JavaScript (auth.js)
```javascript
Funcionalidades:
- Manejo de formularios
- Validaciones
- Peticiones AJAX
- Mensajes de error/éxito

Métodos:
- mostrarError()
- mostrarExito()
```

#### JavaScript (dashboard.js)
```javascript
Funcionalidades:
- Gestión del carrito
- CRUD de pedidos
- Navegación secciones
- Búsqueda productos
- Actualización estados
- Modales

Variables globales:
- carrito (Array)
- productos (Array)

Métodos principales:
- cargarProductos()
- agregarAlCarrito()
- realizarPedido()
- cargarMisPedidos()
- actualizarEstadoPedido()
```

---

## 🗄️ Estructura de Base de Datos

### Tabla: usuarios
```sql
Campos:
- id (PK, AUTO_INCREMENT)
- nombre (VARCHAR 100)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255, HASHED)
- telefono (VARCHAR 20)
- direccion (TEXT)
- rol (ENUM: admin, cliente)
- fecha_registro (TIMESTAMP)
- activo (BOOLEAN)

Índices:
- PRIMARY KEY (id)
- UNIQUE KEY (email)
```

### Tabla: categorias
```sql
Campos:
- id (PK, AUTO_INCREMENT)
- nombre (VARCHAR 50)
- descripcion (TEXT)
- activo (BOOLEAN)
```

### Tabla: productos
```sql
Campos:
- id (PK, AUTO_INCREMENT)
- nombre (VARCHAR 100)
- descripcion (TEXT)
- precio (DECIMAL 10,2)
- categoria_id (FK → categorias.id)
- imagen (VARCHAR 255)
- disponible (BOOLEAN)
- fecha_creacion (TIMESTAMP)

Relaciones:
- FK categoria_id → categorias.id (SET NULL)
```

### Tabla: pedidos
```sql
Campos:
- id (PK, AUTO_INCREMENT)
- usuario_id (FK → usuarios.id)
- total (DECIMAL 10,2)
- estado (ENUM: pendiente, preparando, enviado, entregado, cancelado)
- direccion_entrega (TEXT)
- telefono_contacto (VARCHAR 20)
- notas (TEXT)
- fecha_pedido (TIMESTAMP)
- fecha_actualizacion (TIMESTAMP)

Relaciones:
- FK usuario_id → usuarios.id (CASCADE)
```

### Tabla: detalles_pedidos
```sql
Campos:
- id (PK, AUTO_INCREMENT)
- pedido_id (FK → pedidos.id)
- producto_id (FK → productos.id)
- cantidad (INT)
- precio_unitario (DECIMAL 10,2)
- subtotal (DECIMAL 10,2)

Relaciones:
- FK pedido_id → pedidos.id (CASCADE)
- FK producto_id → productos.id (RESTRICT)
```

---

## 🔄 Flujo de Datos

### Crear Pedido
```
1. Cliente agrega productos al carrito (localStorage)
2. Click "Finalizar Pedido"
3. Modal con formulario
4. Submit → dashboard.js
5. Petición AJAX POST a api_crear_pedido
6. PedidoController.crear()
7. Validar sesión
8. Iniciar transacción
9. Insertar en tabla pedidos
10. Insertar detalles en detalles_pedidos
11. Commit transacción
12. Retornar JSON {success: true, pedido_id: X}
13. Limpiar carrito
14. Mostrar confirmación
15. Redirigir a "Mis Pedidos"
```

### Login
```
1. Usuario ingresa credenciales
2. Submit formulario
3. auth.js captura evento
4. Petición AJAX POST a action=login
5. AuthController.login()
6. Buscar usuario por email
7. Verificar password con password_verify()
8. Crear sesión
9. Retornar JSON {success: true, rol: 'cliente'}
10. Redirigir a dashboard
```

---

## 🔒 Seguridad Implementada

### 1. SQL Injection
```php
// ✅ CORRECTO - Prepared Statements
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->bindParam(":id", $id);

// ❌ INCORRECTO - Vulnerable
$query = "SELECT * FROM usuarios WHERE id = $id";
```

### 2. Passwords
```php
// Hashear al crear
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Verificar al login
if (password_verify($password_input, $password_hash)) {
    // Login correcto
}
```

### 3. Sesiones
```php
// Verificar antes de acciones sensibles
AuthController::verificarSesion();
AuthController::verificarAdmin();

// Regenerar ID de sesión
session_regenerate_id(true);
```

### 4. XSS Protection
```php
// Escapar output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// En views
<?php echo escape($data); ?>
```

---

## 📡 API Endpoints

### Formato de Respuesta
```json
// Success
{
  "success": true,
  "data": {...},
  "message": "Operación exitosa"
}

// Error
{
  "success": false,
  "message": "Descripción del error"
}
```

### Endpoints Disponibles

| Método | Endpoint | Auth | Admin | Descripción |
|--------|----------|------|-------|-------------|
| GET | ?action=api_productos | No | No | Listar productos |
| GET | ?action=api_producto&id=X | No | No | Detalle producto |
| GET | ?action=api_buscar_producto&q=X | No | No | Buscar productos |
| POST | ?action=api_crear_pedido | Sí | No | Crear pedido |
| GET | ?action=api_mis_pedidos | Sí | No | Mis pedidos |
| GET | ?action=api_todos_pedidos | Sí | Sí | Todos los pedidos |
| POST | ?action=api_actualizar_estado | Sí | Sí | Cambiar estado |
| POST | ?action=api_crear_producto | Sí | Sí | Crear producto |
| POST | ?action=api_actualizar_producto | Sí | Sí | Editar producto |

---

## 🎨 Variables CSS

```css
:root {
    --primary: #e63946;      /* Rojo principal */
    --secondary: #f4a261;    /* Naranja */
    --dark: #1d3557;         /* Azul oscuro */
    --light: #f1faee;        /* Blanco roto */
    --success: #06d6a0;      /* Verde */
    --danger: #ef476f;       /* Rojo error */
    --text: #2b2d42;         /* Texto general */
    --gray: #8d99ae;         /* Gris */
    --border: #dee2e6;       /* Bordes */
}
```

---

## 🔧 Configuración

### Cambiar Puerto del Servidor
```bash
php -S localhost:PUERTO
```

### Cambiar Credenciales DB
Editar `config/database.php`:
```php
private $host = "localhost";
private $db_name = "napanchita_db";
private $username = "root";
private $password = "";
```

### Modo Producción
Editar `config/config.php`:
```php
define('ENVIRONMENT', 'production');
```

---

## 📊 Métricas de Rendimiento

### Tamaño de Archivos
- CSS: ~35 KB
- JavaScript total: ~25 KB
- PHP total: ~60 KB

### Peticiones Típicas
- Home: 4-5 requests (HTML, CSS, JS)
- Dashboard: 6-8 requests (+ AJAX)
- Tiempo carga: < 1 segundo (local)

---

## 🧪 Testing

### Test Manual
1. Ejecutar test_conexion.php
2. Verificar cada funcionalidad
3. Probar en diferentes navegadores
4. Validar responsive en móvil

### Casos de Prueba
- ✅ Registro de nuevo usuario
- ✅ Login correcto/incorrecto
- ✅ Agregar productos al carrito
- ✅ Modificar cantidades
- ✅ Crear pedido completo
- ✅ Ver historial
- ✅ Admin: cambiar estados
- ✅ Búsqueda de productos
- ✅ Persistencia del carrito

---

## 📚 Recursos y Referencias

### PHP
- PDO Documentation
- Password Hashing
- Sessions

### JavaScript
- Fetch API
- LocalStorage API
- DOM Manipulation

### CSS
- Flexbox Guide
- Grid Guide
- Media Queries

---

**Documentación completa para desarrolladores y mantenimiento del sistema**
