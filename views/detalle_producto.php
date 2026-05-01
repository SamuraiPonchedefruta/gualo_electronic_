<?php
session_start();
require_once '../models/Producto.php';

// 1. Validar que venga un ID en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$id_producto = $_GET['id'];
$db = DB::getInstance()->getConnection();

// 2. Consultar los datos incluyendo el JOIN con la tabla sucursales
$stmt = $db->prepare("SELECT p.*, c.nombre_categoria, s.nombre_sucursal 
                      FROM productos p 
                      JOIN categorias c ON p.id_categoria = c.id_categoria 
                      JOIN sucursales s ON p.id_sucursal = s.id_sucursal 
                      WHERE p.id_producto = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch();

// Si el producto no existe
if (!$producto) {
    die("El accesorio no existe en nuestro inventario.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $producto['nombre_prod']; ?> | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f4f4; }
        /* Estilo rudo para la imagen */
        .product-img { border: 5px solid #000; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .price-tag { color: #f7ca04; background: #000; padding: 10px 20px; border-radius: 5px; display: inline-block; }
        .navbar { border-bottom: 3px solid #f7ca04; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php">
            <i class="fa-solid fa-arrow-left text-warning"></i> VOLVER AL CATÁLOGO
        </a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-6 mb-4">
            <?php $foto = !empty($producto['imagen_url']) ? '../assets/img/productos/' . $producto['imagen_url'] : '../assets/img/default.jpg'; ?>
            <img src="<?php echo $foto; ?>" class="img-fluid product-img" alt="<?php echo $producto['nombre_prod']; ?>">
        </div>

        <div class="col-md-6">
            <span class="badge bg-warning text-dark mb-2"><?php echo $producto['nombre_categoria']; ?></span>
            <h1 class="fw-bold text-uppercase"><?php echo $producto['nombre_prod']; ?></h1>
            
            <p class="text-muted">
                <i class="fa-solid fa-location-dot text-danger"></i> 
                Disponible en: <strong><?php echo $producto['nombre_sucursal']; ?></strong>
            </p>
            
            <h2 class="price-tag my-3">$<?php echo number_format($producto['precio'], 2); ?></h2>
            
            <div class="my-4">
                <h5><strong><i class="fa-solid fa-file-lines"></i> Descripción:</strong></h5>
                <p class="lead text-dark"><?php echo $producto['descripcion']; ?></p>
            </div>

            <div class="alert <?php echo ($producto['stock'] > 0) ? 'alert-success' : 'alert-danger'; ?> border-2">
                <i class="fa-solid fa-boxes-stacked"></i> 
                Stock disponible: <strong><?php echo $producto['stock']; ?> unidades</strong>
            </div>

            <?php if ($producto['stock'] > 0): ?>
                <div class="d-grid gap-2">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="agendar_cita.php?prod_id=<?php echo $producto['id_producto']; ?>" class="btn btn-dark btn-lg fw-bold shadow">
                            <i class="fa-solid fa-calendar-check text-warning"></i> AGENDAR INSTALACIÓN
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="btn btn-outline-dark btn-lg fw-bold">
                            INICIA SESIÓN PARA COMPRAR
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <button class="btn btn-secondary btn-lg w-100" disabled>PRODUCTO AGOTADO</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="text-center py-5 mt-5 text-muted border-top">
    <p>Gualo Electronic &copy; 2026 - Santiago, Veraguas</p>
</footer>

</body>
</html>