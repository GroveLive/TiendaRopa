document.addEventListener('DOMContentLoaded', function () {
    // Actualizar estado del pedido
    const updateStatusButtons = document.querySelectorAll('.update-status');
    updateStatusButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idPedido = this.dataset.idPedido;
            const row = this.closest('tr');
            const nuevoEstado = row.querySelector('.order-status').value;

            if (confirm(`¿Estás seguro de que deseas cambiar el estado del pedido #${idPedido} a "${nuevoEstado}"?`)) {
                fetch('../controller/VendedorController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=updateOrderStatus&idPedido=${idPedido}&nuevoEstado=${nuevoEstado}`,
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
                    console.error('Error al actualizar el estado del pedido:', error);
                    alert('Error al actualizar el estado del pedido.');
                });
            }
        });
    });
});