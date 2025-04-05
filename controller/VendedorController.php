<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db/conexion.php';

class VendedorController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener todos los pedidos
    public function getPedidos() {
        $query = "SELECT p.id_pedido, p.fecha_pedido, p.total, p.estado, u.nombre as cliente_nombre
                  FROM Pedidos p
                  JOIN Usuarios u ON p.id_usuario = u.id_usuario
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

    // Actualizar el estado de un pedido
    public function updateOrderStatus($idPedido, $nuevoEstado) {
        try {
            // Validar el estado
            $estadosPermitidos = ['pendiente', 'pagado', 'enviado', 'entregado', 'devuelto'];
            if (!in_array($nuevoEstado, $estadosPermitidos)) {
                return [
                    "success" => false,
                    "message" => "Estado no válido."
                ];
            }

            // Validar transiciones de estado (lógica de negocio)
            $query = "SELECT estado FROM Pedidos WHERE id_pedido = :idPedido";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idPedido' => $idPedido]);
            $estadoActual = $stmt->fetch(PDO::FETCH_ASSOC)['estado'];

            $transicionesPermitidas = [
                'pendiente' => ['pagado'],
                'pagado' => ['enviado'],
                'enviado' => ['entregado', 'devuelto'],
                'entregado' => ['devuelto'],
                'devuelto' => []
            ];

            if (!in_array($nuevoEstado, $transicionesPermitidas[$estadoActual] ?? [])) {
                return [
                    "success" => false,
                    "message" => "Transición de estado no permitida: de '$estadoActual' a '$nuevoEstado'."
                ];
            }

            // Actualizar el estado
            $query = "UPDATE Pedidos SET estado = :nuevoEstado WHERE id_pedido = :idPedido";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nuevoEstado' => $nuevoEstado,
                'idPedido' => $idPedido
            ]);

            return [
                "success" => true,
                "message" => "Estado del pedido actualizado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al actualizar el estado del pedido: " . $e->getMessage()
            ];
        }
    }

    // Obtener estadísticas de ventas por día
    public function getEstadisticasVentas() {
        $query = "SELECT DATE(fecha_pedido) as fecha, 
                         SUM(total) as total_ventas, 
                         COUNT(id_pedido) as num_pedidos
                  FROM Pedidos
                  WHERE estado IN ('pagado', 'enviado', 'entregado')
                  GROUP BY DATE(fecha_pedido)
                  ORDER BY fecha DESC
                  LIMIT 30"; // Últimos 30 días
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener los productos más vendidos
    public function getProductosMasVendidos() {
        $query = "SELECT p.nombre, c.nombre as categoria, 
                         SUM(dp.cantidad) as unidades_vendidas, 
                         SUM(dp.cantidad * dp.precio_unitario) as total_generado
                  FROM Detalles_Pedido dp
                  JOIN Productos p ON dp.id_producto = p.id_producto
                  JOIN Categorias c ON p.id_categoria = c.id_categoria
                  JOIN Pedidos ped ON dp.id_pedido = ped.id_pedido
                  WHERE ped.estado IN ('pagado', 'enviado', 'entregado')
                  GROUP BY dp.id_producto, p.nombre, c.nombre
                  ORDER BY unidades_vendidas DESC
                  LIMIT 5"; // Top 5 productos
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Manejar solicitudes AJAX
    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
                echo json_encode([
                    "success" => false,
                    "message" => "Usuario no autenticado o no autorizado."
                ]);
                return;
            }

            $action = isset($_POST['action']) ? $_POST['action'] : '';

            switch ($action) {
                case 'updateOrderStatus':
                    $idPedido = isset($_POST['idPedido']) ? (int)$_POST['idPedido'] : 0;
                    $nuevoEstado = isset($_POST['nuevoEstado']) ? $_POST['nuevoEstado'] : '';
                    if ($idPedido <= 0 || empty($nuevoEstado)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->updateOrderStatus($idPedido, $nuevoEstado));
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
    $controller = new VendedorController($db->getConnection());
    $controller->handleRequest();
}