<?php
require_once '../config/conexion.php';
require_once '../models/Producto.php';

$productoModel = new Producto();

$id = $_POST['id_producto'] ?? null;
$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$categoria = $_POST['categoria'];
$sucursal = $_POST['sucursal'];
$imagen_final = $_POST['imagen_actual'] ?? 'default.jpg';

// --- LÓGICA DE SUBIDA DE IMAGEN ---
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $ruta_destino = "../assets/img/productos/";
    
    // Obtenemos la extensión del archivo (jpg, png, etc.)
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    
    // Creamos un nombre único: Ej. prod_65f2a1.jpg
    // Esto evita problemas si dos fotos se llaman "imagen.jpg"
    $nuevo_nombre = "prod_" . uniqid() . "." . $ext;
    
    $ubicacion_temporal = $_FILES['imagen']['tmp_name'];

    // Movemos el archivo del servidor temporal a tu carpeta del proyecto
    if (move_uploaded_file($ubicacion_temporal, $ruta_destino . $nuevo_nombre)) {
        $imagen_final = $nuevo_nombre;
        
        // OPCIONAL: Borrar la imagen vieja para no llenar el disco de basura
        if (!empty($_POST['imagen_actual']) && $_POST['imagen_actual'] != 'default.jpg') {
            @unlink($ruta_destino . $_POST['imagen_actual']);
        }
    }
}

// Preparamos los datos para el Modelo
$datos = [$nombre, $precio, $stock, $categoria, $sucursal, $imagen_final];

if ($id) {
    $res = $productoModel->actualizar($datos, $id);
} else {
    $res = $productoModel->crear($datos);
}

header("Location: ../views/admin_dashboard.php?res=ok");