<?php
require_once '../config/conexion.php';
require_once '../models/Producto.php';

$productoModel = new Producto();

// Usamos el operador de fusión de nulidad (??) para dar valores por defecto
// y evitar los Warnings de "Undefined array key"
$id          = $_POST['id_producto'] ?? null;
$nombre      = $_POST['nombre_prod'] ?? null; // Si no llega, es null
$descripcion = $_POST['descripcion'] ?? '';
$precio      = $_POST['precio'] ?? 0;
$stock       = $_POST['stock'] ?? 0;
$categoria   = $_POST['id_categoria'] ?? null;
$provider    = !empty($_POST['id_provider']) ? $_POST['id_provider'] : 1;
$sucursal    = !empty($_POST['id_sucursal']) ? $_POST['id_sucursal'] : null;

// VALIDACIÓN CRÍTICA: Si falta el nombre o la categoría, no intentamos guardar
if (empty($nombre) || empty($categoria)) {
    header("Location: ../views/admin_dashboard.php?res=error&msg=faltan_campos");
    exit();
}

// ... (viene de tu validación crítica)

// 1. Lógica para la imagen (Gualo Electronic Style)
$imagen_final = $_POST['imagen_actual'] ?? 'default.jpg';

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $ruta_destino = "../assets/img/productos/";
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = "prod_" . uniqid() . "." . $ext;
    
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino . $nuevo_nombre)) {
        $imagen_final = $nuevo_nombre;
        // Si no es la imagen por defecto, borramos la anterior para no llenar el server de basura
        if (!empty($_POST['imagen_actual']) && $_POST['imagen_actual'] != 'default.jpg') {
            @unlink($ruta_destino . $_POST['imagen_actual']);
        }
    }
}

// 2. Empaquetar datos para el Modelo
// El orden debe ser: nombre, descripcion, precio, stock, categoria, proveedor, imagen, sucursal
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

// 3. Ejecutar acción
if ($id) {
    // Si hay ID, estamos actualizando un accesorio existente (Jimny, Hilux, etc.)
    $res = $productoModel->actualizar($datos, $id);
} else {
    // Si no hay ID, es un producto nuevo para el inventario
    $res = $productoModel->crear($datos);
}

// 4. Redirección final al panel oscuro
if ($res) {
    header("Location: ../views/admin_dashboard.php?res=success");
} else {
    header("Location: ../views/admin_dashboard.php?res=error&msg=db_fail");
}
exit();