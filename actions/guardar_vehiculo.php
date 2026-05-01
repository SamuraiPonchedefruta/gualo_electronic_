<?php
session_start();
require_once '../config/conexion.php';

if($_POST) {
    $id_cliente = $_SESSION['user_id'];
    $id_modelo = $_POST['id_modelo'];
    $anio = $_POST['anio'];

    $db = DB::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO vehiculos_cliente (id_cliente, id_modelo, anio) VALUES (?, ?, ?)");
    
    if($stmt->execute([$id_cliente, $id_modelo, $anio])) {
        // Regresamos al usuario a la página anterior para que termine su cita
        echo "<script>alert('Vehículo registrado con éxito'); window.history.go(-2);</script>";
    }
}