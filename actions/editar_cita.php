<?php
require_once __DIR__ . '/../models/Producto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productoModel = new Producto();
    
    // Capturamos el ID del producto
    $id_producto = $_POST['id_producto'];

    // Preparamos los datos (deben seguir el mismo orden que tu método actualizar)
    $datos = [
        $_POST['nombre_prod'],
        $_POST['descripcion'],
        $_POST['precio'],
        $_POST['stock'],
        $_POST['id_categoria'],
        $_POST['id_provider'],
        $_POST['imagen_url'], // Si no manejas subida de archivos aún, usa el texto del input
        $_POST['id_sucursal']
    ];

    // Ejecutamos la actualización
    if ($productoModel->actualizar($datos, $id_producto)) {
        // LA CLAVE: Redirección inmediata al panel de admin
        header("Location: ../views/admin_dashboard.php?res=success");
        exit();
    } else {
        header("Location: ../views/admin_dashboard.php?res=error");
        exit();
    }
}