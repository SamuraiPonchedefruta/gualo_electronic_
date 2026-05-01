<?php
session_start();
require_once '../config/conexion.php'; // Ajusta la ruta a tu conexión

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$id_cliente = $_SESSION['user_id'];
$db = DB::getInstance()->getConnection();

// Consulta única para traer todas las citas del cliente con sus relaciones
$query = "SELECT c.*, s.nombre_servicio, u.nombre AS mecanico, m.nombre_marca, mdl.nombre_modelo 
          FROM citas c
          LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
          LEFT JOIN usuarios u ON c.id_mecanico = u.id_usuario
          LEFT JOIN vehiculos_cliente vc ON c.id_vehiculo = vc.id_vehiculo
          LEFT JOIN modelos mdl ON vc.id_modelo = mdl.id_modelo
          LEFT JOIN marcas m ON mdl.id_marca = m.id_marca
          WHERE c.id_cliente = ? 
          ORDER BY c.estado ASC, c.fecha_cita DESC";

$stmt = $db->prepare($query);
$stmt->execute([$id_cliente]);
$todas_las_citas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citas | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="fw-bold mb-4">ESTADO DE MIS CITAS</h2>

        <?php if (empty($todas_las_citas)): ?>
            <div class="alert alert-info">Aún no has solicitado ninguna cita. ¡Anímate a equipar tu nave!</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($todas_las_citas as $cita): ?>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0"><?= $cita['nombre_servicio'] ?></h5>
                                    <!-- Manejo de Estados con Badges -->
                                    <?php if ($cita['estado'] == 0): ?>
                                        <span class="badge bg-secondary text-uppercase">Solicitud Pendiente</span>
                                    <?php elseif ($cita['estado'] == 1): ?>
                                        <span class="badge bg-success text-uppercase">Cita Aprobada</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark text-uppercase">Servicio Realizado</span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="mb-1 text-muted">🚗 <?= "{$cita['nombre_marca']} {$cita['nombre_modelo']}" ?></p>
                                
                                <?php if ($cita['estado'] >= 1): ?>
                                    <hr>
                                    <p class="mb-1"><strong>Fecha:</strong> <?= date('d/m/Y - h:i A', strtotime($cita['fecha_cita'])) ?></p>
                                    <p class="mb-0"><strong>Mecánico:</strong> <?= $cita['mecanico'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="../index.php" class="btn btn-dark">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>