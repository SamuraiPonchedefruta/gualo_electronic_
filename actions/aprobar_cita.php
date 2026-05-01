<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cita = $_POST['id_cita'];
    $fecha = $_POST['fecha_programada'];
    $id_mecanico = $_POST['id_mecanico'];
    $nuevo_estado = 1; // Aprobada

    $db = DB::getInstance()->getConnection();
    
    // Actualizamos la cita con la fecha, el mecánico y el nuevo estado
    $stmt = $db->prepare("
        UPDATE citas 
        SET fecha_cita = ?, id_mecanico = ?, estado = ? 
        WHERE id_cita = ?
    ");

    if ($stmt->execute([$fecha, $id_mecanico, $nuevo_estado, $id_cita])) {
        header("Location: ../views/admin/citas_pendientes.php?success=1");
    } else {
        echo "Ocurrió un error al procesar la cita.";
    }
}