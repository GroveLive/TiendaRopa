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

// Obtener productos en el carrito, direcciones y conteo
$carrito = $controller->getCarrito($_SESSION['usuario_id']);
$direcciones = $controller->getDirecciones($_SESSION['usuario_id']);
$cart_count = $controller->getCartCount($_SESSION['usuario_id']);

// Verificar si el carrito está vacío
if (empty($carrito)) {
    header("Location: carrito.php");
    exit();
}

// Calcular el total del pedido
$total = 0;
foreach ($carrito as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $total += $subtotal;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Checkout</title>
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

        <!-- Sección Checkout -->
        <section class="checkout">
            <h2>Finalizar Compra</h2>

            <!-- Resumen del Pedido -->
            <div class="order-summary">
                <h3>Resumen del Pedido</h3>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['imagen_url'] ?? '../assets/images/default-product.jpg'); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" style="width: 50px; height: 50px;">
                                    <?php echo htmlspecialchars($item['nombre']); ?>
                                </td>
                                <td><?php echo number_format($item['precio'], 2); ?>€</td>
                                <td><?php echo $item['cantidad']; ?></td>
                                <td><?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>€</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td><?php echo number_format($total, 2); ?>€</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Selección de Dirección -->
            <div class="shipping-address">
                <h3>Dirección de Envío</h3>
                <form id="checkout-form">
                    <div class="form-group">
                        <label for="direccion_id">Seleccionar Dirección</label>
                        <select id="direccion_id" name="direccion_id" required>
                            <option value="">Selecciona una dirección</option>
                            <?php foreach ($direcciones as $direccion): ?>
                                <option value="<?php echo $direccion['id_direccion']; ?>">
                                    <?php echo htmlspecialchars($direccion['direccion'] . ', ' . $direccion['ciudad'] . ', ' . $direccion['codigo_postal']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="nueva">Añadir nueva dirección</option>
                        </select>
                    </div>

                    <!-- Formulario para nueva dirección (oculto por defecto) -->
                    <div id="nueva-direccion" style="display: none;">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" placeholder="Calle, número, etc.">
                        </div>
                        <div class="form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad">
                        </div>
                        <div class="form-group">
                            <label for="codigo_postal">Código Postal</label>
                            <input type="text" id="codigo_postal" name="codigo_postal">
                        </div>
                        <div class="form-group">
                            <label for="pais">País</label>
                            <input type="text" id="pais" name="pais" value="España" readonly>
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <div class="form-group">
                        <label for="metodo_pago">Método de Pago</label>
                        <select id="metodo_pago" name="metodo_pago" required>
                            <option value="">Selecciona un método de pago</option>
                            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="paypal">PayPal</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>

                    <!-- Botón para confirmar el pedido -->
                    <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
                </form>
            </div>
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