<?php
class DB {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $host = 'localhost:3306'; 
        $db   = 'gualo_electronic';
        $user = 'root';
        $pass = '';
        try {
            // Usamos utf8mb4 para evitar problemas con tildes o eñes
            $this->conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>