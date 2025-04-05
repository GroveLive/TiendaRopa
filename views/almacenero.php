<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario es almacenero
$isAuthenticated = isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'almacenero';
if (!$isAuthenticated) {
    header("Location: login.php");
    exit();
}

// Cargar datos desde el controlador
require_once '../controller/AlmaceneroController.php';
$db = new Database();
$controller = new AlmaceneroController($db->getConnection());

// Obtener inventario, pedidos en estado "enviado" y productos no en inventario
$inventario = $controller->getInventario();
$pedidosEnviados = $controller->getPedidosEnviados();
$productosNoEnInventario = $controller->getProductosNoEnInventario();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Almacenero</title>
    <link rel="stylesheet" href="../assets/css/normalize.css">
    <link rel="stylesheet" href="../assets/css/tienda.css">
    <link rel="stylesheet" href="../assets/css/almacenero.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Cabecera -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-shopping-bag"></i>
                <h1>TiendaRopa - Almacenero</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="almacenero.php?tab=inventario" class="<?php echo !isset($_GET['tab']) || $_GET['tab'] === 'inventario' ? 'active' : ''; ?>">Inventario</a></li>
                    <li><a href="almacenero.php?tab=pedidos" class="<?php echo isset($_GET['tab']) && $_GET['tab'] === 'pedidos' ? 'active' : ''; ?>">Pedidos</a></li>
                </ul>
            </nav>
            <div class="user-actions">
            <a href="../controller/logout.php">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main>
            <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'inventario'): ?>
                <!-- Gestión de Inventario -->
                <section class="inventory">
                    <h2>Gestión de Inventario</h2>

                    <!-- Formulario para agregar producto al inventario -->
                    <h3>Agregar Producto al Inventario</h3>
                    <?php if (empty($productosNoEnInventario)): ?>
                        <p>Todos los productos ya están en el inventario.</p>
                    <?php else: ?>
                        <form id="agregarProductoForm">
                            <label for="idProducto">Producto:</label>
                            <select name="idProducto" id="idProducto" required>
                                <option value="">Selecciona un producto</option>
                                <?php foreach ($productosNoEnInventario as $producto): ?>
                                    <option value="<?php echo htmlspecialchars($producto['id_producto']); ?>">
                                        <?php echo htmlspecialchars($producto['nombre']) . " (" . htmlspecialchars($producto['categoria']) . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <label for="cantidad">Cantidad:</label>
                            <input type="number" name="cantidad" id="cantidad" min="0" required>

                            <button type="submit" class="btn btn-primary">Agregar al Inventario</button>
                        </form>
                    <?php endif; ?>

                    <h3>Inventario Actual</h3>
                    <?php if (empty($inventario)): ?>
                        <p>No hay productos en el inventario.</p>
                    <?php else: ?>
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>ID Producto</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inventario as $item): ?>
                                    <tr data-id-inventario="<?php echo $item['id_inventario']; ?>">
                                        <td><?php echo htmlspecialchars($item['id_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($item['categoria']); ?></td>
                                        <td>
                                            <input type="number" class="stock-quantity" value="<?php echo htmlspecialchars($item['cantidad']); ?>" min="0">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary update-stock" data-id-inventario="<?php echo $item['id_inventario']; ?>">Actualizar Stock</button>
                                            <button class="btn btn-danger delete-from-inventory" data-id-inventario="<?php echo $item['id_inventario']; ?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

            <?php elseif ($_GET['tab'] === 'pedidos'): ?>
                <!-- Pedidos para Enviar -->
                <section class="shipments">
                    <h2>Pedidos para Enviar</h2>
                    <?php if (empty($pedidosEnviados)): ?>
                        <p>No hay pedidos para enviar.</p>
                    <?php else: ?>
                        <table class="shipments-table">
                            <thead>
                                <tr>
                                    <th>ID Pedido</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Detalles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidosEnviados as $pedido): ?>
                                    <tr data-id-pedido="<?php echo $pedido['id_pedido']; ?>">
                                        <td><?php echo htmlspecialchars($pedido['id_pedido']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                                        <td><?php echo number_format($pedido['total'], 2); ?>€</td>
                                        <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                                        <td>
                                            <?php if (!empty($pedido['detalles'])): ?>
                                                <ul>
                                                    <?php foreach ($pedido['detalles'] as $detalle): ?>
                                                        <li>
                                                            <?php echo htmlspecialchars($detalle['producto_nombre']); ?> 
                                                            (Cantidad: <?php echo htmlspecialchars($detalle['cantidad']); ?>, 
                                                            Precio Unitario: <?php echo number_format($detalle['precio_unitario'], 2); ?>€)
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p>No hay detalles para este pedido.</p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary mark-as-delivered" data-id-pedido="<?php echo $pedido['id_pedido']; ?>">Marcar como Entregado</button>
                                        </td>
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

    <script src="../assets/js/almacenero.js"></script>
</body>
</html>