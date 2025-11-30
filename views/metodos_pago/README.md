# Módulo de Métodos de Pago

## Descripción
Sistema completo de gestión de métodos de pago para el restaurante Napanchita.

## Características Implementadas

### 1. Modelo (MetodoPago.php)
- ✅ CRUD completo
- ✅ Listar métodos de pago con filtros
- ✅ Activar/desactivar métodos
- ✅ Validación de duplicados
- ✅ Verificación de uso antes de eliminar

### 2. Controlador (MetodoPagoController.php)
- ✅ index() - Listado con filtros
- ✅ crear() - Formulario de creación
- ✅ guardar() - Guardar nuevo método
- ✅ editar() - Formulario de edición
- ✅ actualizar() - Actualizar método existente
- ✅ eliminar() - Desactivar método
- ✅ cambiarEstado() - Cambiar estado (AJAX)

### 3. Vistas
- ✅ `index.php` - Listado con DataTables
- ✅ `crear.php` - Formulario de creación
- ✅ `editar.php` - Formulario de edición

### 4. Funcionalidades
- ✅ Búsqueda por nombre
- ✅ Filtro por estado (activo/inactivo)
- ✅ Switch para activar/desactivar
- ✅ Confirmación de eliminación con SweetAlert2
- ✅ Validación de datos
- ✅ Protección contra eliminación si está en uso
- ✅ Solo accesible por administradores

## Métodos de Pago por Defecto
1. Efectivo
2. Tarjeta de Crédito
3. Tarjeta de Débito
4. Yape
5. Plin
6. Transferencia Bancaria

## Rutas Implementadas
```
/index.php?action=metodos_pago                  - Listado
/index.php?action=metodos_pago_crear            - Crear
/index.php?action=metodos_pago_guardar          - Guardar
/index.php?action=metodos_pago_editar&id=X      - Editar
/index.php?action=metodos_pago_actualizar       - Actualizar
/index.php?action=metodos_pago_eliminar&id=X    - Eliminar
/index.php?action=metodos_pago_cambiarEstado    - Cambiar estado (AJAX)
```

## Estructura de Base de Datos
```sql
CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_metodos_pago_activo (activo)
);
```

## Permisos
- Solo usuarios con rol **ADMIN** pueden acceder a este módulo

## Integración
Este módulo está integrado con:
- ✅ Tabla `ventas` (campo `metodo_pago_id`)
- ✅ Menú lateral del sistema
- ✅ Sistema de autenticación

## Scripts SQL
- `database/metodos_pago_init.sql` - Script de inicialización

## Uso

### Crear un método de pago
1. Ir a "Métodos de Pago" en el menú
2. Clic en "Nuevo Método de Pago"
3. Llenar formulario (nombre obligatorio)
4. Guardar

### Editar un método de pago
1. En el listado, clic en botón "Editar" (amarillo)
2. Modificar datos
3. Guardar cambios

### Activar/Desactivar
- Usar el switch en la columna "Estado"
- El cambio se aplica inmediatamente via AJAX

### Eliminar
- Clic en botón "Eliminar" (rojo)
- Confirmar acción
- Si el método está en uso en ventas, no se podrá eliminar

## Validaciones
- ✅ Nombre único
- ✅ Nombre obligatorio
- ✅ No permitir eliminar si está en uso
- ✅ Máximo 50 caracteres para nombre
- ✅ Máximo 255 caracteres para descripción
