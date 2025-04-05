<?php
require_once '../db/conexion.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        echo "Conexión exitosa.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>