<?php
session_start();
require_once '../db/conexion.php';
require_once '../controller/UsuarioController.php';

// Habilitar la visualización de errores (solo para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Limpiar cualquier salida previa
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

// Configurar el tipo de contenido como JSON
header('Content-Type: application/json; charset=UTF-8');

// Configurar manejo de errores
ini_set('log_errors', 1);
ini_set('error_log', '../logs/error.log');

try {
    $db = new Database();
    $conn = $db->getConnection();
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }
    $usuarioController = new UsuarioController($conn);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Verificar que todas las variables de $_POST estén definidas
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $rol = isset($_POST['rol']) ? $_POST['rol'] : '';
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';

        if ($action === "login") {
            if (empty($email) || empty($password) || empty($rol)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Por favor, completa todos los campos."
                ]);
                exit();
            }

            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Correo electrónico no válido."
                ]);
                exit();
            }

            // Validar que el rol sea válido
            $rolesValidos = ['cliente', 'vendedor', 'almacenero', 'marketing', 'gerente', 'administrador'];
            if (!in_array($rol, $rolesValidos)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Rol no válido."
                ]);
                exit();
            }

            $response = $usuarioController->login($email, $password, $rol);
            echo json_encode($response);
            exit();
        } elseif ($action === "register") {
            $rol = "cliente"; //  AQUI SE CAMBIAN LOS ROLES PAL REGISTRO WEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE :vvv
            if (empty($nombre) || empty($email) || empty($telefono) || empty($password)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Por favor, completa todos los campos."
                ]);
                exit();
            }

            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Correo electrónico no válido."
                ]);
                exit();
            }

            $response = $usuarioController->registrar($nombre, $email, $telefono, $password, $rol);
            echo json_encode($response);
            exit();
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Acción no válida."
            ]);
            exit();
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Método no permitido."
        ]);
        exit();
    }
} catch (Exception $e) {
    // Limpiar cualquier salida inesperada
    $output = ob_get_contents();
    if (!empty($output)) {
        error_log("Salida inesperada detectada en el bloque catch: " . $output);
        ob_clean();
    }

    echo json_encode([
        "success" => false,
        "message" => "Error en el servidor: " . $e->getMessage()
    ]);
} finally {
    ob_end_flush();
}
?>