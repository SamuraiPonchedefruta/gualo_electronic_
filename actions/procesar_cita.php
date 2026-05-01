<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $db = DB::getInstance()->getConnection();
    
    $id_usuario = $_SESSION['user_id'];
    $id_producto = $_POST['id_producto'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $notas = $_POST['notas'];

    try {
        $sql = "INSERT INTO citas (id_usuario, id_producto, fecha_cita, hora_cita, notas, estado) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_usuario, $id_producto, $fecha, $hora, $notas]);

        // Redirigir a una página de éxito
        header("Location: ../index.php?cita=ok");
    } catch (PDOException $e) {
        die("Error al agendar: " . $e->getMessage());
    }
}