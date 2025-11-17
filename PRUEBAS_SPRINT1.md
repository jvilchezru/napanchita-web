# GUÍA DE PRUEBAS - SPRINT 1

## Sistema de Autenticación y Gestión de Usuarios

### Requisitos Previos

1. **Servidor XAMPP iniciado:**

   - Apache corriendo en puerto 80
   - MySQL corriendo en puerto 3306

2. **Base de datos creada:**

   - Ejecutar `database/schema_completo.sql` en phpMyAdmin
   - O desde terminal: `mysql -u root -p < database/schema_completo.sql`

3. **Configuración verificada:**
   - Revisar `config/database.php` (credenciales de DB)
   - Revisar `config/config.php` (BASE_URL correcta)

---

## 1. PRUEBA DE INSTALACIÓN

### 1.1 Verificar Base de Datos

```sql
-- Desde phpMyAdmin o MySQL CLI
USE napanchita;
SHOW TABLES;  -- Debe mostrar 16 tablas

-- Verificar usuarios de prueba
SELECT * FROM usuarios;
-- Debe mostrar 3 usuarios: admin, mesero, repartidor
```

### 1.2 Verificar Configuración PHP

```
URL: http://localhost/napanchita-web/test_conexion.php
```

**Resultado esperado:** Mensaje de conexión exitosa a la base de datos

---

## 2. PRUEBAS DE LOGIN

### 2.1 Acceso al Sistema

```
URL: http://localhost/napanchita-web/
```

**Resultado esperado:** Página de login con diseño moderno

### 2.2 Login con Credenciales Inválidas

**Datos de prueba:**

- Email: `usuario@falso.com`
- Password: `123456`

**Resultado esperado:**

- ❌ Mensaje de error: "Email o contraseña incorrectos"
- ✓ Permanecer en página de login

### 2.3 Login como Administrador

**Datos de prueba:**

- Email: `admin@napanchita.com`
- Password: `password123`

**Resultado esperado:**

- ✓ Redirección a `/index.php?action=dashboard`
- ✓ Mostrar Dashboard de Administrador
- ✓ Sidebar con todas las opciones de admin
- ✓ Estadísticas visibles (pedidos, ventas, clientes, etc.)
- ✓ Gráficos de Chart.js renderizados

### 2.4 Login como Mesero

**Datos de prueba:**

- Email: `mesero@napanchita.com`
- Password: `password123`

**Resultado esperado:**

- ✓ Redirección a Dashboard de Mesero
- ✓ Accesos rápidos a "Nuevo Pedido" y "Nueva Reserva"
- ✓ Visualización del estado de mesas
- ✓ Tabla de pedidos activos

### 2.5 Login como Repartidor

**Datos de prueba:**

- Email: `repartidor@napanchita.com`
- Password: `password123`

**Resultado esperado:**

- ✓ Redirección a Dashboard de Repartidor
- ✓ Estadísticas de entregas del día
- ✓ Lista de entregas pendientes
- ✓ Panel de rendimiento mensual

---

## 3. PRUEBAS DE GESTIÓN DE USUARIOS (Admin)

### 3.1 Acceder a Gestión de Usuarios

**Pasos:**

1. Login como admin
2. Click en menú lateral: "Gestión de Usuarios"

**URL:** `http://localhost/napanchita-web/index.php?action=usuarios`

**Resultado esperado:**

- ✓ Tabla DataTables con 3 usuarios
- ✓ Botón "Nuevo Usuario" visible
- ✓ Filtros por rol y estado funcionales
- ✓ Acciones (Editar, Cambiar Estado, Eliminar) disponibles

### 3.2 Crear Nuevo Usuario

**Pasos:**

1. Click en "Nuevo Usuario"
2. Llenar formulario:
   - Nombre: `Carlos Ramírez`
   - Email: `carlos@napanchita.com`
   - Password: `test123`
   - Confirmar Password: `test123`
   - Rol: `Mesero`
   - Teléfono: `987654321`
   - Estado: Activo

**Resultado esperado:**

- ✓ Validación de campos en frontend
- ✓ Redirección a lista de usuarios
- ✓ Mensaje de éxito con SweetAlert2
- ✓ Usuario aparece en la tabla

### 3.3 Editar Usuario

**Pasos:**

1. Click en botón "Editar" (ícono lápiz) del usuario creado
2. Modificar nombre a: `Carlos Ramírez López`
3. Guardar cambios

**Resultado esperado:**

- ✓ Formulario pre-cargado con datos actuales
- ✓ Mensaje de éxito al guardar
- ✓ Cambios reflejados en la lista

### 3.4 Cambiar Estado de Usuario

**Pasos:**

1. Click en botón "Cambiar Estado" (ícono ban/check)
2. Confirmar acción en modal

**Resultado esperado:**

- ✓ Modal de confirmación con SweetAlert2
- ✓ Badge de estado cambia de color
- ✓ Usuario inactivo no puede hacer login

### 3.5 Intentar Modificar Propio Usuario

**Pasos:**

1. Como admin, buscar tu propio usuario en la lista
2. Verificar botones de acciones

**Resultado esperado:**

- ✓ Botones de estado/eliminar deshabilitados
- ✓ Solo botón de editar disponible
- ✓ Mensaje de seguridad visible

### 3.6 Eliminar Usuario

**Pasos:**

1. Click en botón "Eliminar" (ícono basura)
2. Confirmar eliminación

**Resultado esperado:**

- ✓ Modal de confirmación
- ✓ Usuario removido de la lista
- ✓ Mensaje de éxito

---

## 4. PRUEBAS DE SEGURIDAD

### 4.1 Acceso sin Autenticación

**Prueba:**

```
URL: http://localhost/napanchita-web/index.php?action=usuarios
```

(Sin estar logueado)

**Resultado esperado:**

- ✓ Redirección automática a login
- ✓ Mensaje: "Debe iniciar sesión"

### 4.2 Acceso de Mesero a Gestión de Usuarios

**Pasos:**

1. Login como mesero
2. Intentar acceder: `/index.php?action=usuarios`

**Resultado esperado:**

- ✓ Acceso denegado o redirección
- ✓ Mensaje: "No tiene permisos"

### 4.3 SQL Injection en Login

**Prueba:**

- Email: `admin@napanchita.com' OR '1'='1`
- Password: `cualquier cosa`

**Resultado esperado:**

- ✓ Login fallido
- ✓ Sin errores SQL visibles
- ✓ PDO prepared statements funcionando

### 4.4 XSS en Formularios

**Prueba al crear usuario:**

- Nombre: `<script>alert('XSS')</script>`

**Resultado esperado:**

- ✓ Script no se ejecuta
- ✓ Texto guardado como string literal
- ✓ Sanitización funcionando

---

## 5. PRUEBAS DE SESIÓN

### 5.1 Timeout de Sesión

**Pasos:**

1. Login exitoso
2. Esperar 1 hora sin actividad (o modificar SESSION_TIMEOUT en config.php a 60 segundos)
3. Intentar navegar

**Resultado esperado:**

- ✓ Sesión expirada
- ✓ Redirección a login
- ✓ Mensaje de timeout

### 5.2 Logout Manual

**Pasos:**

1. Login exitoso
2. Click en menú de usuario > "Cerrar Sesión"

**Resultado esperado:**

- ✓ Sesión destruida
- ✓ Redirección a login
- ✓ Log de actividad registrado

### 5.3 Sesiones Múltiples

**Pasos:**

1. Login en navegador 1 (Chrome)
2. Login con mismo usuario en navegador 2 (Firefox)
3. Verificar ambos funcionan

**Resultado esperado:**

- ✓ Ambas sesiones activas (comportamiento por defecto)
- ✓ Sin conflictos

---

## 6. PRUEBAS DE UI/UX

### 6.1 Responsividad

**Dispositivos a probar:**

- Desktop (1920x1080)
- Tablet (768x1024)
- Mobile (375x667)

**Elementos a verificar:**

- ✓ Sidebar se oculta en móvil
- ✓ Tablas scrolleables
- ✓ Formularios adaptables
- ✓ Botones accesibles

### 6.2 Navegación

**Pruebas:**

- ✓ Breadcrumbs funcionan correctamente
- ✓ Links del sidebar activos (highlight)
- ✓ Navegación entre secciones fluida

### 6.3 Mensajes y Alertas

**Verificar:**

- ✓ SweetAlert2 para confirmaciones
- ✓ Toasts para notificaciones rápidas
- ✓ Alertas de Bootstrap para mensajes persistentes

---

## 7. PRUEBAS DE RENDIMIENTO

### 7.1 Carga de DataTables

**Pasos:**

1. Crear 100+ usuarios (script SQL)
2. Cargar página de usuarios

**Resultado esperado:**

- ✓ Tabla carga en < 2 segundos
- ✓ Paginación funcional
- ✓ Búsqueda instantánea

### 7.2 Consultas a Base de Datos

**Verificar logs:**

- ✓ Prepared statements usados
- ✓ Sin queries N+1
- ✓ Índices en columnas clave

---

## 8. CHECKLIST FINAL SPRINT 1

### Backend

- [x] Base de datos creada (16 tablas)
- [x] Conexión PDO funcionando
- [x] Modelos Usuario y Cliente implementados
- [x] AuthController completo
- [x] UsuarioController completo
- [x] Front Controller con routing

### Frontend

- [x] Vista de login responsive
- [x] Dashboards para 3 roles
- [x] Layouts (header, footer, sidebar)
- [x] Gestión de usuarios (CRUD completo)
- [x] CSS personalizado
- [x] JavaScript con utilidades

### Seguridad

- [x] Password hashing (bcrypt)
- [x] Prepared statements (PDO)
- [x] Validación de inputs
- [x] Control de sesiones
- [x] Verificación de roles

### Integración

- [x] Bootstrap 5 integrado
- [x] Font Awesome funcionando
- [x] DataTables en español
- [x] SweetAlert2 para alertas
- [x] Chart.js para gráficos

---

## 9. ERRORES COMUNES Y SOLUCIONES

### Error: "Call to undefined function password_hash()"

**Solución:** Verificar que PHP >= 5.5.0

### Error: "Access denied for user 'root'@'localhost'"

**Solución:** Verificar credenciales en `config/database.php`

### Error: "Table 'napanchita.usuarios' doesn't exist"

**Solución:** Ejecutar `database/schema_completo.sql`

### Error: "Headers already sent"

**Solución:** Verificar que no hay espacios antes de `<?php` en archivos

### Error 404 en assets

**Solución:** Verificar BASE_URL en `config/config.php`

---

## 10. PRÓXIMOS PASOS (Sprint 2)

Una vez completadas todas las pruebas del Sprint 1:

1. ✅ Implementar gestión de categorías
2. ✅ Implementar gestión de productos
3. ✅ Implementar gestión de combos
4. ✅ Crear catálogo visual de productos
5. ✅ Implementar búsqueda y filtros avanzados

---

**Fecha de prueba:** ******\_******
**Probado por:** ******\_******
**Resultado general:** ☐ APROBADO ☐ PENDIENTE ☐ RECHAZADO

**Observaciones:**

---

---

---
