<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Usamos el puerto 3307 que configuraste previamente
    $db = DB::getInstance()->getConnection();
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // 1. Buscamos al usuario
    $stmt = $db->prepare("SELECT id_usuario, nombre, password, id_rol FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificamos coincidencia
    if ($user && password_verify($password, $user['password'])) {
        // IMPORTANTE: Limpiamos la sesión vieja por seguridad
        session_regenerate_id();

        // 3. Guardamos los datos necesarios en la sesión
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['id_rol']; // Esta es la clave para el botón

        // 4. Redirección según rol
        if ($user['id_rol'] == 1) {
            header("Location: ../views/admin_dashboard.php");
        } else {
            // Usuarios Rol 2 o Rol 3 (Citas) van al index
            header("Location: ../index.php");
        }
        exit();
    } else {
        // Si falla, mandamos error
        header("Location: ../login.php?error=1");
        exit();
    }
}