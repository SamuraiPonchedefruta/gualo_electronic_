<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = DB::getInstance()->getConnection();
    
    // Recibimos los datos del formulario de registro
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // 1. Verificamos si el correo ya está registrado para evitar duplicados
    $checkEmail = $db->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $checkEmail->execute([$correo]);
    
    if ($checkEmail->fetch()) {
        header("Location: ../registro.php?error=email_existe");
        exit();
    }

    // 2. Encriptamos la contraseña por seguridad
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // 3. Insertamos el nuevo usuario (Rol 2 es cliente por defecto)
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, correo, password, id_rol) VALUES (?, ?, ?, 2)");
    
    if ($stmt->execute([$nombre, $correo, $passwordHash])) {
        // Registro exitoso, lo mandamos al login para que entre
        header("Location: ../login.php?registro=exito");
    } else {
        header("Location: ../registro.php?error=db");
    }
}