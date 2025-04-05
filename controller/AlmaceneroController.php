<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db/conexion.php';

class AlmaceneroController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener el inventario (lista de productos con su stock)
    public function getInventario() {
        $query = "SELECT i.id_inventario, p.id_producto, p.nombre, c.nombre as categoria, i.cantidad
                  FROM Inventario i
                  JOIN Productos p ON i.id_producto = p.id_producto
                  JOIN Categorias c ON p.id_categoria = c.id_categoria
                  ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos que no están en el inventario
    public function getProductosNoEnInventario() {
        $query = "SELECT p.id_producto, p.nombre, c.nombre as categoria
                  FROM Productos p
                  JOIN Categorias c ON p.id_categoria = c.id_categoria
                  WHERE p.id_producto NOT IN (SELECT id_producto FROM Inventario)
                  ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Agregar un producto al inventario
    public function agregarProductoInventario($idProducto, $cantidad) {
        try {
            $query = "INSERT INTO Inventario (id_producto, cantidad, fecha_actualizacion) 
                      VALUES (:idProducto, :cantidad, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'idProducto' => $idProducto,
                'cantidad' => $cantidad
            ]);

            // Actualizar el stock en la tabla Productos
            $query = "UPDATE Productos SET stock = :cantidad WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'cantidad' => $cantidad,
                'idProducto' => $idProducto
            ]);

            return [
                "success" => true,
                "message" => "Producto agregado al inventario con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al agregar el producto al inventario: " . $e->getMessage()
            ];
        }
    }

    // Actualizar el stock de un producto
    public function updateStock($idInventario, $cantidad) {
        try {
            // Actualizar la tabla Inventario
            $query = "UPDATE Inventario SET cantidad = :cantidad, fecha_actualizacion = NOW() 
                      WHERE id_inventario = :idInventario";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'cantidad' => $cantidad,
                'idInventario' => $idInventario
            ]);

            // Obtener el id_producto asociado
            $query = "SELECT id_producto FROM Inventario WHERE id_inventario = :idInventario";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idInventario' => $idInventario]);
            $idProducto = $stmt->fetch(PDO::FETCH_ASSOC)['id_producto'];

            // Actualizar el stock en la tabla Productos (para mantener sincronización)
            $query = "UPDATE Productos SET stock = :cantidad WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'cantidad' => $cantidad,
                'idProducto' => $idProducto
            ]);

            return [
                "success" => true,
                "message" => "Stock actualizado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al actualizar el stock: " . $e->getMessage()
            ];
        }
    }

    // Eliminar un producto del inventario
    public function eliminarProductoInventario($idInventario) {
        try {
            // Obtener el id_producto antes de eliminar
            $query = "SELECT id_producto FROM Inventario WHERE id_inventario = :idInventario";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idInventario' => $idInventario]);
            $idProducto = $stmt->fetch(PDO::FETCH_ASSOC)['id_producto'];

            // Eliminar el registro del inventario
            $query = "DELETE FROM Inventario WHERE id_inventario = :idInventario";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idInventario' => $idInventario]);

            // Actualizar el stock en la tabla Productos a 0
            $query = "UPDATE Productos SET stock = 0 WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idProducto' => $idProducto]);

            return [
                "success" => true,
                "message" => "Producto eliminado del inventario con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al eliminar el producto del inventario: " . $e->getMessage()
            ];
        }
    }

    // Obtener pedidos en estado "enviado"
    public function getPedidosEnviados() {
        $query = "SELECT p.id_pedido, p.fecha_pedido, p.total, p.estado, u.nombre as cliente_nombre
                  FROM Pedidos p
                  JOIN Usuarios u ON p.id_usuario = u.id_usuario
                  WHERE p.estado = 'enviado'
                  ORDER BY p.fecha_pedido DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los detalles de cada pedido
        foreach ($pedidos as &$pedido) {
            $pedido['detalles'] = $this->getDetallesPedido($pedido['id_pedido']);
        }

        return $pedidos;
    }

    // Obtener los detalles de un pedido específico
    public function getDetallesPedido($idPedido) {
        $query = "SELECT dp.id_detalle, dp.id_producto, dp.cantidad, dp.precio_unitario, p.nombre as producto_nombre
                  FROM Detalles_Pedido dp
                  JOIN Productos p ON dp.id_producto = p.id_producto
                  WHERE dp.id_pedido = :idPedido";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['idPedido' => $idPedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marcar un pedido como entregado
    public function markAsDelivered($idPedido) {
        try {
            $query = "UPDATE Pedidos SET estado = 'entregado' WHERE id_pedido = :idPedido AND estado = 'enviado'";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idPedido' => $idPedido]);

            if ($stmt->rowCount() > 0) {
                return [
                    "success" => true,
                    "message" => "Pedido marcado como entregado."
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "El pedido no está en estado 'enviado' o no existe."
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al marcar el pedido como entregado: " . $e->getMessage()
            ];
        }
    }

    // Manejar solicitudes AJAX
    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'almacenero') {
                echo json_encode([
                    "success" => false,
                    "message" => "Usuario no autenticado o no autorizado."
                ]);
                return;
            }

            $action = isset($_POST['action']) ? $_POST['action'] : '';

            switch ($action) {
                case 'agregarProductoInventario':
                    $idProducto = isset($_POST['idProducto']) ? (int)$_POST['idProducto'] : 0;
                    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
                    if ($idProducto <= 0 || $cantidad < 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->agregarProductoInventario($idProducto, $cantidad));
                    break;

                case 'updateStock':
                    $idInventario = isset($_POST['idInventario']) ? (int)$_POST['idInventario'] : 0;
                    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
                    if ($idInventario <= 0 || $cantidad < 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->updateStock($idInventario, $cantidad));
                    break;

                case 'eliminarProductoInventario':
                    $idInventario = isset($_POST['idInventario']) ? (int)$_POST['idInventario'] : 0;
                    if ($idInventario <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "ID de inventario no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->eliminarProductoInventario($idInventario));
                    break;

                case 'markAsDelivered':
                    $idPedido = isset($_POST['idPedido']) ? (int)$_POST['idPedido'] : 0;
                    if ($idPedido <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Pedido no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->markAsDelivered($idPedido));
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
    $controller = new AlmaceneroController($db->getConnection());
    $controller->handleRequest();
}