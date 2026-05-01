<?php
class DB {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = '127.0.0.1'; 
        $db   = 'gualo_electronic';
        $user = 'root';
        $pass = ''; // <--- Si sigue fallando, intenta poner 'root' aquí
        $port = '3306'; // <--- ¡OJO! Si en XAMPP dice 3307, cámbialo aquí

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Esto nos dirá si el problema es el puerto o la clave
            die("ERROR DE CONEXIÓN EN GUALO DB: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function __clone() { }
}
?>