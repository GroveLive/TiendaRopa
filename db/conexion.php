<?php
class Database {
    private $host = "localhost";
    private $usuario_db = "root";
    private $contrasena_db = "";
    private $nombre_db = "TiendaRopa";
    private $conexion;

    public function getConnection() {
        try {
            $this->conexion = new PDO(
                "mysql:host=$this->host;dbname=$this->nombre_db;charset=utf8;unix_socket=/tmp/mysql.sock", // Ajusta el unix_socket según tu configuración
                $this->usuario_db,
                $this->contrasena_db
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage() . " | Host: $this->host | DB: $this->nombre_db | User: $this->usuario_db");
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>