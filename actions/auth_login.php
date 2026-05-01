<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = DB::getInstance()->getConnection();
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificamos si el usuario existe y si la clave coincide con el hash
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre']; // Asegúrate que en index.php diga 'user_name'
        $_SESSION['user_rol'] = $user['id_rol'];

        if ($user['id_rol'] == 1) {
            header("Location: ../views/admin_dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    } else {
        header("Location: ../login.php?error=1");
        exit();
    }
}