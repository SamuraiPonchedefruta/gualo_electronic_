<?php
session_start();
require_once '../models/Producto.php';

$productoModel = new Producto();
$termino = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Si hay un término, buscamos. Si no, traemos todo.
if (!empty($termino)) {
    $productos = $productoModel->buscarProductos($termino);
} else {
    $productos = $productoModel->obtenerTodos(); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <!-- Puedes incluir tu Navbar aquí -->

    <div class="container my-5">
        <h2 class="fw-bold mb-4">
            <i class="fa-solid fa-magnifying-glass text-warning"></i> 
            Resultados para: "<?php echo htmlspecialchars($termino); ?>"
        </h2>

        <div class="row g-4">
            <?php if (empty($productos)): ?>
                <div class="col-12 text-center py-5">
                    <div class="alert alert-warning">
                        No encontramos accesorios que coincidan con "<?php echo htmlspecialchars($termino); ?>". 
                        <br>Intenta con palabras como "Suspensión", "Luces" o "Jimny".
                    </div>
                    <a href="../index.php" class="btn btn-dark">Volver al inicio</a>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $producto): 
                    $imagenPath = !empty($producto['imagen_url']) ? '../assets/img/productos/' . $producto['imagen_url'] : '../assets/img/default.jpg';
                ?>
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo $imagenPath; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h6 class="text-muted small"><?php echo htmlspecialchars($producto['nombre_categoria']); ?></h6>
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($producto['nombre_prod']); ?></h5>
                                <p class="text-primary fw-bold">$<?php echo number_format($producto['precio'], 2); ?></p>
                                <a href="detalle_producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn btn-warning btn-sm fw-bold w-100">VER MÁS</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>