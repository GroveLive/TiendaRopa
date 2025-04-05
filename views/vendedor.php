<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario es vendedor
$isAuthenticated = isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'vendedor';
if (!$isAuthenticated) {
    header("Location: login.php");
    exit();
}

// Cargar datos desde el controlador
require_once '../controller/VendedorController.php';
$db = new Database();
$controller = new VendedorController($db->getConnection());

// Obtener pedidos y estadísticas
$pedidos = $controller->getPedidos();
$estadisticasVentas = $controller->getEstadisticasVentas();
$productosMasVendidos = $controller->getProductosMasVendidos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Vendedor</title>
    <link rel="stylesheet" href="../assets/css/normalize.css">
    <link rel="stylesheet" href="../assets/css/tienda.css">
    <link rel="stylesheet" href="../assets/css/vendedor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Cabecera -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-shopping-bag"></i>
                <h1>TiendaRopa - Vendedor</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="vendedor.php?tab=pedidos" class="<?php echo !isset($_GET['tab']) || $_GET['tab'] === 'pedidos' ? 'active' : ''; ?>">Pedidos</a></li>
                    <li><a href="vendedor.php?tab=estadisticas" class="<?php echo isset($_GET['tab']) && $_GET['tab'] === 'estadisticas' ? 'active' : ''; ?>">Estadísticas</a></li>
                </ul>
            </nav>
            <div class="user-actions">
            <a href="../controller/logout.php">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main>
            <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'pedidos'): ?>
                <!-- Gestión de Pedidos -->
                <section class="orders">
                    <h2>Gestión de Pedidos</h2>
                    <?php if (empty($pedidos)): ?>
                        <p>No hay pedidos disponibles.</p>
                    <?php else: ?>
                        <table class="orders-table">
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
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr data-id-pedido="<?php echo $pedido['id_pedido']; ?>">
                                        <td><?php echo htmlspecialchars($pedido['id_pedido']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['cliente_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                                        <td><?php echo number_format($pedido['total'], 2); ?>€</td>
                                        <td>
                                            <select class="order-status" data-id-pedido="<?php echo $pedido['id_pedido']; ?>">
                                                <option value="pendiente" <?php echo $pedido['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="pagado" <?php echo $pedido['estado'] === 'pagado' ? 'selected' : ''; ?>>Pagado</option>
                                                <option value="enviado" <?php echo $pedido['estado'] === 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                                <option value="entregado" <?php echo $pedido['estado'] === 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                                                <option value="devuelto" <?php echo $pedido['estado'] === 'devuelto' ? 'selected' : ''; ?>>Devuelto</option>
                                            </select>
                                        </td>
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
                                            <button class="btn btn-primary update-status" data-id-pedido="<?php echo $pedido['id_pedido']; ?>">Actualizar Estado</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

            <?php elseif ($_GET['tab'] === 'estadisticas'): ?>
                <!-- Estadísticas de Ventas -->
                <section class="statistics">
                    <h2>Estadísticas de Ventas</h2>

                    <!-- Ventas por Día -->
                    <h3>Ventas por Día</h3>
                    <?php if (empty($estadisticasVentas)): ?>
                        <p>No hay datos de ventas disponibles.</p>
                    <?php else: ?>
                        <table class="statistics-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Total Ventas (€)</th>
                                    <th>Número de Pedidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estadisticasVentas as $estadistica): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estadistica['fecha']); ?></td>
                                        <td><?php echo number_format($estadistica['total_ventas'], 2); ?>€</td>
                                        <td><?php echo htmlspecialchars($estadistica['num_pedidos']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <!-- Productos Más Vendidos -->
                    <h3>Productos Más Vendidos</h3>
                    <?php if (empty($productosMasVendidos)): ?>
                        <p>No hay datos de productos vendidos.</p>
                    <?php else: ?>
                        <table class="statistics-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Unidades Vendidas</th>
                                    <th>Total Generado (€)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productosMasVendidos as $producto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($producto['unidades_vendidas']); ?></td>
                                        <td><?php echo number_format($producto['total_generado'], 2); ?>€</td>
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

    <script src="../assets/js/vendedor.js"></script>
</body>
</html>