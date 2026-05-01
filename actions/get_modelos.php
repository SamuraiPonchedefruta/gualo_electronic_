<?php
require_once '../config/conexion.php';
$id_marca = $_GET['id_marca'] ?? null;

if($id_marca) {
    $db = DB::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id_modelo, nombre_modelo FROM modelos WHERE id_marca = ?");
    $stmt->execute([$id_marca]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}