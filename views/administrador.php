<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario es administrador
$isAuthenticated = isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'administrador';
if (!$isAuthenticated) {
    header("Location: login.php");
    exit();
}

// Cargar datos desde el controlador
require_once '../controller/AdminController.php';
$db = new Database();
$controller = new AdminController($db->getConnection());

// Obtener datos
$productos = $controller->getProductos();
$usuarios = $controller->getUsuarios();
$categorias = $controller->getCategorias();
$logs = $controller->getLogs();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Administrador</title>
    <link rel="stylesheet" href="../assets/css/normalize.css">
    <link rel="stylesheet" href="../assets/css/tienda.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Cabecera -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-shopping-bag"></i>
                <h1>TiendaRopa - Administrador</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="admin.php?tab=productos" class="<?php echo !isset($_GET['tab']) || $_GET['tab'] === 'productos' ? 'active' : ''; ?>">Productos</a></li>
                    <li><a href="admin.php?tab=usuarios" class="<?php echo isset($_GET['tab']) && $_GET['tab'] === 'usuarios' ? 'active' : ''; ?>">Usuarios</a></li>
                    <li><a href="admin.php?tab=categorias" class="<?php echo isset($_GET['tab']) && $_GET['tab'] === 'categorias' ? 'active' : ''; ?>">Categorías</a></li>
                    <li><a href="admin.php?tab=logs" class="<?php echo isset($_GET['tab']) && $_GET['tab'] === 'logs' ? 'active' : ''; ?>">Logs</a></li>
                </ul>
            </nav>
            <div class="user-actions">
            <a href="../controller/logout.php">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main>
            <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'productos'): ?>
                <!-- Gestión de Productos -->
                <section class="products">
                    <h2>Gestión de Productos</h2>

                    <!-- Formulario para agregar producto -->
                    <h3>Agregar Producto</h3>
                    <form id="agregarProductoForm">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" required>

                        <label for="descripcion">Descripción:</label>
                        <textarea name="descripcion" id="descripcion"></textarea>

                        <label for="precio">Precio:</label>
                        <input type="number" name="precio" id="precio" step="0.01" min="0" required>

                        <label for="id_categoria">Categoría:</label>
                        <select name="id_categoria" id="id_categoria" required>
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>">
                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="talla">Talla:</label>
                        <input type="text" name="talla" id="talla">

                        <label for="color">Color:</label>
                        <input type="text" name="color" id="color">

                        <label for="stock">Stock Inicial:</label>
                        <input type="number" name="stock" id="stock" min="0" required>

                        <label for="destacado">Destacado:</label>
                        <input type="checkbox" name="destacado" id="destacado">

                        <button type="submit" class="btn btn-primary">Agregar Producto</button>
                    </form>

                    <h3>Lista de Productos</h3>
                    <?php if (empty($productos)): ?>
                        <p>No hay productos disponibles.</p>
                    <?php else: ?>
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Talla</th>
                                    <th>Color</th>
                                    <th>Stock</th>
                                    <th>Destacado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr data-id-producto="<?php echo $producto['id_producto']; ?>">
                                        <td><?php echo htmlspecialchars($producto['id_producto']); ?></td>
                                        <td>
                                            <input type="text" class="edit-nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        </td>
                                        <td>
                                            <select class="edit-categoria">
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>" 
                                                            <?php echo $categoria['id_categoria'] == $producto['id_categoria'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="edit-precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" step="0.01" min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="edit-talla" value="<?php echo htmlspecialchars($producto['talla'] ?? ''); ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="edit-color" value="<?php echo htmlspecialchars($producto['color'] ?? ''); ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="edit-stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" min="0">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="edit-destacado" <?php echo $producto['destacado'] ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary update-product" data-id-producto="<?php echo $producto['id_producto']; ?>">Actualizar</button>
                                            <button class="btn btn-danger delete-product" data-id-producto="<?php echo $producto['id_producto']; ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

            <?php elseif ($_GET['tab'] === 'usuarios'): ?>
                <!-- Gestión de Usuarios -->
                <section class="users">
                    <h2>Gestión de Usuarios</h2>
                    <?php if (empty($usuarios)): ?>
                        <p>No hay usuarios disponibles.</p>
                    <?php else: ?>
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr data-id-usuario="<?php echo $usuario['id_usuario']; ?>">
                                        <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td>
                                            <select class="edit-rol">
                                                <option value="cliente" <?php echo $usuario['rol'] === 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                                                <option value="vendedor" <?php echo $usuario['rol'] === 'vendedor' ? 'selected' : ''; ?>>Vendedor</option>
                                                <option value="almacenero" <?php echo $usuario['rol'] === 'almacenero' ? 'selected' : ''; ?>>Almacenero</option>
                                                <option value="marketing" <?php echo $usuario['rol'] === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                                                <option value="gerente" <?php echo $usuario['rol'] === 'gerente' ? 'selected' : ''; ?>>Gerente</option>
                                                <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary update-user" data-id-usuario="<?php echo $usuario['id_usuario']; ?>">Actualizar Rol</button>
                                            <button class="btn btn-danger delete-user" data-id-usuario="<?php echo $usuario['id_usuario']; ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

            <?php elseif ($_GET['tab'] === 'categorias'): ?>
                <!-- Gestión de Categorías -->
                <section class="categories">
                    <h2>Gestión de Categorías</h2>

                    <!-- Formulario para agregar categoría -->
                    <h3>Agregar Categoría</h3>
                    <form id="agregarCategoriaForm">
                        <label for="nombreCategoria">Nombre:</label>
                        <input type="text" name="nombre" id="nombreCategoria" required>

                        <label for="descripcionCategoria">Descripción:</label>
                        <textarea name="descripcion" id="descripcionCategoria"></textarea>

                        <button type="submit" class="btn btn-primary">Agregar Categoría</button>
                    </form>

                    <h3>Lista de Categorías</h3>
                    <?php if (empty($categorias)): ?>
                        <p>No hay categorías disponibles.</p>
                    <?php else: ?>
                        <table class="categories-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr data-id-categoria="<?php echo $categoria['id_categoria']; ?>">
                                        <td><?php echo htmlspecialchars($categoria['id_categoria']); ?></td>
                                        <td>
                                            <input type="text" class="edit-nombre-categoria" value="<?php echo htmlspecialchars($categoria['nombre']); ?>">
                                        </td>
                                        <td>
                                            <textarea class="edit-descripcion-categoria"><?php echo htmlspecialchars($categoria['descripcion'] ?? ''); ?></textarea>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary update-category" data-id-categoria="<?php echo $categoria['id_categoria']; ?>">Actualizar</button>
                                            <button class="btn btn-danger delete-category" data-id-categoria="<?php echo $categoria['id_categoria']; ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

            <?php elseif ($_GET['tab'] === 'logs'): ?>
                <!-- Logs de Auditoría -->
                <section class="logs">
                    <h2>Logs de Auditoría</h2>
                    <?php if (empty($logs)): ?>
                        <p>No hay logs disponibles.</p>
                    <?php else: ?>
                        <table class="logs-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Tipo de Usuario</th>
                                    <th>Acción</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($log['id_log']); ?></td>
                                        <td><?php echo htmlspecialchars($log['usuario_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($log['tipo_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($log['accion']); ?></td>
                                        <td><?php echo htmlspecialchars($log['fecha_accion']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>

        <!-- Pie de página -->
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TiendaRopa</h3>
                    <p>Tu destino para encontrar las últimas tendencias en moda.</p>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Comercio 123, Madrid</p>
                    <p><i class="fas fa-phone"></i> +34 912 345 678</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 TiendaRopa. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>