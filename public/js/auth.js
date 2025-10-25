/**
 * JavaScript de Autenticación
 * Maneja login y registro con validaciones
 */

document.addEventListener('DOMContentLoaded', function () {
  // Formulario de Login - Ahora usa submit tradicional, no AJAX
  // El formulario se envía directamente a index.php?action=login

  /* Login ya no usa JavaScript, se maneja con PHP
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      // ... código AJAX comentado
    });
  }
  */

  // Formulario de Registro
  const registroForm = document.getElementById('registroForm');
  if (registroForm) {
    registroForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const nombre = document.getElementById('nombre').value;
      const email = document.getElementById('email').value;
      const telefono = document.getElementById('telefono').value;
      const direccion = document.getElementById('direccion').value;
      const password = document.getElementById('password').value;
      const password2 = document.getElementById('password2').value;
      const errorDiv = document.getElementById('errorMessage');
      const successDiv = document.getElementById('successMessage');

      // Validaciones
      if (password !== password2) {
        mostrarError(errorDiv, 'Las contraseñas no coinciden');
        return;
      }

      if (password.length < 6) {
        mostrarError(errorDiv, 'La contraseña debe tener al menos 6 caracteres');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('email', email);
        formData.append('telefono', telefono);
        formData.append('direccion', direccion);
        formData.append('password', password);

        const response = await fetch('index.php?action=registro', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          mostrarExito(successDiv, 'Registro exitoso. Redirigiendo al login...');
          setTimeout(() => {
            window.location.href = 'index.php?action=login';
          }, 2000);
        } else {
          mostrarError(errorDiv, result.message || 'Error al registrar');
        }
      } catch (error) {
        mostrarError(errorDiv, 'Error de conexión. Intente nuevamente.');
      }
    });
  }
});

/**
 * Mostrar mensaje de error
 */
function mostrarError(elemento, mensaje) {
  elemento.textContent = mensaje;
  elemento.style.display = 'block';
  setTimeout(() => {
    elemento.style.display = 'none';
  }, 5000);
}

/**
 * Mostrar mensaje de éxito
 */
function mostrarExito(elemento, mensaje) {
  elemento.textContent = mensaje;
  elemento.style.display = 'block';
}
