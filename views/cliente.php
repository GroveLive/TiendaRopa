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

// Obtener categorías, productos destacados, historial de pedidos y conteo del carrito
$categorias = $controller->getCategorias();
$productosDestacados = $controller->getProductosDestacados();
$historialPedidos = $controller->getHistorialPedidos($_SESSION['usuario_id']);
$cart_count = $controller->getCartCount($_SESSION['usuario_id']);

// Obtener productos por categoría (si se selecciona una categoría)
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'inicio';
$productosPorCategoria = [];
if ($tab === 'catalogo' && isset($_GET['categoria'])) {
    $id_categoria = (int)$_GET['categoria'];
    $productosPorCategoria = $controller->getProductosPorCategoria($id_categoria);
}

// Obtener todas las reseñas para mostrarlas
$reseñas = $controller->getResenasPorProducto(0); // 0 para obtener todas, ajustaremos el método
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Cliente</title>
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
                    <li><a href="cliente.php?tab=inicio" class="<?php echo $tab === 'inicio' ? 'active' : ''; ?>">Inicio</a></li>
                    <li><a href="cliente.php?tab=catalogo" class="<?php echo $tab === 'catalogo' ? 'active' : ''; ?>">Catálogo</a></li>
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

        <!-- Contenido Principal -->
        <main>
            <?php if ($tab === 'inicio'): ?>
                <!-- Productos Destacados -->
                <section class="featured-products">
                    <h2>Productos Destacados</h2>
                    <div class="products-grid">
                        <?php foreach ($productosDestacados as $producto): ?>
                            <div class="product-card">
                                <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <p class="price"><?php echo number_format($producto['precio'], 2); ?>€</p>
                                <p class="rating">
                                    Calificación: <?php echo $producto['promedio_calificacion'] ? number_format($producto['promedio_calificacion'], 1) : 'Sin reseñas'; ?>
                                    <i class="fas fa-star"></i>
                                </p>
                                <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $producto['id_producto']; ?>">Añadir al Carrito</button>
                                <button class="btn btn-secondary add-to-wishlist" data-product-id="<?php echo $producto['id_producto']; ?>">Añadir a Favoritos</button>
                                <!-- Formulario de Reseña -->
                                <div class="review-form">
                                    <h4>Deja tu Reseña</h4>
                                    <form class="review-form-<?php echo $producto['id_producto']; ?>" data-product-id="<?php echo $producto['id_producto']; ?>">
                                        <div class="form-group">
                                            <label for="rating-<?php echo $producto['id_producto']; ?>">Calificación (1-5):</label>
                                            <input type="number" id="rating-<?php echo $producto['id_producto']; ?>" name="rating" min="1" max="5" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="comment-<?php echo $producto['id_producto']; ?>">Comentario:</label>
                                            <textarea id="comment-<?php echo $producto['id_producto']; ?>" name="comment" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                                    </form>
                                </div>
                                <!-- Mostrar Reseñas -->
                                <div class="reviews">
                                    <h4>Reseñas</h4>
                                    <?php
                                    $reseñasProducto = $controller->getResenasPorProducto($producto['id_producto']);
                                    if (empty($reseñasProducto)) {
                                        echo '<p>No hay reseñas para este producto.</p>';
                                    } else {
                                        foreach ($reseñasProducto as $reseña) {
                                            echo '<div class="review">';
                                            echo '<p><strong>Calificación:</strong> ' . htmlspecialchars($reseña['calificacion']) . ' <i class="fas fa-star"></i></p>';
                                            echo '<p>' . htmlspecialchars($reseña['comentario']) . '</p>';
                                            echo '<p><small>Por usuario #' . htmlspecialchars($reseña['id_usuario']) . ' el ' . htmlspecialchars($reseña['fecha_resena']) . '</small></p>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Historial de Pedidos -->
                <section class="order-history">
                    <h2>Historial de Pedidos</h2>
                    <?php if (empty($historialPedidos)): ?>
                        <p>No tienes pedidos recientes.</p>
                    <?php else: ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>ID Pedido</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historialPedidos as $pedido): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pedido['id_pedido']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                                        <td><?php echo number_format($pedido['total'], 2); ?>€</td>
                                        <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

            <?php elseif ($tab === 'catalogo'): ?>
                <!-- Catálogo de Productos -->
                <section class="catalog">
                    <h2>Catálogo de Productos</h2>
                    <div class="categories">
                        <h3>Categorías</h3>
                        <ul>
                            <?php foreach ($categorias as $categoria): ?>
                                <li>
                                    <a href="cliente.php?tab=catalogo&categoria=<?php echo $categoria['id_categoria']; ?>" class="<?php echo isset($_GET['categoria']) && $_GET['categoria'] == $categoria['id_categoria'] ? 'active' : ''; ?>">
                                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="products-grid">
                        <?php if (empty($productosPorCategoria)): ?>
                            <p>Selecciona una categoría para ver los productos.</p>
                        <?php else: ?>
                            <?php foreach ($productosPorCategoria as $producto): ?>
                                <div class="product-card">
                                    <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                    <p class="price"><?php echo number_format($producto['precio'], 2); ?>€</p>
                                    <p class="rating">
                                        Calificación: <?php echo $producto['promedio_calificacion'] ? number_format($producto['promedio_calificacion'], 1) : 'Sin reseñas'; ?>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $producto['id_producto']; ?>">Añadir al Carrito</button>
                                    <button class="btn btn-secondary add-to-wishlist" data-product-id="<?php echo $producto['id_producto']; ?>">Añadir a Favoritos</button>
                                    <!-- Formulario de Reseña -->
                                    <div class="review-form">
                                        <h4>Deja tu Reseña</h4>
                                        <form class="review-form-<?php echo $producto['id_producto']; ?>" data-product-id="<?php echo $producto['id_producto']; ?>">
                                            <div class="form-group">
                                                <label for="rating-<?php echo $producto['id_producto']; ?>">Calificación (1-5):</label>
                                                <input type="number" id="rating-<?php echo $producto['id_producto']; ?>" name="rating" min="1" max="5" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="comment-<?php echo $producto['id_producto']; ?>">Comentario:</label>
                                                <textarea id="comment-<?php echo $producto['id_producto']; ?>" name="comment" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                                        </form>
                                    </div>
                                    <!-- Mostrar Reseñas -->
                                    <div class="reviews">
                                        <h4>Reseñas</h4>
                                        <?php
                                        $reseñasProducto = $controller->getResenasPorProducto($producto['id_producto']);
                                        if (empty($reseñasProducto)) {
                                            echo '<p>No hay reseñas para este producto.</p>';
                                        } else {
                                            foreach ($reseñasProducto as $reseña) {
                                                echo '<div class="review">';
                                                echo '<p><strong>Calificación:</strong> ' . htmlspecialchars($reseña['calificacion']) . ' <i class="fas fa-star"></i></p>';
                                                echo '<p>' . htmlspecialchars($reseña['comentario']) . '</p>';
                                                echo '<p><small>Por usuario #' . htmlspecialchars($reseña['id_usuario']) . ' el ' . htmlspecialchars($reseña['fecha_resena']) . '</small></p>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
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
                    <h3>Enlaces rápidos</h3>
                    <ul>
                        <li><a href="cliente.php?tab=inicio">Inicio</a></li>
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