document.addEventListener('DOMContentLoaded', function () {
    // Mostrar/Ocultar barra de búsqueda
    const searchToggle = document.getElementById('search-toggle');
    const searchBar = document.querySelector('.search-bar');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', function (e) {
            e.preventDefault();
            searchBar.style.display = searchBar.style.display === 'none' ? 'block' : 'none';
        });
    }

    // Añadir al carrito desde productos destacados, catálogo o favoritos
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    console.log('Botones Añadir al Carrito encontrados:', addToCartButtons.length);
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            console.log('Añadiendo al carrito, productId:', productId);

            fetch('../controller/ClienteController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=addToCart&productId=${productId}`,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Respuesta del servidor (addToCart):', response);
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos (addToCart):', data);
                if (data.success) {
                    alert(data.message);
                    updateCartBadge();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al añadir al carrito:', error);
                alert('Error al añadir al carrito.');
            });
        });
    });

    // Añadir a favoritos desde productos destacados o catálogo
    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');
    console.log('Botones Añadir a Favoritos encontrados:', addToWishlistButtons.length);
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;
            console.log('Añadiendo a favoritos, productId:', productId);

            fetch('../controller/ClienteController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=addToWishlist&productId=${productId}`,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Respuesta del servidor (addToWishlist):', response);
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos (addToWishlist):', data);
                if (data.success) {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al añadir a favoritos:', error);
                alert('Error al añadir a favoritos.');
            });
        });
    });

    // Actualizar cantidad en el carrito
    const quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            const idCarrito = this.closest('tr').dataset.idCarrito;
            const cantidad = parseInt(this.value);
            if (cantidad < 1) {
                alert('La cantidad debe ser al menos 1.');
                this.value = 1;
                return;
            }

            fetch('../controller/ClienteController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=updateCarritoCantidad&idCarrito=${idCarrito}&cantidad=${cantidad}`,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = input.closest('tr');
                    const precioUnitario = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('€', ''));
                    const subtotal = precioUnitario * cantidad;
                    row.querySelector('.subtotal').textContent = subtotal.toFixed(2) + '€';

                    let total = 0;
                    document.querySelectorAll('.subtotal').forEach(sub => {
                        total += parseFloat(sub.textContent.replace('€', ''));
                    });
                    document.getElementById('cart-total').textContent = total.toFixed(2) + '€';

                    updateCartBadge();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la cantidad.');
            });
        });
    });

    // Eliminar producto del carrito
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idCarrito = this.dataset.idCarrito;

            if (confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
                fetch('../controller/ClienteController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=removeFromCarrito&idCarrito=${idCarrito}`,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.closest('tr').remove();

                        let total = 0;
                        document.querySelectorAll('.subtotal').forEach(sub => {
                            total += parseFloat(sub.textContent.replace('€', ''));
                        });
                        document.getElementById('cart-total').textContent = total.toFixed(2) + '€';

                        updateCartBadge();

                        if (document.querySelectorAll('.cart-table tbody tr').length === 0) {
                            const cartSection = document.querySelector('.cart');
                            cartSection.innerHTML = `
                                <h2>Tu Carrito de Compras</h2>
                                <p>Tu carrito está vacío. ¡Añade algunos productos!</p>
                                <a href="cliente.php" class="btn btn-primary">Volver al Catálogo</a>
                            `;
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto.');
                });
            }
        });
    });

    // Eliminar producto de favoritos
    const removeFromFavoritesButtons = document.querySelectorAll('.remove-from-favorites');
    removeFromFavoritesButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idFavorito = this.dataset.idFavorito;

            if (confirm('¿Estás seguro de que deseas eliminar este producto de tus favoritos?')) {
                fetch('../controller/ClienteController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=removeFromFavoritos&idFavorito=${idFavorito}`,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.closest('.product-card').remove();

                        if (document.querySelectorAll('.products-grid .product-card').length === 0) {
                            const favoritesSection = document.querySelector('.favorites');
                            favoritesSection.innerHTML = `
                                <h2>Tus Favoritos</h2>
                                <p>No tienes productos en tu lista de favoritos.</p>
                                <a href="cliente.php" class="btn btn-primary">Volver al Catálogo</a>
                            `;
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar de favoritos.');
                });
            }
        });
    });

    // Actualizar información del perfil
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = new URLSearchParams();
            for (const pair of formData) {
                data.append(pair[0], pair[1]);
            }
            data.append('action', 'updateClienteInfo');

            fetch('../controller/ClienteController.php', {
                method: 'POST',
                body: data,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'perfil.php?message=' + encodeURIComponent(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la información.');
            });
        });
    }

    // Manejar el formulario de checkout
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        const direccionSelect = document.getElementById('direccion_id');
        const nuevaDireccionDiv = document.getElementById('nueva-direccion');

        direccionSelect.addEventListener('change', function () {
            if (this.value === 'nueva') {
                nuevaDireccionDiv.style.display = 'block';
            } else {
                nuevaDireccionDiv.style.display = 'none';
            }
        });

        checkoutForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const total = parseFloat(document.querySelector('.order-table tfoot td:last-child').textContent.replace('€', ''));
            formData.append('action', 'procesarPedido');
            formData.append('total', total);

            fetch('../controller/ClienteController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'perfil.php?message=' + encodeURIComponent('Pedido realizado con éxito. ID del pedido: ' + data.pedidoId);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el pedido.');
            });
        });
    }

    // Manejar el formulario de reseñas
    const reviewForms = document.querySelectorAll('form[class^="review-form-"]');
    reviewForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            const rating = this.querySelector('input[name="rating"]').value;
            const comment = this.querySelector('textarea[name="comment"]').value;

            fetch('../controller/ClienteController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=addReview&productId=${productId}&rating=${rating}&comment=${encodeURIComponent(comment)}`,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Recargar para mostrar la nueva reseña
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al enviar la reseña:', error);
                alert('Error al enviar la reseña.');
            });
        });
    });

    // Función para actualizar el badge del carrito
    function updateCartBadge() {
        fetch('../controller/ClienteController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=getCartCount',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.cart-icon .badge');
            if (badge) {
                badge.textContent = data;
            }
        })
        .catch(error => {
            console.error('Error al actualizar el badge del carrito:', error);
        });
    }
});