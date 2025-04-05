<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db/conexion.php';

class ClienteController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener todas las categorías
    public function getCategorias() {
        $query = "SELECT id_categoria, nombre FROM Categorias ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos por categoría
    public function getProductosPorCategoria($id_categoria) {
        $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen_url, p.destacado, 
                         (SELECT AVG(calificacion) FROM Resenas r WHERE r.id_producto = p.id_producto) as promedio_calificacion
                  FROM Productos p
                  WHERE p.id_categoria = :id_categoria";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_categoria' => $id_categoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos destacados
    public function getProductosDestacados() {
        $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen_url, p.destacado, 
                         (SELECT AVG(calificacion) FROM Resenas r WHERE r.id_producto = p.id_producto) as promedio_calificacion
                  FROM Productos p
                  WHERE p.destacado = TRUE
                  LIMIT 3";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener historial de pedidos (limitado para cliente.php)
    public function getHistorialPedidos($clienteId) {
        $query = "SELECT p.id_pedido, p.fecha_pedido, p.total, p.estado
                  FROM Pedidos p
                  WHERE p.id_usuario = :clienteId
                  ORDER BY p.fecha_pedido DESC
                  LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener historial completo de pedidos (para perfil.php)
    public function getHistorialPedidosCompleto($clienteId) {
        $query = "SELECT p.id_pedido, p.fecha_pedido, p.total, p.estado
                  FROM Pedidos p
                  WHERE p.id_usuario = :clienteId
                  ORDER BY p.fecha_pedido DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener detalles del cliente (para perfil.php)
    public function getClienteInfo($clienteId) {
        $query = "SELECT nombre, email, telefono FROM Usuarios WHERE id_usuario = :clienteId";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar información del cliente (para perfil.php)
    public function updateClienteInfo($clienteId, $nombre, $email, $telefono, $password = null) {
        $query = "UPDATE Usuarios SET nombre = :nombre, email = :email, telefono = :telefono";
        $params = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'clienteId' => $clienteId
        ];

        if ($password) {
            $query .= ", contrasena = :password";
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $query .= " WHERE id_usuario = :clienteId";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return [
            "success" => true,
            "message" => "Información actualizada con éxito."
        ];
    }

    // Obtener productos en el carrito (para carrito.php)
    public function getCarrito($clienteId) {
        $query = "SELECT c.id_carrito, c.id_producto, c.cantidad, p.nombre, p.precio, p.imagen_url
                  FROM Carrito c
                  JOIN Productos p ON c.id_producto = p.id_producto
                  WHERE c.id_usuario = :clienteId";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar cantidad en el carrito
    public function updateCarritoCantidad($idCarrito, $cantidad) {
        if ($cantidad <= 0) {
            return $this->removeFromCarrito($idCarrito);
        }
        $query = "UPDATE Carrito SET cantidad = :cantidad WHERE id_carrito = :idCarrito";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'cantidad' => $cantidad,
            'idCarrito' => $idCarrito
        ]);
        return [
            "success" => true,
            "message" => "Cantidad actualizada con éxito."
        ];
    }

    // Eliminar producto del carrito
    public function removeFromCarrito($idCarrito) {
        $query = "DELETE FROM Carrito WHERE id_carrito = :idCarrito";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['idCarrito' => $idCarrito]);
        return [
            "success" => true,
            "message" => "Producto eliminado del carrito."
        ];
    }

    // Obtener productos en favoritos (para favoritos.php)
    public function getFavoritos($clienteId) {
        $query = "SELECT f.id_favorito, f.id_producto, p.nombre, p.precio, p.imagen_url
                  FROM Favoritos f
                  JOIN Productos p ON f.id_producto = p.id_producto
                  WHERE f.id_usuario = :clienteId";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar producto de favoritos
    public function removeFromFavoritos($idFavorito) {
        $query = "DELETE FROM Favoritos WHERE id_favorito = :idFavorito";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['idFavorito' => $idFavorito]);
        return [
            "success" => true,
            "message" => "Producto eliminado de favoritos."
        ];
    }

    // Añadir reseña
    public function addReview($productId, $rating, $comment, $clienteId) {
        try {
            $query = "INSERT INTO Resenas (id_usuario, id_producto, calificacion, comentario, fecha_resena) 
                      VALUES (:clienteId, :productId, :rating, :comment, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'clienteId' => $clienteId,
                'productId' => $productId,
                'rating' => $rating,
                'comment' => $comment
            ]);
            return [
                "success" => true,
                "message" => "Reseña enviada con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al enviar la reseña: " . $e->getMessage()
            ];
        }
    }

    // Obtener reseñas por producto
    public function getResenasPorProducto($id_producto) {
        $query = "SELECT id_usuario, calificacion, comentario, fecha_resena 
                  FROM Resenas 
                  WHERE id_producto = :id_producto";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_producto' => $id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añadir a favoritos
    public function addToWishlist($productId, $clienteId) {
        try {
            $query = "INSERT INTO Favoritos (id_usuario, id_producto, fecha_agregado) 
                      VALUES (:clienteId, :productId, NOW()) 
                      ON DUPLICATE KEY UPDATE fecha_agregado = NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'clienteId' => $clienteId,
                'productId' => $productId
            ]);
            return [
                "success" => true,
                "message" => "Producto añadido a favoritos."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al añadir a favoritos: " . $e->getMessage()
            ];
        }
    }

    // Añadir al carrito
    public function addToCart($productId, $clienteId) {
        try {
            $query = "INSERT INTO Carrito (id_usuario, id_producto, cantidad, fecha_agregado) 
                      VALUES (:clienteId, :productId, 1, NOW()) 
                      ON DUPLICATE KEY UPDATE cantidad = cantidad + 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'clienteId' => $clienteId,
                'productId' => $productId
            ]);
            return [
                "success" => true,
                "message" => "Producto añadido al carrito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al añadir al carrito: " . $e->getMessage()
            ];
        }
    }

    // Obtener el conteo del carrito
    public function getCartCount($clienteId) {
        $query = "SELECT SUM(cantidad) as total FROM Carrito WHERE id_usuario = :clienteId";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Obtener direcciones del cliente
    public function getDirecciones($clienteId) {
        $query = "SELECT id_direccion, direccion, ciudad, codigo_postal, pais 
                  FROM Direcciones 
                  WHERE id_usuario = :clienteId";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['clienteId' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añadir una nueva dirección
    public function addDireccion($clienteId, $direccion, $ciudad, $codigo_postal, $pais) {
        $query = "INSERT INTO Direcciones (id_usuario, direccion, ciudad, codigo_postal, pais) 
                  VALUES (:clienteId, :direccion, :ciudad, :codigo_postal, :pais)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'clienteId' => $clienteId,
            'direccion' => $direccion,
            'ciudad' => $ciudad,
            'codigo_postal' => $codigo_postal,
            'pais' => $pais
        ]);
        return $this->db->lastInsertId();
    }

    // Procesar el pedido
    public function procesarPedido($clienteId, $direccionId, $metodoPago, $total) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO Pedidos (id_usuario, id_direccion, fecha_pedido, total, estado, metodo_pago) 
                      VALUES (:clienteId, :direccionId, NOW(), :total, 'pendiente', :metodoPago)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'clienteId' => $clienteId,
                'direccionId' => $direccionId,
                'total' => $total,
                'metodoPago' => $metodoPago
            ]);
            $pedidoId = $this->db->lastInsertId();

            $carrito = $this->getCarrito($clienteId);
            foreach ($carrito as $item) {
                $query = "INSERT INTO Detalles_Pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                          VALUES (:pedidoId, :productId, :cantidad, :precio)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    'pedidoId' => $pedidoId,
                    'productId' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio']
                ]);
            }

            $query = "DELETE FROM Carrito WHERE id_usuario = :clienteId";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['clienteId' => $clienteId]);

            $this->db->commit();

            return [
                "success" => true,
                "message" => "Pedido realizado con éxito. ID del pedido: " . $pedidoId,
                "pedidoId" => $pedidoId
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            return [
                "success" => false,
                "message" => "Error al procesar el pedido: " . $e->getMessage()
            ];
        }
    }

    // Manejar solicitudes AJAX
    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            error_log("Sesión en ClienteController: " . print_r($_SESSION, true));
            error_log("PHPSESSID: " . session_id());

            if (!isset($_SESSION['usuario_id'])) {
                echo json_encode([
                    "success" => false,
                    "message" => "Usuario no autenticado. Por favor, inicia sesión."
                ]);
                return;
            }

            $action = isset($_POST['action']) ? $_POST['action'] : '';
            $clienteId = $_SESSION['usuario_id'];

            switch ($action) {
                case 'addReview':
                    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
                    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
                    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
                    if ($productId <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos de reseña no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->addReview($productId, $rating, $comment, $clienteId));
                    break;

                case 'addToWishlist':
                    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
                    if ($productId <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Producto no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->addToWishlist($productId, $clienteId));
                    break;

                case 'addToCart':
                    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
                    if ($productId <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Producto no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->addToCart($productId, $clienteId));
                    break;

                case 'updateCarritoCantidad':
                    $idCarrito = isset($_POST['idCarrito']) ? (int)$_POST['idCarrito'] : 0;
                    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
                    if ($idCarrito <= 0 || $cantidad < 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->updateCarritoCantidad($idCarrito, $cantidad));
                    break;

                case 'removeFromCarrito':
                    $idCarrito = isset($_POST['idCarrito']) ? (int)$_POST['idCarrito'] : 0;
                    if ($idCarrito <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Producto no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->removeFromCarrito($idCarrito));
                    break;

                case 'removeFromFavoritos':
                    $idFavorito = isset($_POST['idFavorito']) ? (int)$_POST['idFavorito'] : 0;
                    if ($idFavorito <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Producto no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->removeFromFavoritos($idFavorito));
                    break;

                case 'updateClienteInfo':
                    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
                    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

                    if (empty($nombre) || empty($email) || empty($telefono)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Por favor, completa todos los campos obligatorios."
                        ]);
                        return;
                    }

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Correo electrónico no válido."
                        ]);
                        return;
                    }

                    echo json_encode($this->updateClienteInfo($clienteId, $nombre, $email, $telefono, $password));
                    break;

                case 'getCartCount':
                    echo json_encode($this->getCartCount($clienteId));
                    break;

                case 'procesarPedido':
                    $direccionId = isset($_POST['direccion_id']) ? (int)$_POST['direccion_id'] : 0;
                    $metodoPago = isset($_POST['metodo_pago']) ? trim($_POST['metodo_pago']) : '';
                    $total = isset($_POST['total']) ? floatval($_POST['total']) : 0;

                    if ($direccionId === 0 && isset($_POST['direccion'])) {
                        $direccion = trim($_POST['direccion']);
                        $ciudad = trim($_POST['ciudad']);
                        $codigoPostal = trim($_POST['codigo_postal']);
                        $pais = trim($_POST['pais']);

                        if (empty($direccion) || empty($ciudad) || empty($codigoPostal) || empty($pais)) {
                            echo json_encode([
                                "success" => false,
                                "message" => "Por favor, completa todos los campos de la nueva dirección."
                            ]);
                            return;
                        }

                        $direccionId = $this->addDireccion($clienteId, $direccion, $ciudad, $codigoPostal, $pais);
                    }

                    if ($direccionId <= 0 || empty($metodoPago) || $total <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos del pedido no válidos."
                        ]);
                        return;
                    }

                    $resultado = $this->procesarPedido($clienteId, $direccionId, $metodoPago, $total);
                    echo json_encode($resultado);
                    break;

                default:
                    echo json_encode([
                        "success" => false,
                        "message" => "Acción no válida."
                    ]);
                    break;
            }
        }
    }
}

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $controller = new ClienteController($db->getConnection());
    $controller->handleRequest();
}