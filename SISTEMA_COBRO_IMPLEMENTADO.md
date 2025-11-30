# Sistema de Cobro y Finalización de Pedidos

## Implementación Completada

### 📋 Funcionalidades Implementadas

#### 1. Modal de Cobro con Selección de Método de Pago
- **Vista**: `views/pedidos/ver.php`
- **Función JavaScript**: `cobrarYFinalizar()`
- Modal interactivo con SweetAlert2 que incluye:
  - Visualización del total del pedido
  - Selección de método de pago (dropdown)
  - Campos dinámicos para efectivo (monto recibido y vuelto)
  - Cálculo automático de vuelto en tiempo real
  - Validación de montos

#### 2. Cálculo de Vuelto para Pagos en Efectivo
- Detección automática cuando se selecciona "Efectivo"
- Campo de entrada para "Monto recibido"
- Cálculo y visualización automática del vuelto
- Validación: monto recibido debe ser >= total del pedido
- Indicador visual (color rojo) cuando el monto es insuficiente

#### 3. Registro de Venta en Base de Datos
- **Controlador**: `PedidoController.php` - método `finalizar()`
- Almacenamiento en tabla `ventas`:
  - pedido_id
  - metodo_pago_id
  - total
  - monto_recibido
  - monto_cambio
  - usuario_id (cajero)
  - fecha_venta
- Transacción con rollback automático en caso de error

#### 4. Endpoint AJAX para Obtener Métodos de Pago
- **Controlador**: `PedidoController.php` - método `obtenerMetodosPago()`
- **Ruta**: `index.php?action=pedidos_obtenerMetodosPago`
- Retorna solo métodos de pago activos
- Formato JSON con manejo de errores

### 🔄 Flujo del Sistema

```
1. Usuario hace clic en "Cobrar y Finalizar" (pedido en estado 'entregado')
   ↓
2. JavaScript obtiene métodos de pago vía AJAX (obtenerMetodosPago)
   ↓
3. Se muestra modal con:
   - Total del pedido
   - Dropdown de métodos de pago
   ↓
4. Si selecciona "Efectivo":
   - Aparecen campos de monto recibido y vuelto
   - Cálculo automático en tiempo real
   ↓
5. Al confirmar:
   - Validación de datos (método de pago, monto recibido >= total)
   - POST a pedidos_finalizar con:
     * pedido_id
     * metodo_pago_id
     * monto_recibido
     * monto_cambio
   ↓
6. Servidor (PedidoController::finalizar):
   - Inicia transacción
   - Crea registro en tabla ventas
   - Cambia estado del pedido a 'finalizado'
   - El trigger automáticamente libera la mesa
   - Confirma transacción
   - Registra log de actividad
   ↓
7. Respuesta al cliente:
   - Muestra mensaje de éxito
   - Muestra vuelto si fue efectivo
   - Redirige a lista de pedidos
```

### 📊 Estructura de Datos

#### Tabla: ventas
```sql
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNIQUE NOT NULL,
    metodo_pago_id INT NOT NULL,
    monto_recibido DECIMAL(10, 2) NOT NULL,
    monto_cambio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    descuento_aplicado DECIMAL(10, 2) DEFAULT 0,
    codigo_descuento VARCHAR(50),
    usuario_id INT NOT NULL COMMENT 'Cajero que registró',
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ticket_generado BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

### 🎨 Interfaz de Usuario

#### Modal de Cobro (SweetAlert2)
```
┌─────────────────────────────────────────┐
│           Cobrar Pedido                 │
├─────────────────────────────────────────┤
│ Total a cobrar:                         │
│ S/ 89.00                                │
│                                         │
│ Método de pago *                        │
│ [Dropdown: Efectivo ▼]                  │
│                                         │
│ ──── Solo si es Efectivo ────          │
│                                         │
│ Monto recibido *                        │
│ [Input: 100.00]                         │
│                                         │
│ Vuelto:                                 │
│ S/ 11.00                                │
│                                         │
├─────────────────────────────────────────┤
│      [Cancelar]  [Cobrar y Finalizar]  │
└─────────────────────────────────────────┘
```

### 🔐 Validaciones Implementadas

1. **Frontend (JavaScript)**:
   - Método de pago debe estar seleccionado
   - Si es efectivo, monto recibido es obligatorio
   - Monto recibido debe ser >= total del pedido
   - Indicadores visuales de errores

2. **Backend (PHP)**:
   - Pedido debe existir
   - Pedido debe estar en estado 'entregado'
   - Método de pago debe estar proporcionado
   - Monto recibido debe ser >= total del pedido
   - Usuario debe tener rol autorizado (ADMIN o MESERO)

### 📝 Archivos Modificados

1. **views/pedidos/ver.php**
   - Función `cobrarYFinalizar()` completamente reescrita
   - Nueva función `obtenerMetodosPago()`
   - Modal interactivo con cálculo de vuelto

2. **controllers/PedidoController.php**
   - Método `finalizar()` actualizado para recibir datos de pago
   - Nuevo método `obtenerMetodosPago()`
   - Require de `MetodoPago.php`
   - Creación de registro en tabla ventas
   - Transacción con manejo de errores

3. **index.php**
   - Nueva ruta: `pedidos_obtenerMetodosPago`

### ✅ Características Destacadas

- **Experiencia de Usuario Intuitiva**: Modal amigable con validaciones en tiempo real
- **Cálculo Automático**: El vuelto se calcula mientras el usuario escribe
- **Seguridad**: Validaciones frontend y backend, transacciones SQL
- **Trazabilidad**: Log de actividad + registro de venta completo
- **Flexibilidad**: Soporta cualquier método de pago, especial atención a efectivo
- **Integración**: Trabaja con el trigger existente para liberar mesas
- **Feedback Visual**: Colores que indican estados (verde: suficiente, rojo: insuficiente)

### 🧪 Caso de Prueba

**Escenario**: Cobrar pedido #13 (Mesa, Total: S/ 89.00)

**Pasos**:
1. Ingresar a ver pedido #13
2. Verificar estado: "Entregado"
3. Clic en "Cobrar y Finalizar"
4. Seleccionar "Efectivo"
5. Ingresar monto recibido: S/ 100.00
6. Verificar vuelto: S/ 11.00
7. Clic en "Cobrar y Finalizar"
8. Verificar:
   - Mensaje de éxito con vuelto
   - Pedido cambia a estado "Finalizado"
   - Mesa liberada (disponible)
   - Registro creado en tabla ventas
   - Log de actividad registrado

### 🔗 Dependencias

- SweetAlert2: Para modals y confirmaciones
- jQuery: Para peticiones AJAX
- Bootstrap 5: Para estilos del formulario
- PHP 8.0+: Sintaxis moderna
- MySQL 8.0+: Base de datos con transacciones

### 📈 Mejoras Futuras (Opcionales)

- Impresión automática de ticket después de cobrar
- Opción de enviar comprobante por email
- Estadísticas de ventas por método de pago
- Integración con pasarelas de pago online
- Soporte para pagos mixtos (efectivo + tarjeta)
- Historial de ventas del día en tiempo real
