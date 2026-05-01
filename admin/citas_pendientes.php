<?php
session_start();
require_once '../../config/conexion.php'; 

// Verificamos que sea Admin (id_rol = 1)
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../login.php");
    exit();
}

$db = DB::getInstance()->getConnection();

// Traer citas en estado 0 (Solicitud)
// Ajustado para obtener el nombre del servicio también
$stmt = $db->prepare("
    SELECT c.id_cita, u.nombre AS cliente, m.nombre_marca, mdl.nombre_modelo, vc.anio, s.nombre_servicio, c.notas
    FROM citas c
    JOIN usuarios u ON c.id_cliente = u.id_usuario
    JOIN vehiculos_cliente vc ON c.id_vehiculo = vc.id_vehiculo
    JOIN modelos mdl ON vc.id_modelo = mdl.id_modelo
    JOIN marcas m ON mdl.id_marca = m.id_marca
    JOIN servicios s ON c.id_servicio = s.id_servicio
    WHERE c.estado = 0
");
$stmt->execute();
$solicitudes = $stmt->fetchAll();

// Traer mecánicos (id_rol = 2) de tu tabla usuarios
$stmtMec = $db->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_rol = 2");
$stmtMec->execute();
$mecanicos = $stmtMec->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; }
        .card { background: #1e1e1e; border: 1px solid #f7ca04; border-radius: 10px; }
        .text-warning-gualo { color: #f7ca04; }
        .form-control, .form-select { background: #2a2a2a; color: white; border-color: #444; }
    </style>
</head>
<body class="container py-5">
    <h2 class="text-center mb-5 fw-bold text-warning-gualo">PANEL DE ADMINISTRACIÓN: SOLICITUDES</h2>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info bg-dark text-light border-info">No hay solicitudes nuevas por el momento.</div>
    <?php endif; ?>

    <div class="row">
        <?php foreach($solicitudes as $s): ?>
        <div class="col-md-6 mb-4">
            <div class="card p-4 shadow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="text-warning-gualo mb-1"><?= htmlspecialchars($s['cliente']) ?></h5>
                        <p class="small text-muted">Servicio: <strong><?= htmlspecialchars($s['nombre_servicio']) ?></strong></p>
                    </div>
                    <span class="badge bg-secondary text-uppercase">Solicitud</span>
                </div>
                
                <p class="mb-1"><strong>Vehículo:</strong> <?= "{$s['nombre_marca']} {$s['nombre_modelo']} ({$s['anio']})" ?></p>
                <p class="small italic">"<?= htmlspecialchars($s['notas']) ?: 'Sin notas adicionales' ?>"</p>
                
                <hr class="border-warning">
                
                <form action="../../actions/aprobar_cita.php" method="POST">
                    <input type="hidden" name="id_cita" value="<?= $s['id_cita'] ?>">
                    
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Fecha y Hora de Cita:</label>
                            <input type="datetime-local" name="fecha_programada" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Mecánico Asignado:</label>
                            <select name="id_mecanico" class="form-select" required>
                                <option value="">Seleccionar Mecánico...</option>
                                <?php foreach($mecanicos as $mec): ?>
                                    <option value="<?= $mec['id_usuario'] ?>"><?= htmlspecialchars($mec['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 mt-3 fw-bold">APROBAR CITA</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>