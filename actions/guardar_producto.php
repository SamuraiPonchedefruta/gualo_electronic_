<?php
require_once '../config/conexion.php';
require_once '../models/Producto.php';

$productoModel = new Producto();

// 1. Capturamos TODO lo que viene del formulario
$id = $_POST['id_producto'] ?? null;
$nombre = $_POST['nombre_prod']; // Asegúrate que el name en el HTML sea nombre_prod
$descripcion = $_POST['descripcion'] ?? ''; // Nuevo: Captura la descripción
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$categoria = $_POST['id_categoria'];
$provider = $_POST['id_provider'] ?? 1; // Nuevo: Captura el proveedor (por defecto 1)
$sucursal = $_POST['id_sucursal'];
$imagen_final = $_POST['imagen_actual'] ?? 'default.jpg';

// --- LÓGICA DE SUBIDA DE IMAGEN (Tu lógica actual está bien) ---
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $ruta_destino = "../assets/img/productos/";
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = "prod_" . uniqid() . "." . $ext;
    
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino . $nuevo_nombre)) {
        $imagen_final = $nuevo_nombre;
        if (!empty($_POST['imagen_actual']) && $_POST['imagen_actual'] != 'default.jpg') {
            @unlink($ruta_destino . $_POST['imagen_actual']);
        }
    }
}

// 2. Preparamos los datos PARA QUE COINCIDAN con el Modelo (8 elementos en total)
// El orden debe ser: nombre, descripcion, precio, stock, categoria, provider, imagen, sucursal
$datos = [
    $nombre, 
    $descripcion, 
    $precio, 
    $stock, 
    $categoria, 
    $provider, 
    $imagen_final, 
    $sucursal
];

if ($id) {
    // Aquí el modelo recibirá 8 datos + el ID = 9 parámetros (Lo que pide tu UPDATE)
    $res = $productoModel->actualizar($datos, $id);
} else {
    // Aquí el modelo recibirá 8 datos = 8 parámetros (Lo que pide tu INSERT)
    $res = $productoModel->crear($datos);
}

header("Location: ../views/admin_productos.php?res=ok");
exit();