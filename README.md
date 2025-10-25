# 🍽️ NAPANCHITA - Sistema de Gestión de Pedidos y Delivery

Sistema web completo para la gestión de pedidos y delivery de un restaurante, desarrollado con arquitectura MVC.

## 🚀 Características

- **Landing Page Dinámica**: Página principal responsive con información del restaurante
- **Sistema de Autenticación**: Login y registro de usuarios
- **Dos Roles de Usuario**:
  - **Cliente**: Puede ver el menú, agregar productos al carrito y realizar pedidos
  - **Administrador**: Puede gestionar productos, ver todos los pedidos y actualizar estados
- **Carrito de Compras**: Sistema interactivo con localStorage
- **Gestión de Pedidos**: Seguimiento en tiempo real del estado de los pedidos
- **Diseño Responsive**: Adaptable a dispositivos móviles, tablets y desktop
- **Interfaz Interactiva**: Animaciones, transiciones y AJAX

## 🛠️ Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Arquitectura**: MVC (Model-View-Controller)

## 📋 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx) o usar servidor PHP integrado
- Extensiones PHP: PDO, pdo_mysql

## 📦 Instalación

### 1. Clonar o Descargar el Proyecto

Coloca los archivos en tu directorio web o en la ubicación deseada.

### 2. Configurar la Base de Datos

1. Abre phpMyAdmin o tu gestor de MySQL
2. Ejecuta el script SQL ubicado en: `database/schema.sql`
3. Esto creará la base de datos `napanchita_db` con todas las tablas y datos de ejemplo

### 3. Configurar la Conexión

Edita el archivo `config/database.php` si necesitas cambiar las credenciales:

```php
private $host = "localhost";
private $db_name = "napanchita_db";
private $username = "root";
private $password = "";
```

### 4. Iniciar el Servidor

#### Opción A: Servidor PHP Integrado (Desarrollo)

```bash
cd napanchita
php -S localhost:8000
```

Luego accede a: `http://localhost:8000`

#### Opción B: XAMPP/WAMP/LAMP

1. Copia la carpeta del proyecto a `htdocs` (XAMPP) o `www` (WAMP)
2. Inicia Apache y MySQL
3. Accede a: `http://localhost/napanchita`

## 👤 Credenciales de Prueba

### Administrador
- **Email**: admin@napanchita.com
- **Contraseña**: password

### Cliente
- **Email**: juan@email.com
- **Contraseña**: password

## 📂 Estructura del Proyecto

```
napanchita/
├── config/
│   └── database.php          # Configuración de BD
├── controllers/
│   ├── AuthController.php    # Autenticación
│   ├── ProductoController.php # Gestión de productos
│   └── PedidoController.php  # Gestión de pedidos
├── models/
│   ├── Usuario.php           # Modelo de usuario
│   ├── Producto.php          # Modelo de producto
│   └── Pedido.php            # Modelo de pedido
├── views/
│   ├── home.php              # Página principal
│   ├── login.php             # Login
│   ├── registro.php          # Registro
│   └── dashboard.php         # Dashboard
├── public/
│   ├── css/
│   │   └── style.css         # Estilos principales
│   ├── js/
│   │   ├── main.js           # JavaScript principal
│   │   ├── auth.js           # JavaScript autenticación
│   │   └── dashboard.js      # JavaScript dashboard
│   └── images/               # Imágenes
├── database/
│   └── schema.sql            # Script de BD
└── index.php                 # Punto de entrada
```

## 🎯 Funcionalidades por Rol

### Cliente
- ✅ Ver landing page con información del restaurante
- ✅ Registrarse en el sistema
- ✅ Iniciar sesión
- ✅ Ver menú completo de productos
- ✅ Buscar productos
- ✅ Agregar productos al carrito
- ✅ Modificar cantidades en el carrito
- ✅ Realizar pedidos
- ✅ Ver historial de pedidos
- ✅ Seguimiento del estado de pedidos

### Administrador
- ✅ Todas las funciones de cliente
- ✅ Ver todos los pedidos del sistema
- ✅ Actualizar estado de pedidos (pendiente, preparando, enviado, entregado, cancelado)
- ✅ Gestionar productos (crear, editar, cambiar disponibilidad)
- ✅ Ver información de clientes en cada pedido

## 🎨 Características de Diseño

- **Mobile-First**: Diseño optimizado primero para móviles
- **Responsive**: Adaptable a todos los tamaños de pantalla
- **Animaciones**: Transiciones suaves y efectos de scroll
- **UX Intuitiva**: Interfaz clara y fácil de usar
- **Color Scheme**: Paleta de colores atractiva y profesional

## 🔧 API Endpoints

El sistema utiliza los siguientes endpoints:

- `?action=home` - Página principal
- `?action=login` - Login (GET/POST)
- `?action=registro` - Registro (GET/POST)
- `?action=logout` - Cerrar sesión
- `?action=dashboard` - Dashboard principal
- `?action=api_productos` - Listar productos (JSON)
- `?action=api_crear_pedido` - Crear pedido (POST JSON)
- `?action=api_mis_pedidos` - Mis pedidos (JSON)
- `?action=api_todos_pedidos` - Todos los pedidos (JSON)
- `?action=api_actualizar_estado` - Actualizar estado pedido (POST)

## 💾 Base de Datos

### Tablas Principales

- **usuarios**: Almacena clientes y administradores
- **categorias**: Categorías de productos
- **productos**: Menú del restaurante
- **pedidos**: Pedidos realizados
- **detalles_pedidos**: Items de cada pedido

## 🔒 Seguridad

- Contraseñas hasheadas con BCrypt
- Prepared statements para prevenir SQL Injection
- Validación de sesiones
- Control de acceso por rol
- Sanitización de entradas

## 📱 Responsive Breakpoints

- **Mobile**: < 480px
- **Tablet**: 481px - 768px
- **Desktop**: > 768px

## 🚀 Próximas Mejoras (Sugerencias)

- [ ] Pasarela de pago online
- [ ] Notificaciones en tiempo real
- [ ] Sistema de calificaciones
- [ ] Cupones de descuento
- [ ] Historial de búsquedas
- [ ] Chat de soporte
- [ ] Generación de reportes PDF
- [ ] API REST completa

## 📝 Notas

- El carrito se guarda en localStorage del navegador
- Los productos de ejemplo incluyen comida típica boliviana
- El sistema está preparado para agregar más funcionalidades

## 👨‍💻 Desarrollo

Este sistema fue desarrollado siguiendo las mejores prácticas de:
- Separación de responsabilidades (MVC)
- Código limpio y comentado
- DRY (Don't Repeat Yourself)
- Seguridad web
- Diseño responsive

## 📄 Licencia

Este proyecto es de código abierto para fines educativos.

---

**¡Disfruta del sistema Napanchita!** 🍽️
