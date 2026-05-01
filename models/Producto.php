<?php
require_once __DIR__ . '/../config/conexion.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
    }

    /**
     * Obtiene productos filtrando por estado activo.
     * @param bool $soloActivos Si es true, solo trae productos con estado 1.
     * @param int $limite Opcional para limitar resultados.
     */
    public function obtenerTodos($soloActivos = true, $limite = null) {
        $sql = "SELECT p.*, c.nombre_categoria, s.nombre_sucursal, s.ubicacion 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN sucursales s ON p.id_sucursal = s.id_sucursal";
        
        // --- FILTRO DE ESTADO ---
        // Agregamos el WHERE para que solo traiga los que tienen estado 1
        if ($soloActivos) {
            $sql .= " WHERE p.estado = 1";
        }

        if ($limite) {
            $sql .= " LIMIT " . (int)$limite;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.id_producto = ?"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        // Por defecto, al crear un producto, le asignamos estado 1 en la consulta
        $sql = "INSERT INTO productos (nombre_prod, descripcion, precio, stock, id_categoria, id_provider, imagen_url, id_sucursal, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        return $this->db->prepare($sql)->execute($datos);
    }

    public function actualizar($datos, $id) {
    // 8 campos a actualizar
    $sql = "UPDATE productos SET 
                nombre_prod=?, 
                descripcion=?, 
                precio=?, 
                stock=?, 
                id_categoria=?, 
                id_provider=?, 
                imagen_url=?, 
                id_sucursal=? 
            WHERE id_producto=?"; // El parámetro número 9
    
    // Creamos una copia de los datos para no modificar el array original
    $parametros = array_values($datos); 
    $parametros[] = $id; // Agregamos el ID al final del array (el noveno elemento)

    try {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($parametros);
    } catch (PDOException $e) {
        // Esto imprimirá el error real en los logs de XAMPP
        error_log("Error en DB: " . $e->getMessage());
        return false;
    }
}

    public function eliminar($id) {
        // En lugar de borrar (que da error de integridad), podrías hacer un "borrado lógico"
        // $sql = "UPDATE productos SET estado = 0 WHERE id_producto = ?";
        
        $sql = "DELETE FROM productos WHERE id_producto = ?";
        return $this->db->prepare($sql)->execute([$id]);
    }
}
?>