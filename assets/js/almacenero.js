document.addEventListener('DOMContentLoaded', function () {
    // Agregar producto al inventario
    const agregarProductoForm = document.getElementById('agregarProductoForm');
    if (agregarProductoForm) {
        agregarProductoForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const idProducto = document.getElementById('idProducto').value;
            const cantidad = parseInt(document.getElementById('cantidad').value);

            if (cantidad < 0) {
                alert('La cantidad no puede ser negativa.');
                return;
            }

            fetch('../controller/AlmaceneroController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=agregarProductoInventario&idProducto=${idProducto}&cantidad=${cantidad}`,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al agregar el producto al inventario:', error);
                alert('Error al agregar el producto al inventario.');
            });
        });
    }

    // Actualizar stock
    const updateStockButtons = document.querySelectorAll('.update-stock');
    updateStockButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idInventario = this.dataset.idInventario;
            const row = this.closest('tr');
            const cantidad = parseInt(row.querySelector('.stock-quantity').value);

            if (cantidad < 0) {
                alert('La cantidad no puede ser negativa.');
                return;
            }

            fetch('../controller/AlmaceneroController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=updateStock&idInventario=${idInventario}&cantidad=${cantidad}`,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al actualizar el stock:', error);
                alert('Error al actualizar el stock.');
            });
        });
    });

    // Eliminar producto del inventario
    const deleteFromInventoryButtons = document.querySelectorAll('.delete-from-inventory');
    deleteFromInventoryButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idInventario = this.dataset.idInventario;

            if (confirm('¿Estás seguro de que deseas eliminar este producto del inventario?')) {
                fetch('../controller/AlmaceneroController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=eliminarProductoInventario&idInventario=${idInventario}`,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar el producto del inventario:', error);
                    alert('Error al eliminar el producto del inventario.');
                });
            }
        });
    });

    // Marcar pedido como entregado
    const markAsDeliveredButtons = document.querySelectorAll('.mark-as-delivered');
    markAsDeliveredButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idPedido = this.dataset.idPedido;

            if (confirm('¿Estás seguro de que deseas marcar este pedido como entregado?')) {
                fetch('../controller/AlmaceneroController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=markAsDelivered&idPedido=${idPedido}`,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al marcar el pedido como entregado:', error);
                    alert('Error al marcar el pedido como entregado.');
                });
            }
        });
    });
});