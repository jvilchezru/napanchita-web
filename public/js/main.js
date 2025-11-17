/**
 * MAIN.JS - Funciones JavaScript Globales
 * Sistema de Gestión Napanchita
 */

// Configuración global
const APP_CONFIG = {
    baseUrl: window.location.origin + '/napanchita-web/',
    apiUrl: window.location.origin + '/napanchita-web/api/',
    debug: true
};

// Utilidades generales
const Utils = {
    /**
     * Formatear precio en soles
     */
    formatPrice: (price) => {
        return 'S/ ' + parseFloat(price).toFixed(2);
    },

    /**
     * Formatear fecha
     */
    formatDate: (date, includeTime = false) => {
        const d = new Date(date);
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };
        
        if (includeTime) {
            options.hour = '2-digit';
            options.minute = '2-digit';
        }
        
        return d.toLocaleDateString('es-PE', options);
    },

    /**
     * Validar email
     */
    validateEmail: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    /**
     * Validar teléfono peruano
     */
    validatePhone: (phone) => {
        const re = /^9\d{8}$/;
        return re.test(phone);
    },

    /**
     * Sanitizar input
     */
    sanitizeInput: (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    /**
     * Mostrar loading spinner
     */
    showLoading: (message = 'Cargando...') => {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    /**
     * Ocultar loading
     */
    hideLoading: () => {
        Swal.close();
    },

    /**
     * Mostrar toast notification
     */
    showToast: (message, type = 'success') => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    },

    /**
     * Confirmar acción
     */
    confirm: (title, text, confirmText = 'Sí, continuar') => {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancelar'
        });
    },

    /**
     * Log de debug
     */
    log: (message, data = null) => {
        if (APP_CONFIG.debug) {
            console.log(`[Napanchita] ${message}`, data);
        }
    },

    /**
     * Error log
     */
    error: (message, error = null) => {
        console.error(`[Napanchita Error] ${message}`, error);
    }
};

// Clase AJAX para peticiones
class AjaxHandler {
    /**
     * Realizar petición GET
     */
    static get(url, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;

        return fetch(fullUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            Utils.error('Error en petición GET', error);
            throw error;
        });
    }

    /**
     * Realizar petición POST
     */
    static post(url, data = {}) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            Utils.error('Error en petición POST', error);
            throw error;
        });
    }

    /**
     * Subir archivo
     */
    static uploadFile(url, formData) {
        return fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            Utils.error('Error en subida de archivo', error);
            throw error;
        });
    }
}

// Configuración de DataTables en español
if (typeof $.fn.dataTable !== 'undefined') {
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
    });
}

// Validación de formularios con Bootstrap
(function() {
    'use strict';
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation');
    
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();

// Auto-completar inputs de búsqueda
const AutoComplete = {
    clientes: (inputElement, callback) => {
        $(inputElement).autocomplete({
            source: (request, response) => {
                AjaxHandler.get(APP_CONFIG.baseUrl + 'index.php?action=clientes_buscar', {
                    q: request.term
                })
                .then(data => {
                    response(data.map(cliente => ({
                        label: `${cliente.nombre} - ${cliente.telefono}`,
                        value: cliente.nombre,
                        id: cliente.id_cliente
                    })));
                })
                .catch(error => {
                    response([]);
                });
            },
            minLength: 2,
            select: (event, ui) => {
                if (callback) callback(ui.item);
            }
        });
    },

    productos: (inputElement, callback) => {
        $(inputElement).autocomplete({
            source: (request, response) => {
                AjaxHandler.get(APP_CONFIG.baseUrl + 'index.php?action=productos_buscar', {
                    q: request.term
                })
                .then(data => {
                    response(data.map(producto => ({
                        label: `${producto.nombre} - S/ ${producto.precio}`,
                        value: producto.nombre,
                        id: producto.id_producto,
                        precio: producto.precio
                    })));
                })
                .catch(error => {
                    response([]);
                });
            },
            minLength: 2,
            select: (event, ui) => {
                if (callback) callback(ui.item);
            }
        });
    }
};

// Manejo de impresión de recibos
const PrintHandler = {
    pedido: (idPedido) => {
        window.open(
            APP_CONFIG.baseUrl + `index.php?action=pedidos_imprimir&id=${idPedido}`,
            'PrintWindow',
            'width=800,height=600'
        );
    },

    venta: (idVenta) => {
        window.open(
            APP_CONFIG.baseUrl + `index.php?action=ventas_imprimir&id=${idVenta}`,
            'PrintWindow',
            'width=800,height=600'
        );
    },

    reporte: () => {
        window.print();
    }
};

// Carrito de compras (para pedidos)
class Cart {
    constructor() {
        this.items = [];
        this.subtotal = 0;
        this.descuento = 0;
        this.total = 0;
    }

    addItem(item) {
        const existingItem = this.items.find(i => i.id === item.id);
        
        if (existingItem) {
            existingItem.cantidad += item.cantidad || 1;
        } else {
            this.items.push({
                id: item.id,
                nombre: item.nombre,
                precio: parseFloat(item.precio),
                cantidad: item.cantidad || 1
            });
        }
        
        this.calculate();
    }

    removeItem(itemId) {
        this.items = this.items.filter(i => i.id !== itemId);
        this.calculate();
    }

    updateQuantity(itemId, cantidad) {
        const item = this.items.find(i => i.id === itemId);
        if (item) {
            item.cantidad = parseInt(cantidad);
            if (item.cantidad <= 0) {
                this.removeItem(itemId);
            } else {
                this.calculate();
            }
        }
    }

    calculate() {
        this.subtotal = this.items.reduce((sum, item) => {
            return sum + (item.precio * item.cantidad);
        }, 0);
        
        this.total = this.subtotal - this.descuento;
    }

    clear() {
        this.items = [];
        this.subtotal = 0;
        this.descuento = 0;
        this.total = 0;
    }

    getItems() {
        return this.items;
    }

    getTotal() {
        return this.total;
    }
}

// Timer para sesión
class SessionTimer {
    constructor(timeoutMinutes = 60) {
        this.timeout = timeoutMinutes * 60 * 1000;
        this.warningTime = 5 * 60 * 1000; // 5 minutos antes
        this.timerId = null;
        this.warningId = null;
        
        this.start();
        this.setupActivityListeners();
    }

    start() {
        this.reset();
    }

    reset() {
        clearTimeout(this.timerId);
        clearTimeout(this.warningId);
        
        // Advertencia 5 minutos antes
        this.warningId = setTimeout(() => {
            this.showWarning();
        }, this.timeout - this.warningTime);
        
        // Timeout final
        this.timerId = setTimeout(() => {
            this.logout();
        }, this.timeout);
    }

    showWarning() {
        Swal.fire({
            title: 'Sesión por expirar',
            text: 'Su sesión expirará en 5 minutos por inactividad',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Mantener sesión',
            cancelButtonText: 'Cerrar sesión'
        }).then((result) => {
            if (result.isConfirmed) {
                this.reset();
            } else {
                this.logout();
            }
        });
    }

    logout() {
        window.location.href = APP_CONFIG.baseUrl + 'index.php?action=logout&timeout=1';
    }

    setupActivityListeners() {
        const events = ['mousedown', 'keydown', 'scroll', 'touchstart'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.reset();
            }, true);
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    Utils.log('Sistema Napanchita iniciado');
    
    // Iniciar timer de sesión si el usuario está autenticado
    if (document.body.classList.contains('logged-in')) {
        new SessionTimer(60); // 60 minutos
    }
    
    // Tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Confirmar links de eliminación
    document.querySelectorAll('a.confirm-delete').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            
            Utils.confirm(
                '¿Está seguro?',
                'Esta acción no se puede deshacer',
                'Sí, eliminar'
            ).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});

// Exportar para uso global
window.Utils = Utils;
window.AjaxHandler = AjaxHandler;
window.AutoComplete = AutoComplete;
window.PrintHandler = PrintHandler;
window.Cart = Cart;
window.SessionTimer = SessionTimer;
window.APP_CONFIG = APP_CONFIG;
