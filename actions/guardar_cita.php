<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = DB::getInstance()->getConnection();

    // 1. Capturamos los datos del formulario (incluyendo el id_producto)
    $id_cliente  = $_SESSION['user_id'];
    $id_vehiculo = $_POST['id_vehiculo'];
    $id_servicio = $_POST['id_servicio'];
    $id_producto = $_POST['id_producto']; // <--- ¡ESTE ES EL IMPORTANTE!
    $notas       = $_POST['notas'];
    $estado      = 0; // Estado pendiente

    // 2. Preparamos el INSERT incluyendo la columna id_producto
    $sql = "INSERT INTO citas (id_cliente, id_vehiculo, id_servicio, id_producto, notas, estado) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    
    // 3. Ejecutamos la inserción
    if ($stmt->execute([$id_cliente, $id_vehiculo, $id_servicio, $id_producto, $notas, $estado])) {
        header("Location: ../views/mis_citas.php?msg=success");
    } else {
        echo "Error al guardar la cita.";
    }
}