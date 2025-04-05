<?php
require_once '../db/conexion.php';

class Usuario {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Buscar un usuario por email y rol (para el login)
    public function buscarPorEmailYRol($email, $rol) {
        $query = "SELECT * FROM Usuarios WHERE email = :email AND rol = :rol LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email, 'rol' => $rol]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si un email ya está registrado
    public function emailExiste($email) {
        $query = "SELECT COUNT(*) FROM Usuarios WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    // Registrar un nuevo usuario (solo clientes)
    public function registrar($nombre, $email, $telefono, $password, $rol) {
        $query = "INSERT INTO Usuarios (nombre, email, contrasena, telefono, rol, fecha_registro) 
                  VALUES (:nombre, :email, :contrasena, :telefono, :rol, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'contrasena' => password_hash($password, PASSWORD_DEFAULT),
            'telefono' => $telefono,
            'rol' => $rol
        ]);
        return $this->db->lastInsertId();
    }
}
?>