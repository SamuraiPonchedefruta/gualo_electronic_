<?php
require_once '../config/conexion.php';

if($_POST) {
    $id_cita = $_POST['id_cita'];
    $db = DB::getInstance()->getConnection();

    // Cambiamos el estado a 2 (Realizada)
    $stmt = $db->prepare("UPDATE citas SET estado = 2 WHERE id_cita = ?");
    
    if($stmt->execute([$id_cita])) {
        header("Location: ../views/admin/gestion_citas.php?msg=Servicio Completado");
    }
}