<?php
session_start();
require_once '../config/conexion.php'; 
require_once '../models/Producto.php'; 

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

$db = DB::getInstance()->getConnection();
$productoModel = new Producto();

// --- LÓGICA DE CITAS ---
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

// --- LÓGICA DE PRODUCTOS ---
$productos = $productoModel->obtenerTodos(false);
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
        .card-admin { background: #252525; border: 1px solid #333; border-top: 4px solid #f7ca04; transition: 0.3s; }
        .card-admin:hover { border-color: #f7ca04; }
        .text-gualo { color: #f7ca04; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #f7ca04; font-weight: bold; margin-bottom: 2px; }
        .divider { border-right: 1px solid #444; }
        .nav-tabs .nav-link { color: #aaa; border: none; padding: 12px 25px; }
        .nav-tabs .nav-link.active { background: #f7ca04; color: #000; font-weight: bold; border-radius: 5px 5px 0 0; }
        .img-producto-tabla { width: 70px; height: 50px; object-fit: contain; background: #000; border-radius: 5px; }
        .img-producto-cita { width: 100px; height: 70px; object-fit: contain; background: #000; border-radius: 5px; border: 1px solid #444; }
        @media (max-width: 768px) { .divider { border-right: none; border-bottom: 1px solid #444; margin-bottom: 15px; padding-bottom: 15px; } }
    </style>
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-gualo fw-bold mb-0 text-uppercase"><i class="fa-solid fa-screwdriver-wrench"></i> Panel de Control</h2>
            <p class="text-muted mb-0">Gestión integral de Gualo Electronic</p>
        </div>
        <a href="../index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-house"></i> Inicio</a>
    </div>

    <!-- Pestañas de Navegación -->
    <ul class="nav nav-tabs mb-4 border-secondary" id="adminTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="citas-tab" data-bs-toggle="tab" data-bs-target="#citas">
                <i class="fa-solid fa-calendar-check"></i> CITAS PENDIENTES
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario">
                <i class="fa-solid fa-boxes-stacked"></i> INVENTARIO 4x4
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- SECCIÓN 1: GESTIÓN DE CITAS -->
        <div class="tab-pane fade show active" id="citas">
            <?php if (empty($solicitudes)): ?>
                <div class="alert alert-dark border-secondary text-center py-5">
                    <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                    <h4>¡Todo al día!</h4>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($solicitudes as $s): ?>
                        <div class="col-12 mb-4">
                            <div class="card card-admin p-4 shadow">
                                <div class="row align-items-center">
                                    <!-- Columna 1: Cliente -->
                                    <div class="col-md-4 divider">
                                        <div class="info-label">Cliente y Vehículo</div>
                                        <h4 class="mb-1 text-white"><?= htmlspecialchars($s['cliente']) ?></h4>
                                        <p class="mb-2 text-info small fw-bold">
                                            <i class="fa-solid fa-car"></i> <?= "{$s['nombre_marca']} {$s['nombre_modelo']} ({$s['anio']})" ?>
                                        </p>
                                        <div class="bg-dark p-2 rounded">
                                            <p class="small text-muted mb-0" style="font-style: italic;">
                                                <i class="fa-solid fa-quote-left text-gualo"></i> 
                                                <?= !empty($s['notas']) ? htmlspecialchars($s['notas']) : "Sin notas adicionales." ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Columna 2: Trabajo e Imagen -->
                                    <div class="col-md-4 divider text-center px-4">
                                        <div class="info-label">Trabajo a Realizar</div>
                                        <h5 class="text-warning mb-3 fw-bold"><?= htmlspecialchars($s['nombre_servicio']) ?></h5>
                                        
                                        <?php if (!empty($s['nombre_prod'])): ?>
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="../assets/img/productos/<?= $s['imagen_url'] ?>" 
                                                     class="img-producto-cita mb-2" 
                                                     onerror="this.src='../assets/img/default.jpg';">
                                                <p class="small mb-0 fw-bold text-white"><?= htmlspecialchars($s['nombre_prod']) ?></p>
                                                <p class="text-success fw-bold mb-0">$<?= number_format($s['precio'], 2) ?></p>
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-2 p-2 border border-secondary rounded bg-dark">
                                                <p class="text-muted small mb-0">Sin accesorio vinculado.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Columna 3: Formulario -->
                                    <div class="col-md-4 ps-md-4">
                                        <form action="../actions/procesar_cita.php" method="POST">
                                            <input type="hidden" name="id_cita" value="<?= $s['id_cita'] ?>">
                                            <div class="mb-2">
                                                <label class="info-label">Fecha y Hora</label>
                                                <input type="datetime-local" name="fecha_cita" class="form-control form-control-sm bg-dark text-white border-secondary" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="info-label">Técnico Encargado</label>
                                                <select name="id_mecanico" class="form-select form-select-sm bg-dark text-white border-secondary" required>
                                                    <option value="">-- Seleccionar --</option>
                                                    <?php foreach ($mecanicos as $m): ?>
                                                        <option value="<?= $m['id_usuario'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-warning w-100 fw-bold">AGENDAR SERVICIO</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- SECCIÓN 2: CRUD DE INVENTARIO -->
        <div class="tab-pane fade" id="inventario">
            <div class="card card-admin p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-white mb-0"><i class="fa-solid fa-box"></i> Stock de Accesorios</h4>
                    <a href="form_producto.php" class="btn btn-success btn-sm fw-bold">
                        <i class="fa-solid fa-plus"></i> AGREGAR NUEVO
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="table-warning text-dark">
                            <tr>
                                <th>Imagen</th>
                                <th>Producto</th>
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
                                         class="img-producto-tabla" 
                                         onerror="this.src='../assets/img/default.jpg';">
                                </td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($p['nombre_prod']) ?></span>
                                </td>
                                <td class="text-success fw-bold">$<?= number_format($p['precio'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $p['stock'] > 5 ? 'bg-secondary' : 'bg-danger' ?>">
                                        <?= $p['stock'] ?> disp.
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="form_producto.php?id=<?= $p['id_producto'] ?>" class="btn btn-sm btn-info">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="../actions/eliminar_producto.php?id=<?= $p['id_producto'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('¿Eliminar este accesorio?')">
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