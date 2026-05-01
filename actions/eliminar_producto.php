<?php
require_once '../config/conexion.php';
require_once '../models/Producto.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $productoModel = new Producto();
    $productoModel->eliminar($id);
}
header("Location: ../views/admin_dashboard.php?res=deleted");
exit();