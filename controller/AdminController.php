<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db/conexion.php';

class AdminController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Registrar una acción en los logs
    private function logAction($idUsuario, $accion) {
        $query = "INSERT INTO Logs (id_usuario, tipo_usuario, accion, fecha_accion) 
                  VALUES (:idUsuario, 'administrador', :accion, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'idUsuario' => $idUsuario,
            'accion' => $accion
        ]);
    }

    // Obtener todos los productos
    public function getProductos() {
        $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.talla, p.color, p.stock, p.destacado, p.id_categoria, c.nombre as categoria
                  FROM Productos p
                  JOIN Categorias c ON p.id_categoria = c.id_categoria
                  ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Agregar un nuevo producto
    public function agregarProducto($nombre, $descripcion, $precio, $idCategoria, $talla, $color, $stock, $destacado) {
        try {
            // Insertar en la tabla Productos
            $query = "INSERT INTO Productos (nombre, descripcion, precio, id_categoria, talla, color, stock, destacado, fecha_creacion) 
                      VALUES (:nombre, :descripcion, :precio, :idCategoria, :talla, :color, :stock, :destacado, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion ?: null,
                'precio' => $precio,
                'idCategoria' => $idCategoria,
                'talla' => $talla ?: null,
                'color' => $color ?: null,
                'stock' => $stock,
                'destacado' => $destacado ? 1 : 0
            ]);

            $idProducto = $this->db->lastInsertId();

            // Insertar en la tabla Inventario
            $query = "INSERT INTO Inventario (id_producto, cantidad, fecha_actualizacion) 
                      VALUES (:idProducto, :cantidad, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'idProducto' => $idProducto,
                'cantidad' => $stock
            ]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Producto agregado: $nombre (ID: $idProducto)");

            return [
                "success" => true,
                "message" => "Producto agregado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al agregar el producto: " . $e->getMessage()
            ];
        }
    }

    // Actualizar un producto
    public function actualizarProducto($idProducto, $nombre, $idCategoria, $precio, $talla, $color, $stock, $destacado) {
        try {
            // Actualizar la tabla Productos
            $query = "UPDATE Productos 
                      SET nombre = :nombre, id_categoria = :idCategoria, precio = :precio, 
                          talla = :talla, color = :color, stock = :stock, destacado = :destacado
                      WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nombre' => $nombre,
                'idCategoria' => $idCategoria,
                'precio' => $precio,
                'talla' => $talla ?: null,
                'color' => $color ?: null,
                'stock' => $stock,
                'destacado' => $destacado ? 1 : 0,
                'idProducto' => $idProducto
            ]);

            // Actualizar la tabla Inventario
            $query = "UPDATE Inventario SET cantidad = :cantidad, fecha_actualizacion = NOW() 
                      WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'cantidad' => $stock,
                'idProducto' => $idProducto
            ]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Producto actualizado: $nombre (ID: $idProducto)");

            return [
                "success" => true,
                "message" => "Producto actualizado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al actualizar el producto: " . $e->getMessage()
            ];
        }
    }

    // Eliminar un producto
    public function eliminarProducto($idProducto) {
        try {
            // Obtener el nombre del producto para el log
            $query = "SELECT nombre FROM Productos WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idProducto' => $idProducto]);
            $nombre = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'];

            // Eliminar el producto (las claves foráneas con ON DELETE CASCADE manejan las dependencias)
            $query = "DELETE FROM Productos WHERE id_producto = :idProducto";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idProducto' => $idProducto]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Producto eliminado: $nombre (ID: $idProducto)");

            return [
                "success" => true,
                "message" => "Producto eliminado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al eliminar el producto: " . $e->getMessage()
            ];
        }
    }

    // Obtener todos los usuarios
    public function getUsuarios() {
        $query = "SELECT id_usuario, nombre, email, rol FROM Usuarios ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar el rol de un usuario
    public function actualizarRolUsuario($idUsuario, $nuevoRol) {
        try {
            // Validar el rol
            $rolesPermitidos = ['cliente', 'vendedor', 'almacenero', 'marketing', 'gerente', 'administrador'];
            if (!in_array($nuevoRol, $rolesPermitidos)) {
                return [
                    "success" => false,
                    "message" => "Rol no válido."
                ];
            }

            // No permitir que un administrador se degrade a sí mismo
            if ($idUsuario == $_SESSION['usuario_id'] && $nuevoRol !== 'administrador') {
                return [
                    "success" => false,
                    "message" => "No puedes cambiar tu propio rol."
                ];
            }

            // Obtener el nombre del usuario para el log
            $query = "SELECT nombre FROM Usuarios WHERE id_usuario = :idUsuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idUsuario' => $idUsuario]);
            $nombre = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'];

            // Actualizar el rol
            $query = "UPDATE Usuarios SET rol = :nuevoRol WHERE id_usuario = :idUsuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nuevoRol' => $nuevoRol,
                'idUsuario' => $idUsuario
            ]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Rol actualizado para usuario: $nombre (ID: $idUsuario, Nuevo Rol: $nuevoRol)");

            return [
                "success" => true,
                "message" => "Rol actualizado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al actualizar el rol: " . $e->getMessage()
            ];
        }
    }

    // Eliminar un usuario
    public function eliminarUsuario($idUsuario) {
        try {
            // No permitir que un administrador se elimine a sí mismo
            if ($idUsuario == $_SESSION['usuario_id']) {
                return [
                    "success" => false,
                    "message" => "No puedes eliminarte a ti mismo."
                ];
            }

            // Obtener el nombre del usuario para el log
            $query = "SELECT nombre FROM Usuarios WHERE id_usuario = :idUsuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idUsuario' => $idUsuario]);
            $nombre = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'];

            // Eliminar el usuario (las claves foráneas con ON DELETE CASCADE manejan las dependencias)
            $query = "DELETE FROM Usuarios WHERE id_usuario = :idUsuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idUsuario' => $idUsuario]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Usuario eliminado: $nombre (ID: $idUsuario)");

            return [
                "success" => true,
                "message" => "Usuario eliminado con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al eliminar el usuario: " . $e->getMessage()
            ];
        }
    }

    // Obtener todas las categorías
    public function getCategorias() {
        $query = "SELECT id_categoria, nombre, descripcion FROM Categorias ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Agregar una nueva categoría
    public function agregarCategoria($nombre, $descripcion) {
        try {
            $query = "INSERT INTO Categorias (nombre, descripcion) 
                      VALUES (:nombre, :descripcion)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion ?: null
            ]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Categoría agregada: $nombre");

            return [
                "success" => true,
                "message" => "Categoría agregada con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al agregar la categoría: " . $e->getMessage()
            ];
        }
    }

    // Actualizar una categoría
    public function actualizarCategoria($idCategoria, $nombre, $descripcion) {
        try {
            $query = "UPDATE Categorias 
                      SET nombre = :nombre, descripcion = :descripcion 
                      WHERE id_categoria = :idCategoria";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion ?: null,
                'idCategoria' => $idCategoria
            ]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Categoría actualizada: $nombre (ID: $idCategoria)");

            return [
                "success" => true,
                "message" => "Categoría actualizada con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al actualizar la categoría: " . $e->getMessage()
            ];
        }
    }

    // Eliminar una categoría
    public function eliminarCategoria($idCategoria) {
        try {
            // Verificar si la categoría está siendo utilizada por algún producto
            $query = "SELECT COUNT(*) FROM Productos WHERE id_categoria = :idCategoria";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idCategoria' => $idCategoria]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                return [
                    "success" => false,
                    "message" => "No se puede eliminar la categoría porque está siendo utilizada por productos."
                ];
            }

            // Obtener el nombre de la categoría para el log
            $query = "SELECT nombre FROM Categorias WHERE id_categoria = :idCategoria";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idCategoria' => $idCategoria]);
            $nombre = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'];

            // Eliminar la categoría
            $query = "DELETE FROM Categorias WHERE id_categoria = :idCategoria";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['idCategoria' => $idCategoria]);

            // Registrar la acción en los logs
            $this->logAction($_SESSION['usuario_id'], "Categoría eliminada: $nombre (ID: $idCategoria)");

            return [
                "success" => true,
                "message" => "Categoría eliminada con éxito."
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Error al eliminar la categoría: " . $e->getMessage()
            ];
        }
    }

    // Obtener los logs de auditoría
    public function getLogs() {
        $query = "SELECT l.id_log, u.nombre as usuario_nombre, l.tipo_usuario, l.accion, l.fecha_accion
                  FROM Logs l
                  JOIN Usuarios u ON l.id_usuario = u.id_usuario
                  ORDER BY l.fecha_accion DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Manejar solicitudes AJAX
    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
                echo json_encode([
                    "success" => false,
                    "message" => "Usuario no autenticado o no autorizado."
                ]);
                return;
            }

            $action = isset($_POST['action']) ? $_POST['action'] : '';

            switch ($action) {
                case 'agregarProducto':
                    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
                    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
                    $idCategoria = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : 0;
                    $talla = isset($_POST['talla']) ? trim($_POST['talla']) : '';
                    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
                    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
                    $destacado = isset($_POST['destacado']) && $_POST['destacado'] === 'on' ? true : false;

                    if (empty($nombre) || $precio <= 0 || $idCategoria <= 0 || $stock < 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->agregarProducto($nombre, $descripcion, $precio, $idCategoria, $talla, $color, $stock, $destacado));
                    break;

                case 'actualizarProducto':
                    $idProducto = isset($_POST['idProducto']) ? (int)$_POST['idProducto'] : 0;
                    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                    $idCategoria = isset($_POST['idCategoria']) ? (int)$_POST['idCategoria'] : 0;
                    $precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
                    $talla = isset($_POST['talla']) ? trim($_POST['talla']) : '';
                    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
                    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
                    $destacado = isset($_POST['destacado']) && $_POST['destacado'] === 'true' ? true : false;

                    if ($idProducto <= 0 || empty($nombre) || $idCategoria <= 0 || $precio <= 0 || $stock < 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->actualizarProducto($idProducto, $nombre, $idCategoria, $precio, $talla, $color, $stock, $destacado));
                    break;

                case 'eliminarProducto':
                    $idProducto = isset($_POST['idProducto']) ? (int)$_POST['idProducto'] : 0;
                    if ($idProducto <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "ID de producto no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->eliminarProducto($idProducto));
                    break;

                case 'actualizarRolUsuario':
                    $idUsuario = isset($_POST['idUsuario']) ? (int)$_POST['idUsuario'] : 0;
                    $nuevoRol = isset($_POST['nuevoRol']) ? trim($_POST['nuevoRol']) : '';
                    if ($idUsuario <= 0 || empty($nuevoRol)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->actualizarRolUsuario($idUsuario, $nuevoRol));
                    break;

                case 'eliminarUsuario':
                    $idUsuario = isset($_POST['idUsuario']) ? (int)$_POST['idUsuario'] : 0;
                    if ($idUsuario <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "ID de usuario no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->eliminarUsuario($idUsuario));
                    break;

                case 'agregarCategoria':
                    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
                    if (empty($nombre)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "El nombre de la categoría es obligatorio."
                        ]);
                        return;
                    }
                    echo json_encode($this->agregarCategoria($nombre, $descripcion));
                    break;

                case 'actualizarCategoria':
                    $idCategoria = isset($_POST['idCategoria']) ? (int)$_POST['idCategoria'] : 0;
                    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
                    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
                    if ($idCategoria <= 0 || empty($nombre)) {
                        echo json_encode([
                            "success" => false,
                            "message" => "Datos no válidos."
                        ]);
                        return;
                    }
                    echo json_encode($this->actualizarCategoria($idCategoria, $nombre, $descripcion));
                    break;

                case 'eliminarCategoria':
                    $idCategoria = isset($_POST['idCategoria']) ? (int)$_POST['idCategoria'] : 0;
                    if ($idCategoria <= 0) {
                        echo json_encode([
                            "success" => false,
                            "message" => "ID de categoría no válido."
                        ]);
                        return;
                    }
                    echo json_encode($this->eliminarCategoria($idCategoria));
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
    $controller = new AdminController($db->getConnection());
    $controller->handleRequest();
}