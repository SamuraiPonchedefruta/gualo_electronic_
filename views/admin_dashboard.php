<?php
session_start();
require_once '../config/conexion.php'; 
require_once '../models/Producto.php'; // Asegúrate de tener este modelo

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

$db = DB::getInstance()->getConnection();
$productoModel = new Producto();

// --- LÓGICA DE CITAS (Tu código original) ---
$queryCitas = "SELECT c.id_cita, u.nombre AS cliente, s.nombre_servicio, m.nombre_marca, mdl.nombre_modelo, vc.anio, c.notas, p.nombre_prod, p.precio, p.imagen_url
          FROM citas c
          JOIN usuarios u ON c.id_cliente = u.id_usuario
          JOIN servicios s ON c.id_servicio = s.id_servicio
          JOIN vehiculos_cliente vc ON c.id_vehiculo = vc.id_vehiculo
          JOIN modelos mdl ON vc.id_modelo = mdl.id_modelo
          JOIN marcas m ON mdl.id_marca = m.id_marca
          LEFT JOIN productos p ON c.id_producto = p.id_producto
          WHERE c.estado = 0";
$stmt = $db->prepare($queryCitas);
$stmt->execute();
$solicitudes = $stmt->fetchAll();

$stmtMec = $db->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_rol = 2");
$stmtMec->execute();
$mecanicos = $stmtMec->fetchAll();

// --- LÓGICA DE PRODUCTOS (Para el CRUD) ---
$productos = $productoModel->obtenerTodos(); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #1a1a1a; color: white; font-family: 'Segoe UI', sans-serif; }
        .card-admin { background: #252525; border: 1px solid #333; border-top: 4px solid #f7ca04; }
        .text-gualo { color: #f7ca04; }
        .nav-tabs .nav-link { color: #aaa; border: none; }
        .nav-tabs .nav-link.active { background: #f7ca04; color: #000; fw-bold; }
        .img-producto { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; }
        .table-dark-gualo { background: #252525; color: white; }
    </style>
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gualo fw-bold text-uppercase"><i class="fa-solid fa-gauge"></i> Panel de Control</h2>
        <a href="../index.php" class="btn btn-outline-warning btn-sm"><i class="fa-solid fa-house"></i> Volver a la Tienda</a>
    </div>

    <!-- Pestañas de Navegación -->
    <ul class="nav nav-tabs mb-4 border-secondary" id="adminTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold" id="citas-tab" data-bs-toggle="tab" data-bs-target="#citas">
                <i class="fa-solid fa-calendar-check"></i> Citas Pendientes
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario">
                <i class="fa-solid fa-boxes-stacked"></i> Gestionar Inventario
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- SECCIÓN 1: GESTIÓN DE CITAS -->
        <div class="tab-pane fade show active" id="citas">
            <?php if (empty($solicitudes)): ?>
                <div class="alert alert-dark text-center py-5 border-secondary">
                    <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                    <h4>¡Todo al día!</h4>
                </div>
            <?php else: ?>
                <?php foreach ($solicitudes as $s): ?>
                    <!-- Tu diseño original de card de citas aquí -->
                    <div class="card card-admin p-4 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-4 border-end border-secondary">
                                <div class="small text-warning fw-bold uppercase">Cliente</div>
                                <h4><?= htmlspecialchars($s['cliente']) ?></h4>
                                <p class="text-info small mb-0"><i class="fa-solid fa-car"></i> <?= "{$s['nombre_marca']} {$s['nombre_modelo']}" ?></p>
                            </div>
                            <div class="col-md-4 text-center border-end border-secondary">
                                <span class="badge bg-warning text-dark mb-2"><?= htmlspecialchars($s['nombre_servicio']) ?></span>
                                <?php if($s['nombre_prod']): ?>
                                    <p class="small mb-0 text-white"><?= htmlspecialchars($s['nombre_prod']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 ps-4">
                                <form action="../actions/procesar_cita.php" method="POST" class="row g-2">
                                    <input type="hidden" name="id_cita" value="<?= $s['id_cita'] ?>">
                                    <div class="col-12">
                                        <input type="datetime-local" name="fecha_cita" class="form-control form-control-sm bg-dark text-white border-secondary" required>
                                    </div>
                                    <div class="col-12">
                                        <select name="id_mecanico" class="form-select form-select-sm bg-dark text-white border-secondary" required>
                                            <option value="">Seleccionar Técnico</option>
                                            <?php foreach ($mecanicos as $m): ?>
                                                <option value="<?= $m['id_usuario'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-sm fw-bold w-100 mt-2">AGENDAR</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SECCIÓN 2: CRUD DE INVENTARIO (NUEVO) -->
        <div class="tab-pane fade" id="inventario">
            <div class="card card-admin p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-white mb-0">Control de Accesorios</h4>
                    <a href="form_producto.php" class="btn btn-success fw-bold">
                        <i class="fa-solid fa-plus"></i> Nuevo Accesorio
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="table-warning text-dark">
                            <tr>
                                <th>Imagen</th>
                                <th>Accesorio</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $p): ?>
                            <tr>
                                <td>
                                    <img src="../assets/img/productos/<?= $p['imagen_url'] ?>" 
                                         class="img-producto" 
                                         onerror="this.src='../assets/img/default.jpg';">
                                </td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($p['nombre_prod']) ?></span><br>
                                    <small class="text-muted"><?= htmlspecialchars($p['nombre_categoria']) ?></small>
                                </td>
                                <td class="text-success fw-bold">$<?= number_format($p['precio'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $p['stock'] > 5 ? 'bg-secondary' : 'bg-danger' ?>">
                                        <?= $p['stock'] ?> disp.
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="form_producto.php?id=<?= $p['id_producto'] ?>" class="btn btn-sm btn-info me-1">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="../actions/eliminar_producto.php?id=<?= $p['id_producto'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Eliminar accesorio?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>