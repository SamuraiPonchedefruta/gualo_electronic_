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
        /* Fondo general oscuro */
        body { 
            background: #1a1a1a; 
            color: white; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Contenedor del formulario */
        .card-form { 
            background: #252525; 
            border: none;
            border-top: 4px solid #f7ca04; /* El amarillo Gualo */
            border-radius: 8px;
        }

        /* Estilo de las etiquetas (Labels) - Aquí corregimos lo del color */
        label.form-label {
            color: #ffffff !important; /* Blanco puro */
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        /* Estilo de los Inputs y Selects */
        .form-control, .form-select {
            background-color: #333333 !important; /* Gris oscuro para contraste */
            color: #ffffff !important;           /* Texto blanco al escribir */
            border: 1px solid #444444;
            padding: 10px;
        }

        /* Efecto al hacer clic en los campos */
        .form-control:focus, .form-select:focus {
            background-color: #3d3d3d !important;
            border-color: #f7ca04 !important;
            box-shadow: 0 0 0 0.25rem rgba(247, 202, 4, 0.15);
            outline: none;
        }

        /* Color del texto de ayuda */
        .text-muted {
            color: #aaaaaa !important;
        }

        .btn-warning {
            background-color: #f7ca04;
            border: none;
            color: #000;
            transition: 0.3s;
        }

        .btn-warning:hover {
            background-color: #e5bc03;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Botón para volver -->
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
                    <p class="text-muted small">Complete todos los campos para gestionar el inventario</p>
                </div>
                
                <form action="../actions/guardar_producto.php" method="POST" enctype="multipart/form-data">
                    <!-- Campos Ocultos para control -->
                    <input type="hidden" name="id_producto" value="<?= $id_prod ?>">
                    <input type="hidden" name="imagen_actual" value="<?= $producto['imagen_url'] ?? 'default.jpg' ?>">

                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-7 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" 
                                   placeholder="Ej: Suspensión Rough Country 2.5"
                                   value="<?= $producto['nombre_prod'] ?? '' ?>" required>
                        </div>
                        
                        <!-- Precio -->
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Precio de Venta ($)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" 
                                   placeholder="0.00"
                                   value="<?= $producto['precio'] ?? '' ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Stock -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Disponible</label>
                            <input type="number" name="stock" class="form-control" 
                                   placeholder="Cantidad"
                                   value="<?= $producto['stock'] ?? '' ?>" required>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select" required>
                                <option value="" hidden>Seleccione...</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" 
                                        <?= (isset($producto['id_categoria']) && $producto['id_categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                                        <?= $cat['nombre_categoria'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sucursal -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sucursal</label>
                            <select name="sucursal" class="form-select" required>
                                <option value="" hidden>Seleccione...</option>
                                <?php foreach($sucursales as $suc): ?>
                                    <option value="<?= $suc['id_sucursal'] ?>" 
                                        <?= (isset($producto['id_sucursal']) && $producto['id_sucursal'] == $suc['id_sucursal']) ? 'selected' : '' ?>>
                                        <?= $suc['nombre_sucursal'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4 mt-2">
                        <label class="form-label">Foto del Accesorio</label>
                        <div class="p-3 border border-secondary rounded bg-dark d-flex align-items-center">
                            <input type="file" name="imagen" class="form-control-file text-muted" accept="image/*">
                            <?php if($producto && !empty($producto['imagen_url'])): ?>
                                <span class="ms-auto badge bg-secondary">Archivo: <?= $producto['imagen_url'] ?></span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Formatos sugeridos: JPG, PNG o WebP.</small>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2">
                                <i class="fa-solid fa-save"></i> <?= $id_prod ? 'GUARDAR CAMBIOS' : 'REGISTRAR EN SISTEMA' ?>
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

    <!-- Scripts de FontAwesome para iconos -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>