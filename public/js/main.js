/**
 * JavaScript Principal - Página de Inicio
 * Maneja la navegación, animaciones e interactividad
 */

// Navegación móvil
document.addEventListener('DOMContentLoaded', function () {
  const navToggle = document.getElementById('navToggle');
  const navMenu = document.getElementById('navMenu');

  if (navToggle) {
    navToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
    });
  }

  // Smooth scroll para enlaces internos
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        navMenu.classList.remove('active');
      }
    });
  });

  // Animaciones al hacer scroll
  const observerOptions = {
    threshold: 0.2,
    rootMargin: '0px 0px -100px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, observerOptions);

  document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
  });

  // Cargar productos en la página principal
  cargarProductosPreview();

  // Header con efecto al scroll
  let lastScroll = 0;
  const header = document.getElementById('header');

  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
      header.style.boxShadow = '0 5px 20px rgba(0,0,0,0.2)';
    } else {
      header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    }

    lastScroll = currentScroll;
  });
});

/**
 * Cargar productos para preview en la página principal
 */
async function cargarProductosPreview() {
  try {
    const response = await fetch('index.php?action=api_productos');
    const productos = await response.json();

    const container = document.getElementById('menuContainer');
    if (!container) return;

    // Mostrar solo los primeros 6 productos
    const productosPreview = productos.slice(0, 6);

    container.innerHTML = productosPreview.map(producto => `
            <div class="producto-card animate-on-scroll">
                <div class="producto-image">
                    ${getProductoIcon(producto.categoria_nombre)}
                </div>
                <div class="producto-body">
                    <h3 class="producto-title">${producto.nombre}</h3>
                    <p class="producto-descripcion">${producto.descripcion}</p>
                    <p class="producto-precio">Bs. ${parseFloat(producto.precio).toFixed(2)}</p>
                </div>
            </div>
        `).join('');

    // Re-observar los nuevos elementos
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
          }
        });
      }, { threshold: 0.2 });
      observer.observe(el);
    });

  } catch (error) {
    console.error('Error al cargar productos:', error);
  }
}

/**
 * Obtener icono según categoría
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
