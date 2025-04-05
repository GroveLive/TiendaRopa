<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el cliente está autenticado
$isAuthenticated = isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'cliente';
if (!$isAuthenticated) {
    header("Location: login.php");
    exit();
}

// Cargar datos desde el controlador
require_once '../controller/ClienteController.php';
$db = new Database();
$controller = new ClienteController($db->getConnection());

// Obtener productos en el carrito y conteo
$carrito = $controller->getCarrito($_SESSION['usuario_id']);
$cart_count = $controller->getCartCount($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Carrito</title>
    <link rel="stylesheet" href="../assets/css/normalize.css">
    <link rel="stylesheet" href="../assets/css/tienda.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Cabecera -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-shopping-bag"></i>
                <h1>TiendaRopa</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="cliente.php">Inicio</a></li>
                    <li><a href="cliente.php?tab=catalogo">Catálogo</a></li>
                    <li><a href="cliente.php?action=ofertas">Ofertas</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <a href="#" class="icon-link" id="search-toggle"><i class="fas fa-search"></i></a>
                <a href="favoritos.php" class="icon-link"><i class="fas fa-heart"></i></a>
                <a href="carrito.php" class="icon-link cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge"><?php echo $cart_count; ?></span>
                </a>
                <a href="perfil.php" class="icon-link"><i class="fas fa-user"></i></a>
                <a href="../controller/logout.php">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Barra de búsqueda (oculta por defecto) -->
        <div class="search-bar" style="display: none;">
            <div class="search-container">
                <input type="text" placeholder="Buscar productos...">
                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Sección Carrito -->
        <section class="cart">
            <h2>Tu Carrito de Compras</h2>
            <?php if (!empty($carrito)): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($carrito as $item):
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total += $subtotal;
                        ?>
                            <tr data-id-carrito="<?php echo $item['id_carrito']; ?>">
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['imagen_url'] ?? '../assets/images/default-product.jpg'); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" style="width: 50px; height: 50px;">
                                    <?php echo htmlspecialchars($item['nombre']); ?>
                                </td>
                                <td><?php echo number_format($item['precio'], 2); ?>€</td>
                                <td>
                                    <input type="number" class="cart-quantity" value="<?php echo $item['cantidad']; ?>" min="1">
                                </td>
                                <td class="subtotal"><?php echo number_format($subtotal, 2); ?>€</td>
                                <td>
                                    <button class="btn btn-danger remove-from-cart" data-id-carrito="<?php echo $item['id_carrito']; ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td id="cart-total"><?php echo number_format($total, 2); ?>€</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="cart-actions">
                    <a href="cliente.php" class="btn btn-secondary">Seguir Comprando</a>
                    <a href="checkout.php" class="btn btn-primary">Proceder al Pago</a>
                </div>
            <?php else: ?>
                <p>Tu carrito está vacío. ¡Añade algunos productos!</p>
                <a href="cliente.php" class="btn btn-primary">Volver al Catálogo</a>
            <?php endif; ?>
        </section>

        <!-- Pie de página -->
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TiendaRopa</h3>
                    <p>Tu destino para encontrar las últimas tendencias en moda.</p>
                </div>
                <div class="footer-section">
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="cliente.php">Inicio</a></li>
                        <li><a href="cliente.php?tab=catalogo">Catálogo</a></li>
                        <li><a href="cliente.php?action=ofertas">Ofertas</a></li>
                    </ul>
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

    <script src="../assets/js/cliente.js"></script>
</body>
</html>