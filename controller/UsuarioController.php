<?php
session_start();
require_once '../model/usuario.php';

class UsuarioController {
    private $usuario;

    public function __construct($db) {
        $this->usuario = new Usuario($db);
    }

    public function login($email, $password, $rol) {
        $usuario = $this->usuario->buscarPorEmailYRol($email, $rol);

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            // Iniciar sesión
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['telefono'] = $usuario['telefono'];
            $_SESSION['rol'] = $usuario['rol'];

            // Determinar la redirección según el rol
            $redirectPage = "";
            switch ($usuario['rol']) {
                case "cliente":
                    $redirectPage = "../views/cliente.php";
                    break;
                case "vendedor":
                    $redirectPage = "../views/vendedor.php";
                    break;
                case "almacenero":
                    $redirectPage = "../views/almacenero.php";
                    break;
                case "marketing":
                    $redirectPage = "../views/marketing.php";
                    break;
                case "gerente":
                    $redirectPage = "../views/gerente.php";
                    break;
                case "administrador":
                    $redirectPage = "../views/administrador.php";
                    break;
                default:
                    session_destroy();
                    return [
                        "success" => false,
                        "message" => "Rol no válido."
                    ];
            }

            return [
                "success" => true,
                "redirect" => $redirectPage
            ];
        } else {
            return [
                "success" => false,
                "message" => "Correo, contraseña o rol incorrectos."
            ];
        }
    }

    public function registrar($nombre, $email, $telefono, $password, $rol) {
        // Validar que el email no esté registrado
        if ($this->usuario->emailExiste($email)) {
            return [
                "success" => false,
                "message" => "El correo ya está registrado."
            ];
        }

        // Registrar el usuario
        $id = $this->usuario->registrar($nombre, $email, $telefono, $password, $rol);

        if ($id) {
            return [
                "success" => true,
                "message" => "Registro exitoso. Por favor, inicia sesión."
            ];
        } else {
            return [
                "success" => false,
                "message" => "Error al registrar el usuario."
            ];
        }
    }
}
?>