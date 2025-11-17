# MODELO ENTIDAD-RELACIÓN (MER) - SISTEMA NAPANCHITA

## Diagrama Conceptual

### Entidades Principales

#### 1. **USUARIOS** (Personal del Sistema)

Almacena información del personal que accede al sistema.

**Atributos:**

- `id` (PK) - INT
- `nombre` - VARCHAR(100)
- `email` - VARCHAR(100) UNIQUE
- `password` - VARCHAR(255)
- `telefono` - VARCHAR(20)
- `rol` - ENUM('admin', 'mesero', 'repartidor')
- `fecha_registro` - TIMESTAMP
- `activo` - BOOLEAN

---

#### 2. **CLIENTES** (Clientes Externos)

Almacena información de clientes que consumen los productos.

**Atributos:**

- `id` (PK) - INT
- `nombre` - VARCHAR(100)
- `telefono` - VARCHAR(20)
- `email` - VARCHAR(100) NULLABLE
- `direcciones` - JSON (array de direcciones)
- `notas` - TEXT
- `fecha_registro` - TIMESTAMP
- `activo` - BOOLEAN

**Ejemplo JSON direcciones:**

```json
[
  {
    "id": 1,
    "direccion": "Av. Salaverry 1234",
    "referencia": "Frente al parque",
    "zona_id": 1,
    "principal": true
  }
]
```

---

#### 3. **CATEGORIAS**

Clasificación de productos del menú.

**Atributos:**

- `id` (PK) - INT
- `nombre` - VARCHAR(50) UNIQUE
- `descripcion` - TEXT
- `orden` - INT (para ordenamiento visual)
- `activo` - BOOLEAN

---

#### 4. **PRODUCTOS**

Productos individuales del menú.

**Atributos:**

- `id` (PK) - INT
- `categoria_id` (FK) - INT
- `nombre` - VARCHAR(100)
- `descripcion` - TEXT
- `precio` - DECIMAL(10, 2)
- `imagen_url` - VARCHAR(255)
- `disponible` - BOOLEAN
- `fecha_creacion` - TIMESTAMP

---

#### 5. **COMBOS**

Agrupación de productos con precio especial.

**Atributos:**

- `id` (PK) - INT
- `nombre` - VARCHAR(100)
- `descripcion` - TEXT
- `precio` - DECIMAL(10, 2)
- `imagen_url` - VARCHAR(255)
- `activo` - BOOLEAN
- `fecha_creacion` - TIMESTAMP

---

#### 6. **COMBO_PRODUCTOS** (Relación N:M)

Productos incluidos en cada combo.

**Atributos:**

- `id` (PK) - INT
- `combo_id` (FK) - INT
- `producto_id` (FK) - INT
- `cantidad` - INT

---

#### 7. **MESAS**

Mesas del restaurante.

**Atributos:**

- `id` (PK) - INT
- `numero` - VARCHAR(10) UNIQUE
- `capacidad` - INT
- `estado` - ENUM('disponible', 'ocupada', 'reservada', 'inactiva')
- `posicion_x` - INT (para layout visual)
- `posicion_y` - INT
- `activo` - BOOLEAN

---

#### 8. **RESERVAS**

Reservas de mesas por parte de clientes.

**Atributos:**

- `id` (PK) - INT
- `cliente_id` (FK) - INT
- `mesa_id` (FK) - INT
- `fecha` - DATE
- `hora` - TIME
- `personas` - INT
- `estado` - ENUM('pendiente', 'confirmada', 'completada', 'cancelada', 'no_show')
- `codigo_confirmacion` - VARCHAR(20) UNIQUE
- `notas` - TEXT
- `creado_por_usuario_id` (FK) - INT
- `fecha_creacion` - TIMESTAMP
- `fecha_actualizacion` - TIMESTAMP

---

#### 9. **PEDIDOS**

Pedidos realizados por clientes.

**Atributos:**

- `id` (PK) - INT
- `cliente_id` (FK) - INT NULLABLE
- `mesa_id` (FK) - INT NULLABLE
- `usuario_id` (FK) - INT (quien registró el pedido)
- `tipo` - ENUM('mesa', 'delivery', 'para_llevar')
- `estado` - ENUM('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado')
- `subtotal` - DECIMAL(10, 2)
- `costo_envio` - DECIMAL(10, 2) DEFAULT 0
- `descuento` - DECIMAL(10, 2) DEFAULT 0
- `total` - DECIMAL(10, 2)
- `notas` - TEXT
- `fecha_pedido` - TIMESTAMP
- `fecha_actualizacion` - TIMESTAMP

---

#### 10. **PEDIDO_ITEMS** (Detalles de Pedido)

Items individuales de cada pedido.

**Atributos:**

- `id` (PK) - INT
- `pedido_id` (FK) - INT
- `producto_id` (FK) - INT NULLABLE
- `combo_id` (FK) - INT NULLABLE
- `tipo` - ENUM('producto', 'combo')
- `nombre` - VARCHAR(100) (snapshot del nombre)
- `cantidad` - INT
- `precio_unitario` - DECIMAL(10, 2)
- `subtotal` - DECIMAL(10, 2)
- `notas` - TEXT

---

#### 11. **ZONAS_DELIVERY**

Zonas geográficas para delivery.

**Atributos:**

- `id` (PK) - INT
- `nombre` - VARCHAR(100)
- `descripcion` - TEXT
- `costo_envio` - DECIMAL(10, 2)
- `tiempo_estimado` - INT (minutos)
- `activo` - BOOLEAN

---

#### 12. **DELIVERIES**

Información específica de pedidos delivery.

**Atributos:**

- `id` (PK) - INT
- `pedido_id` (FK) - INT UNIQUE
- `direccion` - TEXT
- `referencia` - TEXT
- `zona_id` (FK) - INT
- `repartidor_id` (FK) - INT NULLABLE
- `estado` - ENUM('pendiente', 'asignado', 'en_camino', 'entregado', 'fallido')
- `fecha_asignacion` - TIMESTAMP
- `fecha_entrega` - TIMESTAMP
- `observaciones` - TEXT

---

#### 13. **METODOS_PAGO**

Catálogo de métodos de pago aceptados.

**Atributos:**

- `id` (PK) - INT
- `nombre` - VARCHAR(50) (Efectivo, Tarjeta, Yape, Plin, Transferencia)
- `descripcion` - VARCHAR(255)
- `activo` - BOOLEAN

---

#### 14. **VENTAS**

Registro de ventas/pagos realizados.

**Atributos:**

- `id` (PK) - INT
- `pedido_id` (FK) - INT UNIQUE
- `metodo_pago_id` (FK) - INT
- `monto_recibido` - DECIMAL(10, 2)
- `monto_cambio` - DECIMAL(10, 2)
- `total` - DECIMAL(10, 2)
- `descuento_aplicado` - DECIMAL(10, 2) DEFAULT 0
- `codigo_descuento` - VARCHAR(50) NULLABLE
- `usuario_id` (FK) - INT (cajero que registró)
- `fecha_venta` - TIMESTAMP
- `ticket_generado` - BOOLEAN

---

#### 15. **CIERRES_CAJA**

Cierres de caja diarios.

**Atributos:**

- `id` (PK) - INT
- `usuario_id` (FK) - INT
- `fecha` - DATE
- `hora_apertura` - TIME
- `hora_cierre` - TIME
- `monto_inicial` - DECIMAL(10, 2)
- `total_efectivo_sistema` - DECIMAL(10, 2)
- `total_efectivo_fisico` - DECIMAL(10, 2)
- `total_tarjeta` - DECIMAL(10, 2)
- `total_digital` - DECIMAL(10, 2) (Yape, Plin, etc.)
- `total_ventas` - DECIMAL(10, 2)
- `diferencia` - DECIMAL(10, 2)
- `observaciones` - TEXT
- `fecha_cierre` - TIMESTAMP

---

## Relaciones (Cardinalidad)

### 1. **USUARIOS - PEDIDOS**

- **Relación:** "registra"
- **Cardinalidad:** 1:N (Un usuario registra muchos pedidos)
- **FK:** `pedidos.usuario_id → usuarios.id`

### 2. **USUARIOS - RESERVAS**

- **Relación:** "crea"
- **Cardinalidad:** 1:N
- **FK:** `reservas.creado_por_usuario_id → usuarios.id`

### 3. **USUARIOS (Repartidor) - DELIVERIES**

- **Relación:** "asignado a"
- **Cardinalidad:** 1:N
- **FK:** `deliveries.repartidor_id → usuarios.id`

### 4. **USUARIOS - VENTAS**

- **Relación:** "registra"
- **Cardinalidad:** 1:N
- **FK:** `ventas.usuario_id → usuarios.id`

### 5. **USUARIOS - CIERRES_CAJA**

- **Relación:** "realiza"
- **Cardinalidad:** 1:N
- **FK:** `cierres_caja.usuario_id → usuarios.id`

### 6. **CLIENTES - PEDIDOS**

- **Relación:** "realiza"
- **Cardinalidad:** 1:N
- **FK:** `pedidos.cliente_id → clientes.id`

### 7. **CLIENTES - RESERVAS**

- **Relación:** "hace"
- **Cardinalidad:** 1:N
- **FK:** `reservas.cliente_id → clientes.id`

### 8. **CATEGORIAS - PRODUCTOS**

- **Relación:** "contiene"
- **Cardinalidad:** 1:N
- **FK:** `productos.categoria_id → categorias.id`

### 9. **PRODUCTOS - PEDIDO_ITEMS**

- **Relación:** "incluido en"
- **Cardinalidad:** 1:N
- **FK:** `pedido_items.producto_id → productos.id`

### 10. **COMBOS - PEDIDO_ITEMS**

- **Relación:** "incluido en"
- **Cardinalidad:** 1:N
- **FK:** `pedido_items.combo_id → combos.id`

### 11. **COMBOS - PRODUCTOS** (N:M)

- **Relación:** "compuesto por"
- **Cardinalidad:** N:M (a través de COMBO_PRODUCTOS)
- **FK:**
  - `combo_productos.combo_id → combos.id`
  - `combo_productos.producto_id → productos.id`

### 12. **MESAS - PEDIDOS**

- **Relación:** "tiene"
- **Cardinalidad:** 1:N
- **FK:** `pedidos.mesa_id → mesas.id`

### 13. **MESAS - RESERVAS**

- **Relación:** "reservada por"
- **Cardinalidad:** 1:N
- **FK:** `reservas.mesa_id → mesas.id`

### 14. **PEDIDOS - PEDIDO_ITEMS**

- **Relación:** "contiene"
- **Cardinalidad:** 1:N
- **FK:** `pedido_items.pedido_id → pedidos.id`

### 15. **PEDIDOS - DELIVERIES**

- **Relación:** "tiene información de"
- **Cardinalidad:** 1:1
- **FK:** `deliveries.pedido_id → pedidos.id`

### 16. **PEDIDOS - VENTAS**

- **Relación:** "genera"
- **Cardinalidad:** 1:1
- **FK:** `ventas.pedido_id → pedidos.id`

### 17. **ZONAS_DELIVERY - DELIVERIES**

- **Relación:** "ubicado en"
- **Cardinalidad:** 1:N
- **FK:** `deliveries.zona_id → zonas_delivery.id`

### 18. **METODOS_PAGO - VENTAS**

- **Relación:** "pagado con"
- **Cardinalidad:** 1:N
- **FK:** `ventas.metodo_pago_id → metodos_pago.id`

---

## Diagrama Relacional (Formato Mermaid)

```mermaid
erDiagram
    USUARIOS ||--o{ PEDIDOS : "registra"
    USUARIOS ||--o{ RESERVAS : "crea"
    USUARIOS ||--o{ DELIVERIES : "asignado_a"
    USUARIOS ||--o{ VENTAS : "registra"
    USUARIOS ||--o{ CIERRES_CAJA : "realiza"

    CLIENTES ||--o{ PEDIDOS : "realiza"
    CLIENTES ||--o{ RESERVAS : "hace"

    CATEGORIAS ||--o{ PRODUCTOS : "contiene"

    PRODUCTOS ||--o{ PEDIDO_ITEMS : "incluido_en"
    PRODUCTOS ||--o{ COMBO_PRODUCTOS : "forma_parte"

    COMBOS ||--o{ PEDIDO_ITEMS : "incluido_en"
    COMBOS ||--o{ COMBO_PRODUCTOS : "compuesto_por"

    MESAS ||--o{ PEDIDOS : "tiene"
    MESAS ||--o{ RESERVAS : "reservada_por"

    PEDIDOS ||--o{ PEDIDO_ITEMS : "contiene"
    PEDIDOS ||--|| DELIVERIES : "tiene_info"
    PEDIDOS ||--|| VENTAS : "genera"

    ZONAS_DELIVERY ||--o{ DELIVERIES : "ubicado_en"

    METODOS_PAGO ||--o{ VENTAS : "pagado_con"

    USUARIOS {
        int id PK
        varchar nombre
        varchar email UK
        varchar password
        varchar telefono
        enum rol
        timestamp fecha_registro
        boolean activo
    }

    CLIENTES {
        int id PK
        varchar nombre
        varchar telefono
        varchar email
        json direcciones
        text notas
        timestamp fecha_registro
        boolean activo
    }

    CATEGORIAS {
        int id PK
        varchar nombre UK
        text descripcion
        int orden
        boolean activo
    }

    PRODUCTOS {
        int id PK
        int categoria_id FK
        varchar nombre
        text descripcion
        decimal precio
        varchar imagen_url
        boolean disponible
        timestamp fecha_creacion
    }

    COMBOS {
        int id PK
        varchar nombre
        text descripcion
        decimal precio
        varchar imagen_url
        boolean activo
        timestamp fecha_creacion
    }

    COMBO_PRODUCTOS {
        int id PK
        int combo_id FK
        int producto_id FK
        int cantidad
    }

    MESAS {
        int id PK
        varchar numero UK
        int capacidad
        enum estado
        int posicion_x
        int posicion_y
        boolean activo
    }

    RESERVAS {
        int id PK
        int cliente_id FK
        int mesa_id FK
        date fecha
        time hora
        int personas
        enum estado
        varchar codigo_confirmacion UK
        text notas
        int creado_por_usuario_id FK
        timestamp fecha_creacion
        timestamp fecha_actualizacion
    }

    PEDIDOS {
        int id PK
        int cliente_id FK
        int mesa_id FK
        int usuario_id FK
        enum tipo
        enum estado
        decimal subtotal
        decimal costo_envio
        decimal descuento
        decimal total
        text notas
        timestamp fecha_pedido
        timestamp fecha_actualizacion
    }

    PEDIDO_ITEMS {
        int id PK
        int pedido_id FK
        int producto_id FK
        int combo_id FK
        enum tipo
        varchar nombre
        int cantidad
        decimal precio_unitario
        decimal subtotal
        text notas
    }

    ZONAS_DELIVERY {
        int id PK
        varchar nombre
        text descripcion
        decimal costo_envio
        int tiempo_estimado
        boolean activo
    }

    DELIVERIES {
        int id PK
        int pedido_id FK UK
        text direccion
        text referencia
        int zona_id FK
        int repartidor_id FK
        enum estado
        timestamp fecha_asignacion
        timestamp fecha_entrega
        text observaciones
    }

    METODOS_PAGO {
        int id PK
        varchar nombre
        varchar descripcion
        boolean activo
    }

    VENTAS {
        int id PK
        int pedido_id FK UK
        int metodo_pago_id FK
        decimal monto_recibido
        decimal monto_cambio
        decimal total
        decimal descuento_aplicado
        varchar codigo_descuento
        int usuario_id FK
        timestamp fecha_venta
        boolean ticket_generado
    }

    CIERRES_CAJA {
        int id PK
        int usuario_id FK
        date fecha
        time hora_apertura
        time hora_cierre
        decimal monto_inicial
        decimal total_efectivo_sistema
        decimal total_efectivo_fisico
        decimal total_tarjeta
        decimal total_digital
        decimal total_ventas
        decimal diferencia
        text observaciones
        timestamp fecha_cierre
    }
```

---

## Índices Recomendados

### Para Optimización de Consultas:

```sql
-- USUARIOS
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_rol ON usuarios(rol);
CREATE INDEX idx_usuarios_activo ON usuarios(activo);

-- CLIENTES
CREATE INDEX idx_clientes_telefono ON clientes(telefono);
CREATE INDEX idx_clientes_email ON clientes(email);

-- PRODUCTOS
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_disponible ON productos(disponible);

-- PEDIDOS
CREATE INDEX idx_pedidos_cliente ON pedidos(cliente_id);
CREATE INDEX idx_pedidos_mesa ON pedidos(mesa_id);
CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_pedidos_tipo ON pedidos(tipo);
CREATE INDEX idx_pedidos_estado ON pedidos(estado);
CREATE INDEX idx_pedidos_fecha ON pedidos(fecha_pedido);

-- PEDIDO_ITEMS
CREATE INDEX idx_pedido_items_pedido ON pedido_items(pedido_id);
CREATE INDEX idx_pedido_items_producto ON pedido_items(producto_id);
CREATE INDEX idx_pedido_items_combo ON pedido_items(combo_id);

-- RESERVAS
CREATE INDEX idx_reservas_cliente ON reservas(cliente_id);
CREATE INDEX idx_reservas_mesa ON reservas(mesa_id);
CREATE INDEX idx_reservas_fecha ON reservas(fecha, hora);
CREATE INDEX idx_reservas_estado ON reservas(estado);
CREATE INDEX idx_reservas_codigo ON reservas(codigo_confirmacion);

-- DELIVERIES
CREATE INDEX idx_deliveries_pedido ON deliveries(pedido_id);
CREATE INDEX idx_deliveries_repartidor ON deliveries(repartidor_id);
CREATE INDEX idx_deliveries_zona ON deliveries(zona_id);
CREATE INDEX idx_deliveries_estado ON deliveries(estado);

-- VENTAS
CREATE INDEX idx_ventas_pedido ON ventas(pedido_id);
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_metodo_pago ON ventas(metodo_pago_id);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);

-- CIERRES_CAJA
CREATE INDEX idx_cierres_usuario ON cierres_caja(usuario_id);
CREATE INDEX idx_cierres_fecha ON cierres_caja(fecha);
```

---

## Reglas de Integridad

### Integridad Referencial:

- Todas las FK deben existir en las tablas referenciadas
- ON DELETE RESTRICT para productos/combos en pedidos activos
- ON DELETE CASCADE para detalles de pedidos eliminados
- ON DELETE SET NULL para relaciones opcionales

### Integridad de Dominio:

- Precios siempre > 0
- Cantidades siempre > 0
- Estados solo valores permitidos en ENUM
- Emails con formato válido
- Teléfonos con formato válido

### Reglas de Negocio:

1. Un pedido debe tener al menos 1 item
2. El total del pedido = suma de items + costo_envio - descuento
3. Una mesa no puede tener múltiples pedidos activos simultáneos
4. Una reserva confirmada bloquea la mesa en esa fecha/hora
5. Un repartidor no puede tener más de X pedidos asignados simultáneamente
6. El monto_cambio debe ser >= 0
7. fecha_entrega debe ser >= fecha_asignacion

---

## Normalización

El modelo está en **Tercera Forma Normal (3FN)**:

✅ **1FN:** Todos los atributos son atómicos (excepto JSON en clientes.direcciones por diseño)
✅ **2FN:** No hay dependencias parciales
✅ **3FN:** No hay dependencias transitivas

### Desnormalización Controlada:

- `pedido_items.nombre`: Snapshot del nombre para histórico
- `clientes.direcciones`: JSON para flexibilidad (alternativa a tabla adicional)

---

## Estimación de Volumen

| Tabla        | Registros Estimados | Crecimiento |
| ------------ | ------------------- | ----------- |
| USUARIOS     | 10-50               | Bajo        |
| CLIENTES     | 1000-5000           | Medio       |
| CATEGORIAS   | 10-20               | Muy Bajo    |
| PRODUCTOS    | 50-200              | Bajo        |
| COMBOS       | 10-30               | Bajo        |
| MESAS        | 10-30               | Muy Bajo    |
| RESERVAS     | 500/mes             | Medio       |
| PEDIDOS      | 100-300/día         | Alto        |
| PEDIDO_ITEMS | 300-900/día         | Alto        |
| DELIVERIES   | 50-150/día          | Medio       |
| VENTAS       | 100-300/día         | Alto        |
| CIERRES_CAJA | 1/día               | Bajo        |

---

## Notas para la Tesis

### Justificación del Diseño:

1. **Separación usuarios/clientes:** Los usuarios son personal interno con acceso al sistema; los clientes son externos sin acceso
2. **Tabla combo_productos:** Permite flexibilidad para que un combo tenga múltiples productos con cantidades
3. **Campo JSON en clientes:** Simplifica manejo de múltiples direcciones sin crear tabla adicional
4. **Snapshot de nombres en pedido_items:** Mantiene histórico aunque cambien productos
5. **Relación 1:1 pedidos-deliveries:** Solo pedidos tipo delivery tienen registro en deliveries

### Diagramas a Incluir en Tesis:

1. ✅ Modelo Entidad-Relación conceptual
2. ✅ Diagrama relacional con FK
3. ✅ Diccionario de datos completo
4. Normalización paso a paso (1FN → 2FN → 3FN)

---

**Elaborado:** 16/11/2025  
**Versión:** 1.0
