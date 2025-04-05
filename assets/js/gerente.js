// assets/js/gerente.js

document.addEventListener('DOMContentLoaded', () => {
    const sidebarMenuItems = document.querySelectorAll('.sidebar-menu li:not(.logout)');
    const logoutButton = document.querySelector('.sidebar-menu .logout');
    const tabContents = document.querySelectorAll('.tab-content');

    // Cambio de pestañas desde el sidebar
    sidebarMenuItems.forEach(item => {
        item.addEventListener('click', () => {
            const tab = item.getAttribute('data-tab');

            // Actualizar URL sin recargar la página
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);

            // Cambiar pestaña activa
            sidebarMenuItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            // Mostrar contenido correspondiente
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === tab) {
                    content.classList.add('active');
                }
            });
        });
    });

    // Acción de cerrar sesión (simulación)
    if (logoutButton) {
        logoutButton.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                window.location.href = '?controller=logout';
            }
        });
    }
});