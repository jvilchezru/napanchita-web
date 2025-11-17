# ARQUITECTURA DEL SISTEMA - NAPANCHITA WEB

## 1. ARQUITECTURA GENERAL

### Patrón Arquitectónico: MVC (Modelo-Vista-Controlador)

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENTE (Navegador)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   HTML/CSS   │  │  JavaScript  │  │    AJAX      │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP/HTTPS Requests
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    SERVIDOR WEB (Apache)                     │
│                         XAMPP                                │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                     APLICACIÓN PHP 8+                        │
│                                                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │                  FRONT CONTROLLER                   │    │
│  │                    (index.php)                      │    │
│  │         Enrutamiento y Gestión de Sesiones         │    │
│  └───────────────────┬────────────────────────────────┘    │
│                      │                                      │
│  ┌───────────────────┴────────────────────────────────┐    │
│  │                                                     │    │
│  ▼                   ▼                   ▼            │    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐           │    │
│  │ MODELOS  │  │ VISTAS   │  │CONTROLA- │           │    │
│  │          │  │          │  │  DORES   │           │    │
│  │ Usuario  │  │ login    │  │  Auth    │           │    │
│  │ Cliente  │  │ dashboard│  │ Pedido   │           │    │
│  │ Producto │  │ productos│  │ Producto │           │    │
│  │ Pedido   │  │ pedidos  │  │ Cliente  │           │    │
│  │ Mesa     │  │ mesas    │  │ Mesa     │           │    │
│  │ Reserva  │  │ reservas │  │ Reserva  │           │    │
│  │ Delivery │  │ delivery │  │ Delivery │           │    │
│  │ Venta    │  │ ventas   │  │ Venta    │           │    │
│  │ Reporte  │  │ reportes │  │ Reporte  │           │    │
│  └────┬─────┘  └──────────┘  └─────┬────┘           │    │
│       │                             │                │    │
│       └─────────────┬───────────────┘                │    │
│                     │                                │    │
│  ┌──────────────────┴───────────────────────────┐   │    │
│  │           CAPA DE ACCESO A DATOS             │   │    │
│  │              (database.php)                  │   │    │
│  │                   PDO                        │   │    │
│  └──────────────────┬───────────────────────────┘   │    │
│                     │                                │    │
└─────────────────────┼────────────────────────────────┘    │
                      │                                      │
                      ▼                                      │
┌─────────────────────────────────────────────────────────────┐
│              BASE DE DATOS (MySQL 8.0+)                     │
│                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ usuarios │  │ clientes │  │productos │  │  pedidos │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │  mesas   │  │ reservas │  │deliveries│  │  ventas  │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                │
│  │ combos   │  │categorias│  │ reportes │  ...           │
│  └──────────┘  └──────────┘  └──────────┘                │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. ARQUITECTURA MVC DETALLADA

### 2.1 CAPA DE PRESENTACIÓN (Vista)

**Responsabilidad:** Interfaz de usuario y experiencia visual

**Componentes:**

```
views/
├── layouts/
│   ├── header.php          # Cabecera común
│   ├── footer.php          # Pie de página común
│   ├── sidebar.php         # Menú lateral
│   └── navbar.php          # Barra de navegación
├── auth/
│   ├── login.php           # Página de login
│   └── registro.php        # Registro de usuarios (admin)
├── dashboard/
│   ├── admin.php           # Dashboard administrador
│   ├── mesero.php          # Dashboard mesero
│   └── repartidor.php      # Dashboard repartidor
├── productos/
│   ├── index.php           # Lista de productos
│   ├── crear.php           # Formulario crear producto
│   ├── editar.php          # Formulario editar producto
│   └── detalle.php         # Ver detalles del producto
├── categorias/
│   └── index.php           # Gestión de categorías
├── combos/
│   ├── index.php           # Lista de combos
│   └── form.php            # Formulario de combo
├── clientes/
│   ├── index.php           # Lista de clientes
│   └── form.php            # Formulario de cliente
├── pedidos/
│   ├── pos.php             # Punto de venta (tomar pedido)
│   ├── cocina.php          # Vista de cocina
│   ├── historial.php       # Historial de pedidos
│   └── detalle.php         # Detalle de pedido
├── mesas/
│   ├── layout.php          # Layout visual de mesas
│   ├── configurar.php      # Configurar mesas
│   └── estado.php          # Estado de mesas
├── reservas/
│   ├── calendario.php      # Calendario de reservas
│   ├── crear.php           # Crear reserva
│   └── listado.php         # Lista de reservas
├── delivery/
│   ├── admin.php           # Vista admin delivery
│   ├── repartidor.php      # Vista repartidor
│   └── zonas.php           # Gestión de zonas
├── ventas/
│   ├── registrar.php       # Registrar venta
│   ├── historial.php       # Historial de ventas
│   └── cierre_caja.php     # Cierre de caja
└── reportes/
    ├── ventas.php          # Reportes de ventas
    ├── productos.php       # Reportes de productos
    ├── delivery.php        # Reportes de delivery
    └── clientes.php        # Reportes de clientes
```

**Tecnologías:**

- HTML5 + CSS3
- Bootstrap 5 / Tailwind CSS
- JavaScript ES6+
- jQuery (opcional para AJAX)
- Chart.js para gráficos
- DataTables para tablas interactivas

---

### 2.2 CAPA DE LÓGICA DE NEGOCIO (Controlador)

**Responsabilidad:** Procesar solicitudes, validar datos, coordinar flujo

**Componentes:**

```
controllers/
├── AuthController.php          # Autenticación y sesiones
├── UsuarioController.php       # CRUD de usuarios
├── ClienteController.php       # CRUD de clientes
├── CategoriaController.php     # CRUD de categorías
├── ProductoController.php      # CRUD de productos
├── ComboController.php         # CRUD de combos
├── PedidoController.php        # Gestión de pedidos
├── MesaController.php          # Gestión de mesas
├── ReservaController.php       # Gestión de reservas
├── DeliveryController.php      # Gestión de delivery
├── VentaController.php         # Gestión de ventas
├── CierreCajaController.php    # Cierre de caja
└── ReporteController.php       # Generación de reportes
```

**Responsabilidades de cada Controller:**

1. Recibir parámetros de la solicitud
2. Validar datos de entrada
3. Llamar a modelos para operaciones de BD
4. Procesar lógica de negocio
5. Preparar datos para la vista
6. Cargar vista correspondiente
7. Manejar errores y excepciones

---

### 2.3 CAPA DE DATOS (Modelo)

**Responsabilidad:** Interacción con base de datos y lógica de datos

**Componentes:**

```
models/
├── Usuario.php             # Modelo de usuarios
├── Cliente.php             # Modelo de clientes
├── Categoria.php           # Modelo de categorías
├── Producto.php            # Modelo de productos
├── Combo.php               # Modelo de combos
├── ComboProducto.php       # Relación combos-productos
├── Mesa.php                # Modelo de mesas
├── Reserva.php             # Modelo de reservas
├── Pedido.php              # Modelo de pedidos
├── PedidoItem.php          # Detalles de pedidos
├── Delivery.php            # Modelo de delivery
├── ZonaDelivery.php        # Zonas de delivery
├── MetodoPago.php          # Métodos de pago
├── Venta.php               # Modelo de ventas
├── CierreCaja.php          # Modelo de cierre de caja
└── Reporte.php             # Queries para reportes
```

**Métodos comunes en modelos:**

- `crear()` - INSERT
- `obtenerPorId($id)` - SELECT por ID
- `listar()` - SELECT all
- `actualizar()` - UPDATE
- `eliminar($id)` - DELETE
- `buscar($criterio)` - SELECT con filtros

---

### 2.4 CAPA DE CONFIGURACIÓN

```
config/
├── database.php            # Configuración de BD y clase Database
├── config.php              # Constantes del sistema
├── routes.php              # Definición de rutas (opcional)
└── helpers.php             # Funciones auxiliares
```

**database.php** - Gestión de conexión PDO:

```php
class Database {
    private $host = "localhost";
    private $db_name = "napanchita_db";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
}
```

---

## 3. FLUJO DE EJECUCIÓN

### 3.1 Front Controller Pattern (index.php)

```php
<?php
session_start();

// Cargar configuración
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/helpers.php';

// Obtener acción de la URL
$action = $_GET['action'] ?? 'home';

// Enrutamiento simple
switch($action) {
    // Autenticación
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->mostrarLogin();
        break;

    case 'procesarLogin':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // Dashboard
    case 'dashboard':
        AuthController::verificarSesion();
        require_once 'views/dashboard/' . $_SESSION['usuario_rol'] . '.php';
        break;

    // Productos
    case 'productos':
        AuthController::verificarSesion();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->listar();
        break;

    // ... más rutas

    // Home pública
    case 'home':
    default:
        require_once 'views/home.php';
        break;
}
?>
```

---

### 3.2 Flujo de una Solicitud Típica

**Ejemplo: Crear un Pedido**

```
1. USUARIO
   ↓
   Hace clic en "Crear Pedido"
   ↓
2. NAVEGADOR
   ↓
   GET /index.php?action=crearPedido
   ↓
3. FRONT CONTROLLER (index.php)
   ↓
   Verifica sesión activa
   Carga PedidoController
   ↓
4. CONTROLADOR (PedidoController.php)
   ↓
   Método: mostrarFormulario()
   - Obtiene productos disponibles (ProductoModel)
   - Obtiene mesas disponibles (MesaModel)
   - Obtiene clientes (ClienteModel)
   - Prepara datos
   ↓
5. VISTA (views/pedidos/pos.php)
   ↓
   Renderiza formulario con datos
   ↓
6. USUARIO
   ↓
   Llena formulario y envía
   ↓
7. NAVEGADOR
   ↓
   POST /index.php?action=guardarPedido
   + Datos del formulario
   ↓
8. FRONT CONTROLLER
   ↓
   Carga PedidoController
   ↓
9. CONTROLADOR (PedidoController.php)
   ↓
   Método: guardar()
   - Valida datos recibidos
   - Crea objeto Pedido (modelo)
   - Llama a Pedido->crear()
   ↓
10. MODELO (Pedido.php)
    ↓
    Método: crear()
    - Inicia transacción
    - INSERT en tabla pedidos
    - INSERT en tabla pedido_items (por cada producto)
    - Actualiza estado de mesa
    - Commit transacción
    - Retorna resultado
    ↓
11. CONTROLADOR
    ↓
    Recibe resultado
    - Si éxito: Redirige a vista de cocina
    - Si error: Muestra mensaje de error
    ↓
12. NAVEGADOR
    ↓
    Muestra resultado al usuario
```

---

## 4. CAPAS TRANSVERSALES

### 4.1 Seguridad

**Implementaciones:**

1. **Autenticación:**

   - Sesiones PHP
   - Password hashing (bcrypt)
   - Verificación de rol en cada acción

2. **Prevención de Ataques:**

   - SQL Injection → PDO con prepared statements
   - XSS → htmlspecialchars() en vistas
   - CSRF → Tokens en formularios
   - Session Hijacking → session_regenerate_id()

3. **Validación:**
   ```php
   // Backend (Controller)
   function validarPedido($datos) {
       if (empty($datos['productos'])) {
           throw new Exception("Debe agregar productos");
       }
       if ($datos['total'] <= 0) {
           throw new Exception("Total inválido");
       }
       // ... más validaciones
   }
   ```

---

### 4.2 Gestión de Errores

**Estrategia:**

```php
// config/config.php
define('ENVIRONMENT', 'development'); // production

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// Manejador personalizado
set_exception_handler(function($e) {
    error_log($e->getMessage());
    if (ENVIRONMENT === 'development') {
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        header('Location: /error.php');
    }
});
```

---

### 4.3 Logging

**Implementación:**

```php
// config/helpers.php
function log_actividad($usuario_id, $accion, $detalles = '') {
    global $db;
    $query = "INSERT INTO logs (usuario_id, accion, detalles, fecha)
              VALUES (:usuario_id, :accion, :detalles, NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'accion' => $accion,
        'detalles' => $detalles
    ]);
}

// Uso en controladores
log_actividad($_SESSION['usuario_id'], 'CREAR_PEDIDO', 'Pedido #' . $pedido_id);
```

---

### 4.4 Caché (Opcional para Reportes)

```php
// Para reportes pesados
function obtenerReporteVentas($periodo) {
    $cache_key = "reporte_ventas_" . $periodo;
    $cache_file = __DIR__ . "/../cache/" . $cache_key . ".json";

    // Verificar si existe caché (válido por 1 hora)
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < 3600)) {
        return json_decode(file_get_contents($cache_file), true);
    }

    // Generar reporte
    $reporte = Reporte::generarVentas($periodo);

    // Guardar en caché
    file_put_contents($cache_file, json_encode($reporte));

    return $reporte;
}
```

---

## 5. COMPONENTES ADICIONALES

### 5.1 Generación de PDFs

**Librería:** TCPDF o FPDF

```php
// controllers/VentaController.php
public function generarTicket($venta_id) {
    require_once 'libraries/tcpdf/tcpdf.php';

    $venta = Venta::obtenerPorId($venta_id);

    $pdf = new TCPDF('P', 'mm', array(80, 200), true, 'UTF-8');
    $pdf->SetMargins(5, 5, 5);
    $pdf->AddPage();

    $html = $this->generarHTMLTicket($venta);
    $pdf->writeHTML($html);

    $pdf->Output('ticket_' . $venta_id . '.pdf', 'I');
}
```

---

### 5.2 Exportación a Excel

**Librería:** PhpSpreadsheet

```php
public function exportarReporte($datos) {
    require_once 'libraries/PhpSpreadsheet/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Headers
    $sheet->setCellValue('A1', 'Fecha');
    $sheet->setCellValue('B1', 'Producto');
    $sheet->setCellValue('C1', 'Cantidad');
    $sheet->setCellValue('D1', 'Total');

    // Datos
    $row = 2;
    foreach ($datos as $item) {
        $sheet->setCellValue('A' . $row, $item['fecha']);
        $sheet->setCellValue('B' . $row, $item['producto']);
        $sheet->setCellValue('C' . $row, $item['cantidad']);
        $sheet->setCellValue('D' . $row, $item['total']);
        $row++;
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('reporte.xlsx');
}
```

---

### 5.3 AJAX para Actualizaciones en Tiempo Real

**Frontend:**

```javascript
// public/js/cocina.js
function actualizarPedidos() {
  fetch("index.php?action=obtenerPedidosCocina")
    .then((response) => response.json())
    .then((data) => {
      actualizarTablero(data);
    })
    .catch((error) => console.error("Error:", error));
}

// Actualizar cada 30 segundos
setInterval(actualizarPedidos, 30000);
```

**Backend:**

```php
// controllers/PedidoController.php
public function obtenerPedidosCocina() {
    header('Content-Type: application/json');

    $pedidos = Pedido::obtenerPendientes();
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos
    ]);
}
```

---

## 6. ESCALABILIDAD Y RENDIMIENTO

### 6.1 Optimizaciones de Base de Datos

```sql
-- Índices críticos ya definidos en MER
-- Queries optimizadas con EXPLAIN
-- Uso de JOINs en lugar de múltiples queries
-- LIMIT en listados paginados
```

### 6.2 Optimizaciones de Frontend

```html
<!-- Minificación de CSS/JS -->
<link rel="stylesheet" href="css/style.min.css" />
<script src="js/app.min.js"></script>

<!-- Lazy loading de imágenes -->
<img src="producto.jpg" loading="lazy" alt="Producto" />

<!-- CDN para librerías -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

### 6.3 Compresión de Imágenes

```php
// helpers.php
function optimizarImagen($archivo, $max_width = 800) {
    list($width, $height) = getimagesize($archivo);

    if ($width > $max_width) {
        $ratio = $max_width / $width;
        $new_width = $max_width;
        $new_height = $height * $ratio;

        $image_src = imagecreatefromjpeg($archivo);
        $image_dest = imagecreatetruecolor($new_width, $new_height);

        imagecopyresampled($image_dest, $image_src, 0, 0, 0, 0,
                          $new_width, $new_height, $width, $height);

        imagejpeg($image_dest, $archivo, 85);

        imagedestroy($image_src);
        imagedestroy($image_dest);
    }
}
```

---

## 7. DESPLIEGUE

### Estructura de Archivos en Producción:

```
napanchita-web/
├── index.php                   # Front controller
├── .htaccess                   # Configuración Apache
├── config/
│   ├── database.php
│   ├── config.php
│   └── helpers.php
├── controllers/
├── models/
├── views/
├── public/                     # Archivos públicos
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/               # Imágenes subidas
├── libraries/                  # Librerías de terceros
│   ├── tcpdf/
│   └── PhpSpreadsheet/
├── cache/                      # Caché temporal
├── logs/                       # Logs del sistema
├── database/                   # Scripts SQL
│   ├── schema.sql
│   ├── migrations/
│   └── seeds/
└── docs/                       # Documentación
    ├── PLAN_DE_TRABAJO.md
    ├── diagramas/
    └── manuales/
```

### .htaccess para URLs amigables:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

---

## 8. DECISIONES ARQUITECTÓNICAS

| Decisión           | Razón                           | Alternativa Considerada          |
| ------------------ | ------------------------------- | -------------------------------- |
| MVC Manual         | Control total, ideal para tesis | Framework (Laravel, CodeIgniter) |
| PHP Vanilla        | Simplicidad, aprendizaje        | Node.js, Python                  |
| MySQL              | Maduro, confiable, XAMPP        | PostgreSQL, MongoDB              |
| Bootstrap          | Rápido desarrollo, responsive   | Tailwind, Material UI            |
| PDO                | Seguro, portable                | MySQLi                           |
| Session-based Auth | Simple, suficiente              | JWT, OAuth                       |

---

## RESUMEN PARA LA TESIS

### Arquitectura en 3 Capas:

1. **Presentación:** Views (HTML/CSS/JS)
2. **Lógica de Negocio:** Controllers (PHP)
3. **Datos:** Models + MySQL

### Patrón de Diseño Principal:

- MVC (Model-View-Controller)

### Patrones Adicionales:

- Front Controller
- Active Record (en modelos)
- Dependency Injection (en controllers)
- Repository Pattern (en modelos complejos)

### Tecnologías Core:

- **Backend:** PHP 8+, Apache
- **Frontend:** HTML5, CSS3, JavaScript ES6+, Bootstrap 5
- **Base de Datos:** MySQL 8.0+
- **Servidor:** XAMPP

---

**Elaborado:** 16/11/2025  
**Versión:** 1.0
