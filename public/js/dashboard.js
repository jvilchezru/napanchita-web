/**
 * JavaScript del Dashboard
 * Maneja el carrito, pedidos y funcionalidad del sistema
 */

// Estado del carrito
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
let productos = [];

document.addEventListener('DOMContentLoaded', function () {
  // Inicializar
  cargarProductos();
  actualizarCarrito();

  // Navegación del sidebar
  document.querySelectorAll('.sidebar-menu a').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const section = link.dataset.section;
      cambiarSeccion(section);

      // Marcar como activo
      document.querySelectorAll('.sidebar-menu a').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });

  // Buscador de productos
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const termino = e.target.value.toLowerCase();
      filtrarProductos(termino);
    });
  }

  // Modal de pedido
  const btnFinalizarPedido = document.getElementById('btnFinalizarPedido');
  if (btnFinalizarPedido) {
    btnFinalizarPedido.addEventListener('click', () => {
      if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
      }
      abrirModal('modalPedido');
    });
  }

  // Formulario de pedido
  const formPedido = document.getElementById('formPedido');
  if (formPedido) {
    formPedido.addEventListener('submit', (e) => {
      e.preventDefault();
      realizarPedido();
    });
  }

  // Botón nuevo producto (Admin)
  const btnNuevoProducto = document.getElementById('btnNuevoProducto');
  if (btnNuevoProducto) {
    btnNuevoProducto.addEventListener('click', () => {
      abrirModalProducto();
    });
  }

  // Formulario de producto (Admin)
  const formProducto = document.getElementById('formProducto');
  if (formProducto) {
    formProducto.addEventListener('submit', (e) => {
      e.preventDefault();
      guardarProducto();
    });
  }

  // Cerrar modales
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
      cerrarModales();
    });
  });

  // Cerrar modal al hacer clic fuera
  window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
      cerrarModales();
    }
  });
});

/**
 * Cambiar sección activa del dashboard
 */
function cambiarSeccion(seccion) {
  document.querySelectorAll('.dashboard-section').forEach(s => {
    s.classList.remove('active');
  });

  const seccionElement = document.getElementById(`section-${seccion}`);
  if (seccionElement) {
    seccionElement.classList.add('active');

    // Cargar datos según la sección
    if (seccion === 'pedidos') {
      cargarMisPedidos();
    } else if (seccion === 'admin-pedidos') {
      cargarTodosPedidos();
    } else if (seccion === 'admin-productos') {
      cargarAdminProductos();
    }
  }
}

/**
 * Cargar productos del menú
 */
async function cargarProductos() {
  try {
    const response = await fetch('index.php?action=api_productos');
    productos = await response.json();
    mostrarProductos(productos);
  } catch (error) {
    console.error('Error al cargar productos:', error);
  }
}

/**
 * Mostrar productos en el grid
 */
function mostrarProductos(productosArray) {
  const container = document.getElementById('productosContainer');
  if (!container) return;

  container.innerHTML = productosArray.map(producto => `
        <div class="producto-card">
            <div class="producto-image">
                ${getProductoIcon(producto.categoria_nombre)}
            </div>
            <div class="producto-body">
                <span class="badge">${producto.categoria_nombre}</span>
                <h3 class="producto-title">${producto.nombre}</h3>
                <p class="producto-descripcion">${producto.descripcion}</p>
                <p class="producto-precio">Bs. ${parseFloat(producto.precio).toFixed(2)}</p>
                ${usuarioRol !== 'admin' ? `
                <button class="btn btn-primary btn-block" onclick="agregarAlCarrito(${producto.id})">
                    Agregar al Carrito
                </button>
                ` : ''}
            </div>
        </div>
    `).join('');
}

/**
 * Filtrar productos
 */
function filtrarProductos(termino) {
  const productosFiltrados = productos.filter(p =>
    p.nombre.toLowerCase().includes(termino) ||
    p.descripcion.toLowerCase().includes(termino)
  );
  mostrarProductos(productosFiltrados);
}

/**
 * Agregar producto al carrito
 */
function agregarAlCarrito(productoId) {
  const producto = productos.find(p => p.id == productoId);
  if (!producto) return;

  const itemExistente = carrito.find(item => item.producto_id == productoId);

  if (itemExistente) {
    itemExistente.cantidad++;
  } else {
    carrito.push({
      producto_id: producto.id,
      nombre: producto.nombre,
      precio: parseFloat(producto.precio),
      cantidad: 1,
      subtotal: parseFloat(producto.precio)
    });
  }

  actualizarCarrito();
  guardarCarrito();

  // Animación de feedback
  mostrarNotificacion('Producto agregado al carrito');
}

/**
 * Actualizar vista del carrito
 */
function actualizarCarrito() {
  // Actualizar contador
  const cartCount = document.getElementById('cartCount');
  if (cartCount) {
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    cartCount.textContent = totalItems;
  }

  // Actualizar items del carrito
  const carritoItems = document.getElementById('carritoItems');
  if (carritoItems) {
    if (carrito.length === 0) {
      carritoItems.innerHTML = '<p class="text-center">El carrito está vacío</p>';
    } else {
      carritoItems.innerHTML = carrito.map((item, index) => `
                <div class="carrito-item">
                    <div class="carrito-item-image">
                        ${getProductoIcon()}
                    </div>
                    <div class="carrito-item-details">
                        <h3>${item.nombre}</h3>
                        <p class="producto-precio">Bs. ${item.precio.toFixed(2)}</p>
                    </div>
                    <div class="carrito-item-actions">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="cambiarCantidad(${index}, -1)">-</button>
                            <span>${item.cantidad}</span>
                            <button class="qty-btn" onclick="cambiarCantidad(${index}, 1)">+</button>
                        </div>
                        <button class="btn btn-small btn-secondary" onclick="eliminarDelCarrito(${index})">🗑️</button>
                    </div>
                </div>
            `).join('');
    }
  }

  // Actualizar total
  const carritoTotal = document.getElementById('carritoTotal');
  if (carritoTotal) {
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    carritoTotal.textContent = total.toFixed(2);
  }
}

/**
 * Cambiar cantidad de producto en el carrito
 */
function cambiarCantidad(index, delta) {
  carrito[index].cantidad += delta;

  if (carrito[index].cantidad <= 0) {
    carrito.splice(index, 1);
  }

  actualizarCarrito();
  guardarCarrito();
}

/**
 * Eliminar producto del carrito
 */
function eliminarDelCarrito(index) {
  carrito.splice(index, 1);
  actualizarCarrito();
  guardarCarrito();
}

/**
 * Guardar carrito en localStorage
 */
function guardarCarrito() {
  localStorage.setItem('carrito', JSON.stringify(carrito));
}

/**
 * Realizar pedido
 */
async function realizarPedido() {
  const direccion = document.getElementById('pedidoDireccion').value;
  const telefono = document.getElementById('pedidoTelefono').value;
  const notas = document.getElementById('pedidoNotas').value;

  const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

  const items = carrito.map(item => ({
    producto_id: item.producto_id,
    cantidad: item.cantidad,
    precio: item.precio,
    subtotal: item.precio * item.cantidad
  }));

  const data = {
    total: total,
    direccion: direccion,
    telefono: telefono,
    notas: notas,
    items: items
  };

  try {
    const response = await fetch('index.php?action=api_crear_pedido', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();

    if (result.success) {
      mostrarNotificacion('Pedido realizado exitosamente');
      carrito = [];
      guardarCarrito();
      actualizarCarrito();
      cerrarModales();
      cambiarSeccion('pedidos');
    } else {
      alert('Error al realizar el pedido: ' + result.message);
    }
  } catch (error) {
    alert('Error de conexión. Intente nuevamente.');
    console.error(error);
  }
}

/**
 * Cargar mis pedidos
 */
async function cargarMisPedidos() {
  try {
    const response = await fetch('index.php?action=api_mis_pedidos');
    const pedidos = await response.json();

    const container = document.getElementById('pedidosContainer');
    if (!container) return;

    if (pedidos.length === 0) {
      container.innerHTML = '<p class="text-center">No tienes pedidos aún</p>';
      return;
    }

    container.innerHTML = pedidos.map(pedido => {
      const tiempoTranscurrido = calcularTiempoTranscurrido(pedido.fecha_pedido);
      const puedeCancel = pedido.estado === 'pendiente';

      return `
            <div class="pedido-card">
                <div class="pedido-header">
                    <div>
                        <h3>Pedido #${pedido.id}</h3>
                        <p>${new Date(pedido.fecha_pedido).toLocaleString()}</p>
                        <p class="tiempo-transcurrido">⏱️ ${tiempoTranscurrido}</p>
                    </div>
                    <span class="pedido-estado estado-${pedido.estado}">${pedido.estado}</span>
                </div>
                <div class="pedido-body">
                    <p><strong>Total:</strong> Bs. ${parseFloat(pedido.total).toFixed(2)}</p>
                    <p><strong>Dirección:</strong> ${pedido.direccion_entrega}</p>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-small btn-primary" onclick="verDetallePedido(${pedido.id})">
                            Ver Detalles
                        </button>
                        ${puedeCancel ? `
                        <button class="btn btn-small btn-secondary" onclick="cancelarPedido(${pedido.id})">
                            Cancelar Pedido
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
  } catch (error) {
    console.error('Error al cargar pedidos:', error);
  }
}

/**
 * Cargar todos los pedidos (admin)
 */
async function cargarTodosPedidos() {
  try {
    const response = await fetch('index.php?action=api_todos_pedidos');
    const pedidos = await response.json();

    const container = document.getElementById('adminPedidosContainer');
    if (!container) return;

    if (pedidos.length === 0) {
      container.innerHTML = '<p class="text-center">No hay pedidos</p>';
      return;
    }

    container.innerHTML = pedidos.map(pedido => `
            <div class="pedido-card">
                <div class="pedido-header">
                    <div>
                        <h3>Pedido #${pedido.id} - ${pedido.cliente_nombre}</h3>
                        <p>${new Date(pedido.fecha_pedido).toLocaleString()}</p>
                        <p>Email: ${pedido.email}</p>
                    </div>
                    <select class="pedido-estado estado-${pedido.estado}" 
                            onchange="actualizarEstadoPedido(${pedido.id}, this.value)">
                        <option value="pendiente" ${pedido.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                        <option value="preparando" ${pedido.estado === 'preparando' ? 'selected' : ''}>Preparando</option>
                        <option value="enviado" ${pedido.estado === 'enviado' ? 'selected' : ''}>Enviado</option>
                        <option value="entregado" ${pedido.estado === 'entregado' ? 'selected' : ''}>Entregado</option>
                        <option value="finalizado" ${pedido.estado === 'finalizado' ? 'selected' : ''}>Finalizado</option>
                        <option value="cancelado" ${pedido.estado === 'cancelado' ? 'selected' : ''}>Cancelado</option>
                    </select>
                </div>
                <div class="pedido-body">
                    <p><strong>Total:</strong> Bs. ${parseFloat(pedido.total).toFixed(2)}</p>
                    <p><strong>Dirección:</strong> ${pedido.direccion_entrega}</p>
                    <p><strong>Teléfono:</strong> ${pedido.telefono_contacto}</p>
                </div>
            </div>
        `).join('');
  } catch (error) {
    console.error('Error al cargar pedidos:', error);
  }
}

/**
 * Actualizar estado del pedido (admin)
 */
async function actualizarEstadoPedido(pedidoId, nuevoEstado) {
  try {
    const formData = new FormData();
    formData.append('id', pedidoId);
    formData.append('estado', nuevoEstado);

    const response = await fetch('index.php?action=api_actualizar_estado', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      mostrarNotificacion('Estado actualizado');
    } else {
      alert('Error al actualizar estado');
    }
  } catch (error) {
    alert('Error de conexión');
    console.error(error);
  }
}

/**
 * Utilidades
 */
function getProductoIcon(categoria) {
  const iconos = {
    'Entradas': '🥟',
    'Platos Principales': '🍖',
    'Bebidas': '🥤',
    'Postres': '🍮'
  };
  return iconos[categoria] || '🍽️';
}

function abrirModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
  }
}

function cerrarModales() {
  document.querySelectorAll('.modal').forEach(modal => {
    modal.classList.remove('active');
  });
}

function mostrarNotificacion(mensaje) {
  // Crear elemento de notificación
  const notif = document.createElement('div');
  notif.className = 'notificacion';
  notif.textContent = mensaje;
  notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success);
        color: white;
        padding: 1rem 2rem;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;

  document.body.appendChild(notif);

  setTimeout(() => {
    notif.style.animation = 'slideOut 0.3s ease-in';
    setTimeout(() => notif.remove(), 300);
  }, 3000);
}

/**
 * ========================================
 * GESTIÓN DE PRODUCTOS (ADMIN)
 * ========================================
 */

/**
 * Cargar productos para administración
 */
async function cargarAdminProductos() {
  try {
    const response = await fetch('index.php?action=listarProductos');
    const productosData = await response.json();
    mostrarAdminProductos(productosData);
  } catch (error) {
    console.error('Error al cargar productos:', error);
  }
}

/**
 * Mostrar productos en tabla de administración
 */
function mostrarAdminProductos(productosArray) {
  const container = document.getElementById('adminProductosContainer');
  if (!container) return;

  container.innerHTML = `
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th>Categoría</th>
          <th>Disponible</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        ${productosArray.map(producto => `
          <tr>
            <td>${producto.id}</td>
            <td>${producto.nombre}</td>
            <td>${producto.descripcion}</td>
            <td>Bs. ${parseFloat(producto.precio).toFixed(2)}</td>
            <td>${producto.categoria_nombre}</td>
            <td>${producto.disponible == 1 ? '✅ Sí' : '❌ No'}</td>
            <td>
              <button class="btn btn-sm btn-secondary" onclick="editarProducto(${producto.id})">
                ✏️ Editar
              </button>
            </td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  `;
}

/**
 * Abrir modal de producto (crear nuevo)
 */
function abrirModalProducto() {
  document.getElementById('modalProductoTitulo').textContent = 'Nuevo Producto';
  document.getElementById('formProducto').reset();
  document.getElementById('productoId').value = '';
  document.getElementById('productoDisponible').value = '1';
  abrirModal('modalProducto');
}

/**
 * Editar producto existente
 */
async function editarProducto(productoId) {
  try {
    const response = await fetch(`index.php?action=obtenerProducto&id=${productoId}`);
    const producto = await response.json();

    document.getElementById('modalProductoTitulo').textContent = 'Editar Producto';
    document.getElementById('productoId').value = producto.id;
    document.getElementById('productoNombre').value = producto.nombre;
    document.getElementById('productoDescripcion').value = producto.descripcion;
    document.getElementById('productoPrecio').value = producto.precio;
    document.getElementById('productoCategoria').value = producto.categoria_id;
    document.getElementById('productoDisponible').value = producto.disponible;

    abrirModal('modalProducto');
  } catch (error) {
    console.error('Error al cargar producto:', error);
    alert('Error al cargar el producto');
  }
}

/**
 * Guardar producto (crear o actualizar)
 */
async function guardarProducto() {
  const id = document.getElementById('productoId').value;
  const nombre = document.getElementById('productoNombre').value;
  const descripcion = document.getElementById('productoDescripcion').value;
  const precio = document.getElementById('productoPrecio').value;
  const categoria_id = document.getElementById('productoCategoria').value;
  const disponible = document.getElementById('productoDisponible').value;

  const action = id ? 'actualizarProducto' : 'crearProducto';

  const formData = new FormData();
  if (id) formData.append('id', id);
  formData.append('nombre', nombre);
  formData.append('descripcion', descripcion);
  formData.append('precio', precio);
  formData.append('categoria_id', categoria_id);
  formData.append('disponible', disponible);

  try {
    const response = await fetch(`index.php?action=${action}`, {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      mostrarNotificacion(result.message);
      cerrarModales();
      cargarAdminProductos();
      cargarProductos(); // Actualizar también la lista del menú
    } else {
      alert(result.message);
    }
  } catch (error) {
    console.error('Error al guardar producto:', error);
    alert('Error al guardar el producto');
  }
}

/**
 * Calcular tiempo transcurrido desde la fecha del pedido
 */
function calcularTiempoTranscurrido(fechaPedido) {
  const ahora = new Date();
  const fecha = new Date(fechaPedido);
  const diferencia = ahora - fecha; // en milisegundos

  const minutos = Math.floor(diferencia / 60000);
  const horas = Math.floor(minutos / 60);
  const dias = Math.floor(horas / 24);

  if (dias > 0) {
    return `Hace ${dias} día${dias > 1 ? 's' : ''}`;
  } else if (horas > 0) {
    return `Hace ${horas} hora${horas > 1 ? 's' : ''}`;
  } else if (minutos > 0) {
    return `Hace ${minutos} minuto${minutos > 1 ? 's' : ''}`;
  } else {
    return 'Hace un momento';
  }
}

/**
 * Cancelar pedido (cliente)
 */
async function cancelarPedido(pedidoId) {
  if (!confirm('¿Estás seguro de que deseas cancelar este pedido?')) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('id', pedidoId);
    formData.append('estado', 'cancelado');

    const response = await fetch('index.php?action=api_actualizar_estado', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      mostrarNotificacion('Pedido cancelado exitosamente');
      cargarMisPedidos();
    } else {
      alert(result.message || 'Error al cancelar el pedido');
    }
  } catch (error) {
    console.error('Error al cancelar pedido:', error);
    alert('Error al cancelar el pedido');
  }
}

/**
 * Ver detalle del pedido (modal)
 */
async function verDetallePedido(pedidoId) {
  try {
    const response = await fetch(`index.php?action=api_detalle_pedido&id=${pedidoId}`);
    const pedido = await response.json();

    if (pedido.error) {
      alert(pedido.error);
      return;
    }

    // Crear modal dinámico
    const modalHtml = `
      <div id="modalDetallePedido" class="modal active">
        <div class="modal-content">
          <span class="modal-close" onclick="cerrarModales()">&times;</span>
          <h2>Detalle del Pedido #${pedido.id}</h2>
          <div class="detalle-pedido-info">
            <p><strong>Estado:</strong> <span class="pedido-estado estado-${pedido.estado}">${pedido.estado}</span></p>
            <p><strong>Fecha:</strong> ${new Date(pedido.fecha_pedido).toLocaleString()}</p>
            <p><strong>Dirección:</strong> ${pedido.direccion_entrega}</p>
            <p><strong>Teléfono:</strong> ${pedido.telefono_contacto}</p>
            ${pedido.notas ? `<p><strong>Notas:</strong> ${pedido.notas}</p>` : ''}
          </div>
          <h3>Productos</h3>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              ${pedido.detalles.map(detalle => `
                <tr>
                  <td>${detalle.producto_nombre}</td>
                  <td>Bs. ${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                  <td>${detalle.cantidad}</td>
                  <td>Bs. ${parseFloat(detalle.subtotal).toFixed(2)}</td>
                </tr>
              `).join('')}
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3"><strong>Total:</strong></td>
                <td><strong>Bs. ${parseFloat(pedido.total).toFixed(2)}</strong></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    `;

    // Remover modal anterior si existe
    const modalExistente = document.getElementById('modalDetallePedido');
    if (modalExistente) {
      modalExistente.remove();
    }

    // Agregar modal al body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

  } catch (error) {
    console.error('Error al cargar detalle del pedido:', error);
    alert('Error al cargar el detalle del pedido');
  }
}


