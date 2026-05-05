<?php
// actions/marcar_notificaciones.php
// Llamado por AJAX cuando el admin abre el dropdown de notificaciones

session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Notificacion.php';

header('Content-Type: application/json');

// 1. CORRECCIÓN DE SEGURIDAD:
// Usamos la misma lógica que en tu dashboard: id_rol = 1
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 1) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

try {
    $db = DB::getInstance()->getConnection();
    // Usamos $db porque es la instancia de PDO que devuelve tu clase DB
    $notificacion = new Notificacion($db);
    
    // Asegúrate de que el método en tu modelo se llame exactamente así
    $notificacion->marcarTodasLeidas();

    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}