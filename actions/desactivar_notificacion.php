<?php
// actions/desactivar_notificacion.php
session_start();

// 1. Cargamos rutas usando realpath para evitar errores en Windows/XAMPP
$conexionPath = realpath(__DIR__ . '/../config/conexion.php');
$modelPath = realpath(__DIR__ . '/../models/Notificacion.php');

header('Content-Type: application/json');

if ($conexionPath && $modelPath) {
    require_once $conexionPath;
    require_once $modelPath;
} else {
    echo json_encode(['ok' => false, 'error' => 'Error de rutas en el servidor']);
    exit;
}

// 2. Verificación de sesión (Solo admin puede desactivar)
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 1) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['ok' => false, 'error' => 'ID de alerta no recibido']);
    exit;
}

try {
    // Usamos la instancia de PDO de tu clase DB
    $db = DB::getInstance()->getConnection();
    $notifModel = new Notificacion($db);
    
    // Ejecutamos el método que creamos en tu modelo
    $success = $notifModel->desactivarAlerta((int)$id);

    echo json_encode(['ok' => $success]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}