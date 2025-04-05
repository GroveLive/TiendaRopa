<?php
session_start();


if (session_destroy()) {
    header("Location: ../views/login.php");
    exit();
} else {
    
    echo "Error al cerrar la sesión. Inténtalo de nuevo.";
    exit();
}
?>