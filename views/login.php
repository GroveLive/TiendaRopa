<?php
session_start();

// Si el usuario ya está autenticado, redirigir según su rol
if (isset($_SESSION['usuario_id'])) {
    $rol = $_SESSION['rol'];
    switch ($rol) {
        case "cliente":
            header("Location: cliente.php");
            exit();
        case "vendedor":
            header("Location: vendedor.php");
            exit();
        case "soporte":
            header("Location: soporte_tecnico.php");
            exit();
        case "almacenero":
            header("Location: almacenero.php");
            exit();
        case "marketing":
            header("Location: marketing.php");
            exit();
        case "gerente":
            header("Location: gerente.php");
            exit();
        case "administrador":
            header("Location: administrador.php");
            exit();
        default:
            session_destroy();
            header("Location: login.php?success=0&message=" . urlencode("Rol no válido."));
            exit();
    }
}

$success = isset($_GET['success']) ? (int)$_GET['success'] : 0;
$message = isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : '';
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register - TiendaRopa</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="logo">
                <img src="../assets/img/logotienda.png" alt="TiendaRopa Logo">
            </div>
            <div class="login-form">
                <h2><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h2>
                <div id="login-error" style="color: red; display: <?php echo $success === 0 && $message ? 'block' : 'none'; ?>;">
                    <?php echo $success === 0 ? $message : ''; ?>
                </div>
                <input type="email" placeholder="Correo Electrónico" id="login-email">
                <input type="password" placeholder="Contraseña" id="login-password">
                <div>
                    <label for="login-rol">Rol</label>
                    <select id="login-rol">
                        <option value="cliente">Cliente</option>
                        <option value="vendedor">Vendedor</option>
                        <option value="soporte">Soporte</option>
                        <option value="almacenero">Almacenero</option>
                        <option value="marketing">Marketing</option>
                        <option value="gerente">Gerente</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <button id="btnLogin"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
                <p>¿No tienes una cuenta? <a href="#" id="btnShowRegister">Regístrate</a></p>
            </div>

            <div class="register-form" style="display: none;">
                <h2><i class="fas fa-user-plus"></i> Registro</h2>
                <div id="register-error" style="color: red; display: <?php echo $success === 1 && $message ? 'block' : 'none'; ?>;">
                    <?php echo $success === 1 ? $message : ''; ?>
                </div>
                <input type="text" placeholder="Nombre" id="register-name">
                <input type="email" placeholder="Correo Electrónico" id="register-email">
                <input type="tel" placeholder="Teléfono" id="register-phone">
                <input type="password" placeholder="Contraseña" id="register-password">
                <button id="btnRegister"><i class="fas fa-user-plus"></i> Registrar</button>
                <button id="btnCancelRegister">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="../assets/js/login.js"></script>
</body>
</html>