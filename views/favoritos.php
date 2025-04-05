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

// Obtener favoritos y conteo del carrito
$favoritos = $controller->getFavoritos($_SESSION['usuario_id']);
$cart_count = $controller->getCartCount($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Favoritos</title>
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

        <!-- Sección Favoritos -->
        <section class="favorites">
            <h2>Tus Favoritos</h2>
            <?php if (!empty($favoritos)): ?>
                <div class="products-grid">
                    <?php foreach ($favoritos as $producto): ?>
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars($producto['imagen_url'] ?? '../assets/images/default-product.jpg'); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price"><?php echo number_format($producto['precio'], 2); ?>€</p>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $producto['id_producto']; ?>">Añadir al Carrito</button>
                            <button class="btn btn-danger remove-from-favorites" data-id-favorito="<?php echo $producto['id_favorito']; ?>">Eliminar de Favoritos</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No tienes productos en tu lista de favoritos.</p>
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