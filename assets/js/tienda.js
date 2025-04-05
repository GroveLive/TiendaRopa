// assets/js/tienda.js

document.addEventListener('DOMContentLoaded', () => {
    // Seleccionar elementos del DOM
    const searchToggle = document.getElementById('search-toggle');
    const searchBar = document.querySelector('.search-bar');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');

    // Mostrar/ocultar barra de búsqueda
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', (e) => {
            e.preventDefault();
            searchBar.style.display = searchBar.style.display === 'none' ? 'block' : 'none';
        });
    }

    // Efecto de scroll para el header
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Agregar al carrito (simulación)
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            alert(`Producto ${productId} agregado al carrito`);
            // Aquí puedes hacer una solicitud AJAX para agregar al carrito
        });
    });

    // Agregar a la lista de deseos (simulación)
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            alert(`Producto ${productId} agregado a la lista de deseos`);
            // Aquí puedes hacer una solicitud AJAX para agregar a la lista de deseos
        });
    });
});