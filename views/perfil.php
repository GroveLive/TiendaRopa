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

// Obtener información del cliente y pedidos
$clienteInfo = $controller->getClienteInfo($_SESSION['usuario_id']);
$historialPedidos = $controller->getHistorialPedidosCompleto($_SESSION['usuario_id']);
$cart_count = $controller->getCartCount($_SESSION['usuario_id']);

// Mostrar mensaje de éxito si existe
$message = isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiendaRopa - Perfil</title>
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

        <!-- Mensaje de Éxito (si existe) -->
        <?php if ($message): ?>
            <div class="success-message" style="color: green; padding: 10px; background-color: #e6ffe6; margin: 20px 0;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Sección Perfil -->
        <section class="profile">
            <h2>Mi Perfil</h2>
            <form id="profile-form">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($clienteInfo['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($clienteInfo['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($clienteInfo['telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Información</button>
            </form>
        </section>

        <!-- Historial de Pedidos -->
        <section class="order-history">
            <h2>Historial de Pedidos</h2>
            <?php if (!empty($historialPedidos)): ?>
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
            <?php else: ?>
                <p>No tienes pedidos.</p>
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