<?php
require_once __DIR__ . '/../config/conexion.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
    }

    // Obtener todos los productos con su categoría y sucursal (para el Admin y Catálogo)
    public function obtenerTodos() {
        $sql = "SELECT p.*, c.nombre_categoria, s.nombre_sucursal 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                LEFT JOIN sucursales s ON p.id_sucursal = s.id_sucursal
                ORDER BY p.id_producto DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para insertar nuevos productos desde el panel de administración
    public function crear($nombre, $descripcion, $precio, $stock, $id_categoria, $id_provider, $imagen_url, $id_sucursal) {
        $sql = "INSERT INTO productos (nombre_prod, descripcion, precio, stock, id_categoria, id_provider, imagen_url, id_sucursal) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $precio, $stock, $id_categoria, $id_provider, $imagen_url, $id_sucursal]);
    }

    public function crear($datos) {
    $sql = "INSERT INTO productos (nombre_prod, precio, stock, id_categoria, id_sucursal, imagen_url) 
            VALUES (?, ?, ?, ?, ?, ?)";
    return $this->db->prepare($sql)->execute($datos);
}

public function actualizar($datos, $id) {
    $sql = "UPDATE productos SET nombre_prod=?, precio=?, stock=?, id_categoria=?, id_sucursal=?, imagen_url=? 
            WHERE id_producto=?";
    $datos[] = $id; // Agregamos el ID al final del array de datos
    return $this->db->prepare($sql)->execute($datos);
}

public function eliminar($id) {
    $sql = "DELETE FROM productos WHERE id_producto = ?";
    return $this->db->prepare($sql)->execute([$id]);
}
}
?>
