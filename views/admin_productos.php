<?php
session_start();
require_once '../models/Producto.php';
// Seguridad: Solo admin
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 1) { header("Location: ../login.php"); exit(); }

$productoModel = new Producto();
$productos = $productoModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario | Gualo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Inventario Gualo Electronic</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                + Nuevo Producto
            </button>
        </div>

        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p): ?>
                <tr>
                    <td><img src="../assets/img/productos/<?php echo $p['imagen_url']; ?>" width="50"></td>
                    <td><?php echo $p['nombre_prod']; ?></td>
                    <td>$<?php echo $p['precio']; ?></td>
                    <td><span class="badge bg-info"><?php echo $p['stock']; ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-warning">Editar</button>
                        <button class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="../actions/subir_producto.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header"><h5>Agregar Accesorio 4x4</h5></div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre del producto" required>
                    <input type="number" name="precio" class="form-control mb-2" placeholder="Precio" required>
                    <input type="number" name="stock" class="form-control mb-2" placeholder="Stock inicial" required>
                    <input type="file" name="imagen" class="form-control mb-2" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar en Bodega</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>