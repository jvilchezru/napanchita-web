# 🎯 GUÍA RÁPIDA DE USO - NAPANCHITA

## 🚀 Inicio Rápido (3 Pasos)

### 1️⃣ Preparar Base de Datos
```bash
# Abrir MySQL y ejecutar:
CREATE DATABASE napanchita_db;
```
Luego importar: `database/schema.sql`

### 2️⃣ Iniciar Servidor
```bash
cd napanchita
php -S localhost:8000
```

### 3️⃣ Abrir Navegador
```
http://localhost:8000
```

---

## 👤 CREDENCIALES DE ACCESO

### 🔑 Usuario Administrador
- **Email**: `admin@napanchita.com`
- **Contraseña**: `password`
- **Permisos**: 
  - ✅ Ver y gestionar todos los pedidos
  - ✅ Cambiar estado de pedidos
  - ✅ Gestionar productos (crear, editar)
  - ✅ Ver información de todos los clientes

### 🔑 Usuario Cliente
- **Email**: `juan@email.com`
- **Contraseña**: `password`
- **Permisos**:
  - ✅ Ver menú de productos
  - ✅ Agregar productos al carrito
  - ✅ Realizar pedidos
  - ✅ Ver historial de sus pedidos

---

## 🎮 Cómo Usar el Sistema

### Para Clientes:

1. **Registrarse** (si no tienes cuenta)
   - Ir a: Registro
   - Llenar formulario
   - Click en "Registrarse"

2. **Iniciar Sesión**
   - Ir a: Login
   - Ingresar email y contraseña
   - Click en "Ingresar"

3. **Explorar el Menú**
   - En el dashboard, ver todos los productos
   - Usar buscador para encontrar platos específicos

4. **Agregar al Carrito**
   - Click en "Agregar al Carrito" en cada producto
   - El contador del carrito se actualiza

5. **Ver Carrito**
   - Click en "Carrito" en el menú lateral
   - Modificar cantidades con +/-
   - Eliminar productos si es necesario

6. **Realizar Pedido**
   - Click en "Finalizar Pedido"
   - Completar dirección y teléfono
   - Click en "Confirmar Pedido"

7. **Seguimiento**
   - Ir a "Mis Pedidos"
   - Ver estado de cada pedido
   - Estados: Pendiente → Preparando → Enviado → Entregado

### Para Administradores:

1. **Ver Todos los Pedidos**
   - Click en "Gestionar Pedidos"
   - Ver lista completa con información de clientes

2. **Actualizar Estado**
   - Seleccionar nuevo estado en el dropdown
   - Se actualiza automáticamente

3. **Gestionar Productos**
   - Click en "Gestionar Productos"
   - Crear nuevos productos
   - Editar disponibilidad

---

## 🔍 URLs Importantes

| Función | URL |
|---------|-----|
| Home | `http://localhost:8000/` |
| Login | `http://localhost:8000/index.php?action=login` |
| Registro | `http://localhost:8000/index.php?action=registro` |
| Dashboard | `http://localhost:8000/index.php?action=dashboard` |
| Test Conexión | `http://localhost:8000/test_conexion.php` |

---

## 📱 Navegación Móvil

En dispositivos móviles:
- Click en el menú hamburguesa (☰) para abrir navegación
- El sidebar del dashboard se oculta automáticamente
- Todas las funciones están disponibles

---

## 🎨 Características Interactivas

### ✨ Animaciones
- Hero section con efecto fade-in
- Cards con efecto hover
- Smooth scrolling en enlaces
- Transiciones suaves en modales

### 🔔 Notificaciones
- Feedback al agregar productos
- Confirmación de pedidos
- Alertas de errores

### 💾 Persistencia
- El carrito se guarda en localStorage
- No se pierde al recargar la página
- Se limpia al confirmar pedido

---

## 🛠️ Solución de Problemas Rápidos

### El carrito no guarda productos
✅ Verificar que localStorage esté habilitado
✅ Abrir consola del navegador (F12) y revisar errores

### Los estilos no cargan
✅ Verificar que la ruta sea correcta
✅ Limpiar caché del navegador (Ctrl + F5)

### Error de conexión a BD
✅ Verificar que MySQL esté corriendo
✅ Ejecutar test_conexion.php
✅ Revisar credenciales en config/database.php

### No puedo iniciar sesión
✅ Verificar que se ejecutó database/schema.sql
✅ Usar las credenciales exactas (case-sensitive)
✅ Revisar consola del navegador por errores

---

## 📊 Datos de Ejemplo Incluidos

### Usuarios (2)
- 1 Administrador
- 1 Cliente

### Productos (7)
- Empanadas Salteñas - Bs. 8.50
- Pique Macho - Bs. 25.00
- Silpancho - Bs. 22.00
- Chicharrón de Cerdo - Bs. 28.00
- Api con Pastel - Bs. 6.00
- Refresco Natural - Bs. 5.00
- Helado de Canela - Bs. 7.00

### Categorías (4)
- Entradas
- Platos Principales
- Bebidas
- Postres

---

## 🎯 Flujo Completo de Ejemplo

1. **Abrir** `http://localhost:8000`
2. **Explorar** la landing page
3. **Click** en "Ingresar"
4. **Usar** credenciales de cliente
5. **Buscar** "pique" en el buscador
6. **Agregar** Pique Macho al carrito
7. **Agregar** más productos
8. **Ir** al carrito
9. **Modificar** cantidades
10. **Click** "Finalizar Pedido"
11. **Llenar** datos de entrega
12. **Confirmar** pedido
13. **Ir** a "Mis Pedidos"
14. **Ver** el pedido creado

### Como Admin:
15. **Cerrar** sesión
16. **Login** como admin
17. **Ir** a "Gestionar Pedidos"
18. **Ver** el pedido del cliente
19. **Cambiar** estado a "Preparando"
20. **Ver** actualización en tiempo real

---

## 💡 Tips y Trucos

### Para Desarrollo
- Usa las herramientas del navegador (F12)
- Revisa la pestaña Network para peticiones AJAX
- Usa la consola para ver errores JavaScript

### Para Pruebas
- Prueba en diferentes navegadores
- Cambia el tamaño de ventana para ver responsive
- Prueba con varios productos en el carrito

### Para Producción
- Cambia las contraseñas por defecto
- Modifica config/config.php a modo production
- Elimina test_conexion.php
- Configura SSL (HTTPS)

---

## 📞 Atajos de Teclado (Navegador)

- `F5` - Recargar página
- `Ctrl + F5` - Recargar sin caché
- `F12` - Abrir DevTools
- `Ctrl + Shift + I` - Abrir Inspector

---

## ✅ Checklist de Primera Vez

- [ ] Base de datos creada
- [ ] Script schema.sql ejecutado
- [ ] Servidor PHP iniciado
- [ ] test_conexion.php pasa todas las pruebas
- [ ] Página principal carga correctamente
- [ ] Login funciona con credenciales de prueba
- [ ] Productos se muestran en el dashboard
- [ ] Carrito funciona correctamente
- [ ] Se puede crear un pedido
- [ ] Admin puede ver todos los pedidos

---

## 🎓 Conceptos para Aprender

Si eres estudiante, este proyecto te enseña:
- ✅ Arquitectura MVC
- ✅ CRUD completo
- ✅ Autenticación y sesiones
- ✅ Base de datos relacionales
- ✅ Responsive design
- ✅ AJAX y JavaScript moderno
- ✅ Seguridad web básica

---

**¡Todo listo para empezar a usar Napanchita! 🍽️**

*Si algo no funciona, revisa README.md o INSTALACION.md*
