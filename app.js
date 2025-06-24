import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Inicializar todos los tooltips
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

// Inicializar todos los popovers
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

// Función para mostrar/ocultar loader
const showLoader = (show = true) => {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = show ? 'flex' : 'none';
    }
};

// Función para mostrar alertas personalizadas
const showAlert = (message, type = 'success') => {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;

    const alert = document.createElement('div');
    alert.className = `alert-custom alert-${type} animate-slide-in`;
    alert.innerHTML = message;

    alertContainer.appendChild(alert);

    setTimeout(() => {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 300);
    }, 3000);
};

// Función para confirmar acciones
const confirmAction = (message = '¿Está seguro de realizar esta acción?') => {
    return new Promise((resolve) => {
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const confirmBtn = document.getElementById('confirmActionBtn');
        const cancelBtn = document.getElementById('cancelActionBtn');
        const messageEl = document.getElementById('confirmMessage');

        if (messageEl) messageEl.textContent = message;

        const handleConfirm = () => {
            cleanup();
            resolve(true);
        };

        const handleCancel = () => {
            cleanup();
            resolve(false);
        };

        const cleanup = () => {
            modal.hide();
            confirmBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
        };

        confirmBtn.addEventListener('click', handleConfirm);
        cancelBtn.addEventListener('click', handleCancel);

        modal.show();
    });
};

// Función para formatear números como moneda
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN'
    }).format(amount);
};

// Función para formatear fechas
const formatDate = (date) => {
    return new Intl.DateTimeFormat('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
};

// Función para manejar formularios con AJAX
const handleForm = async (form, options = {}) => {
    try {
        const formData = new FormData(form);
        showLoader(true);

        const response = await fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            ...options
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message || 'Error en la solicitud');

        showAlert(data.message || 'Operación exitosa', 'success');
        return data;

    } catch (error) {
        showAlert(error.message, 'danger');
        throw error;
    } finally {
        showLoader(false);
    }
};

// Función para actualizar progress bars
const updateProgress = (elementId, value) => {
    const progressBar = document.querySelector(`#${elementId} .progress-bar-custom`);
    if (progressBar) {
        progressBar.style.width = `${value}%`;
        progressBar.setAttribute('aria-valuenow', value);
    }
};

// Exportar funciones para uso global
window.showLoader = showLoader;
window.showAlert = showAlert;
window.confirmAction = confirmAction;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.handleForm = handleForm;
window.updateProgress = updateProgress;

// Event Listeners globales
document.addEventListener('DOMContentLoaded', () => {
    // Manejar formularios con AJAX por defecto
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                await handleForm(form);
                if (form.dataset.reset === 'true') form.reset();
            } catch (error) {
                console.error('Error en el formulario:', error);
            }
        });
    });

    // Inicializar efectos hover
    document.querySelectorAll('.hover-shadow, .hover-grow').forEach(element => {
        element.addEventListener('mouseenter', () => {
            element.style.transform = element.classList.contains('hover-shadow') ? 
                'translateY(-2px)' : 'scale(1.02)';
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'none';
        });
    });
});
