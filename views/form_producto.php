<?php
session_start();
require_once '../config/conexion.php';
require_once '../models/Producto.php';

$productoModel = new Producto();
$id_prod = $_GET['id'] ?? null;
$producto = null;

// Si recibimos un ID, buscamos los datos para EDITAR
if ($id_prod) {
    $producto = $productoModel->obtenerPorId($id_prod);
}

// Traer categorías y sucursales de la BD para los Selects
$db = DB::getInstance()->getConnection();
$categorias = $db->query("SELECT * FROM categorias")->fetchAll();
$sucursales = $db->query("SELECT * FROM sucursales")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id_prod ? 'Editar' : 'Nuevo' ?> Accesorio | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #1a1a1a; color: white; font-family: 'Segoe UI', sans-serif; }
        .card-form { background: #252525; border: none; border-top: 4px solid #f7ca04; border-radius: 8px; }
        label.form-label { color: #ffffff !important; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .form-control, .form-select { background-color: #333333 !important; color: #ffffff !important; border: 1px solid #444444; padding: 10px; }
        .form-control:focus, .form-select:focus { border-color: #f7ca04 !important; box-shadow: 0 0 0 0.25rem rgba(247, 202, 4, 0.15); }
        .btn-warning { background-color: #f7ca04; border: none; color: #000; font-weight: bold; }
    </style>
</head>
<body class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-4">
                <a href="admin_dashboard.php" class="text-warning text-decoration-none small fw-bold">
                    <i class="fa-solid fa-arrow-left"></i> VOLVER AL PANEL
                </a>
            </div>

            <div class="card card-form p-4 shadow-lg">
                <div class="text-center mb-4">
                    <h3 class="text-warning fw-bold">
                        <i class="fa-solid <?= $id_prod ? 'fa-pen-to-square' : 'fa-plus-circle' ?>"></i>
                        <?= $id_prod ? 'EDITAR ACCESORIO' : 'NUEVO ACCESORIO' ?>
                    </h3>
                    <p class="text-muted small">Gestión de inventario para vehículos 4x4</p>
                </div>
                
                <form action="../actions/guardar_producto.php" method="POST" enctype="multipart/form-data">
                    <!-- ID del producto para saber si es UPDATE o INSERT -->
                    <input type="hidden" name="id_producto" value="<?= $id_prod ?>">
                    <input type="hidden" name="imagen_actual" value="<?= $producto['imagen_url'] ?? 'default.jpg' ?>">

                    <div class="row">
                        <!-- Nombre del Producto (Corregido a nombre_prod) -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" name="nombre_prod" class="form-control" 
                                   placeholder="Ej: Snorkel para Suzuki Jimny"
                                   value="<?= $producto['nombre_prod'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Precio -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" 
                                   value="<?= $producto['precio'] ?? '' ?>" required>
                        </div>
                        <!-- Stock -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" 
                                   value="<?= $producto['stock'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Categoría (Corregido a id_categoria) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="id_categoria" class="form-select" required>
                                <option value="" hidden>Seleccione...</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" 
                                        <?= (isset($producto['id_categoria']) && $producto['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                                        <?= $cat['nombre_categoria'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sucursal (Corregido a id_sucursal) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sucursal</label>
                            <select name="id_sucursal" class="form-select">
                                <option value="">Ninguna / Global</option>
                                <?php foreach($sucursales as $suc): ?>
                                    <option value="<?= $suc['id_sucursal'] ?>" 
                                        <?= (isset($producto['id_sucursal']) && $producto['id_sucursal'] == $suc['id_sucursal']) ? 'selected' : '' ?>>
                                        <?= $suc['nombre_sucursal'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Descripción (Añadido para completar el modelo) -->
                    <div class="mb-3">
                        <label class="form-label">Descripción Técnica</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?= $producto['descripcion'] ?? '' ?></textarea>
                    </div>

                    <!-- Foto -->
                    <div class="mb-4">
                        <label class="form-label">Foto del Accesorio</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                        <?php if($producto && !empty($producto['imagen_url'])): ?>
                            <div class="mt-2 small text-warning">Imagen actual: <?= $producto['imagen_url'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-warning w-100 py-2">
                                <i class="fa-solid fa-save"></i> <?= $id_prod ? 'ACTUALIZAR DATOS' : 'CREAR PRODUCTO' ?>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="admin_dashboard.php" class="btn btn-outline-light w-100 py-2">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>