document.addEventListener('DOMContentLoaded', function () {
    // Agregar producto
    const agregarProductoForm = document.getElementById('agregarProductoForm');
    if (agregarProductoForm) {
        agregarProductoForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = new URLSearchParams();
            for (const [key, value] of formData) {
                data.append(key, value);
            }
            data.append('action', 'agregarProducto');

            fetch('../controller/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data,
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
                console.error('Error al agregar el producto:', error);
                alert('Error al agregar el producto.');
            });
        });
    }

    // Actualizar producto
    const updateProductButtons = document.querySelectorAll('.update-product');
    updateProductButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idProducto = this.dataset.idProducto;
            const row = this.closest('tr');
            const nombre = row.querySelector('.edit-nombre').value;
            const idCategoria = row.querySelector('.edit-categoria').value;
            const precio = parseFloat(row.querySelector('.edit-precio').value);
            const talla = row.querySelector('.edit-talla').value;
            const color = row.querySelector('.edit-color').value;
            const stock = parseInt(row.querySelector('.edit-stock').value);
            const destacado = row.querySelector('.edit-destacado').checked;

            if (empty(nombre) || precio <= 0 || idCategoria <= 0 || stock < 0) {
                alert('Por favor, completa todos los campos correctamente.');
                return;
            }

            fetch('../controller/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=actualizarProducto&idProducto=${idProducto}&nombre=${encodeURIComponent(nombre)}&idCategoria=${idCategoria}&precio=${precio}&talla=${encodeURIComponent(talla)}&color=${encodeURIComponent(color)}&stock=${stock}&destacado=${destacado}`,
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
                console.error('Error al actualizar el producto:', error);
                alert('Error al actualizar el producto.');
            });
        });
    });

    // Eliminar producto
    const deleteProductButtons = document.querySelectorAll('.delete-product');
    deleteProductButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idProducto = this.dataset.idProducto;
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                fetch('../controller/AdminController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=eliminarProducto&idProducto=${idProducto}`,
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
                    console.error('Error al eliminar el producto:', error);
                    alert('Error al eliminar el producto.');
                });
            }
        });
    });

    // Actualizar rol de usuario
    const updateUserButtons = document.querySelectorAll('.update-user');
    updateUserButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idUsuario = this.dataset.idUsuario;
            const row = this.closest('tr');
            const nuevoRol = row.querySelector('.edit-rol').value;

            if (confirm(`¿Estás seguro de que deseas cambiar el rol del usuario a "${nuevoRol}"?`)) {
                fetch('../controller/AdminController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=actualizarRolUsuario&idUsuario=${idUsuario}&nuevoRol=${nuevoRol}`,
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
                    console.error('Error al actualizar el rol del usuario:', error);
                    alert('Error al actualizar el rol del usuario.');
                });
            }
        });
    });

    // Eliminar usuario
    const deleteUserButtons = document.querySelectorAll('.delete-user');
    deleteUserButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idUsuario = this.dataset.idUsuario;
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                fetch('../controller/AdminController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=eliminarUsuario&idUsuario=${idUsuario}`,
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
                    console.error('Error al eliminar el usuario:', error);
                    alert('Error al eliminar el usuario.');
                });
            }
        });
    });

    // Agregar categoría
    const agregarCategoriaForm = document.getElementById('agregarCategoriaForm');
    if (agregarCategoriaForm) {
        agregarCategoriaForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = new URLSearchParams();
            for (const [key, value] of formData) {
                data.append(key, value);
            }
            data.append('action', 'agregarCategoria');

            fetch('../controller/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data,
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
                console.error('Error al agregar la categoría:', error);
                alert('Error al agregar la categoría.');
            });
        });
    }

    // Actualizar categoría
    const updateCategoryButtons = document.querySelectorAll('.update-category');
    updateCategoryButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idCategoria = this.dataset.idCategoria;
            const row = this.closest('tr');
            const nombre = row.querySelector('.edit-nombre-categoria').value;
            const descripcion = row.querySelector('.edit-descripcion-categoria').value;

            if (empty(nombre)) {
                alert('El nombre de la categoría es obligatorio.');
                return;
            }

            fetch('../controller/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=actualizarCategoria&idCategoria=${idCategoria}&nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}`,
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
                console.error('Error al actualizar la categoría:', error);
                alert('Error al actualizar la categoría.');
            });
        });
    });

    // Eliminar categoría
    const deleteCategoryButtons = document.querySelectorAll('.delete-category');
    deleteCategoryButtons.forEach(button => {
        button.addEventListener('click', function () {
            const idCategoria = this.dataset.idCategoria;
            if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
                fetch('../controller/AdminController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=eliminarCategoria&idCategoria=${idCategoria}`,
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
                    console.error('Error al eliminar la categoría:', error);
                    alert('Error al eliminar la categoría.');
                });
            }
        });
    });

    // Función auxiliar para verificar si un valor está vacío
    function empty(value) {
        return !value || value.trim() === '';
    }
});