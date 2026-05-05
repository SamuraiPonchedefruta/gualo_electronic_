<?php
session_start();
require_once '../config/conexion.php'; 

// 1. Verificación de seguridad: Solo clientes (Rol 3) pueden entrar aquí
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 3) { 
    header("Location: ../login.php"); 
    exit(); 
}

$id_cliente_session = $_SESSION['user_id'];
$db = DB::getInstance()->getConnection();

try {
    // 2. Consulta optimizada usando 'id_cliente' (como en tu DB) y LEFT JOINs para evitar errores si faltan datos
    $query = "SELECT 
                c.id_cita, 
                c.fecha_cita, 
                c.hora_cita, 
                c.estado, 
                c.notas,
                s.nombre_servicio, 
                u.nombre AS mecanico, 
                m.nombre_marca, 
                mdl.nombre_modelo 
              FROM citas c
              LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
              LEFT JOIN usuarios u ON c.id_mecanico = u.id_usuario
              LEFT JOIN vehiculos_cliente vc ON c.id_vehiculo = vc.id_vehiculo
              LEFT JOIN modelos mdl ON vc.id_modelo = mdl.id_modelo
              LEFT JOIN marcas m ON mdl.id_marca = m.id_marca
              WHERE c.id_cliente = ? 
              ORDER BY c.fecha_cita DESC";

    $stmt = $db->prepare($query);
    $stmt->execute([$id_cliente_session]);
    $todas_las_citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar tus citas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas | Gualo Electronic</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-cita { border-radius: 15px; transition: transform 0.2s; }
        .card-cita:hover { transform: translateY(-5px); }
        .badge-estado { font-size: 0.8rem; padding: 0.6em 1em; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fa-solid fa-calendar-days text-primary"></i> ESTADO DE MIS CITAS</h2>
            <a href="../index.php" class="btn btn-outline-dark btn-sm"><i class="fa-solid fa-house"></i> Inicio</a>
        </div>

        <?php if (empty($todas_las_citas)): ?>
            <div class="alert alert-light shadow-sm text-center py-5">
                <i class="fa-solid fa- car-on fa-3x mb-3 text-muted"></i>
                <p class="h5">Aún no tienes citas registradas.</p>
                <a href="../index.php" class="btn btn-primary mt-3">Agendar mi primera cita</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($todas_las_citas as $cita): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm border-0 card-cita">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="fw-bold text-dark mb-0">
                                        <?= $cita['nombre_servicio'] ?? 'Servicio General' ?>
                                    </h5>
                                    
                                    <!-- Manejo de Estados con Colores -->
                                    <?php 
                                        $estado_clase = 'bg-secondary';
                                        $estado_texto = 'Pendiente';
                                        if ($cita['estado'] == 1) { $estado_clase = 'bg-success'; $estado_texto = 'Aprobada'; }
                                        if ($cita['estado'] == 2) { $estado_clase = 'bg-info text-dark'; $estado_texto = 'Finalizada'; }
                                    ?>
                                    <span class="badge rounded-pill <?= $estado_clase ?> badge-estado">
                                        <?= $estado_texto ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted mb-2">
                                    <i class="fa-solid fa-car"></i> 
                                    <?= ($cita['nombre_marca']) ? "{$cita['nombre_marca']} {$cita['nombre_modelo']}" : "Vehículo no especificado" ?>
                                </p>

                                <div class="bg-light p-3 rounded-3 mt-3">
                                    <div class="small text-muted mb-1"><i class="fa-regular fa-calendar"></i> Fecha y Hora:</div>
                                    <div class="fw-bold">
                                        <?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?> - <?= date('h:i A', strtotime($cita['hora_cita'])) ?>
                                    </div>
                                    
                                    <?php if ($cita['mecanico']): ?>
                                        <div class="small text-muted mt-2"><i class="fa-solid fa-user-gear"></i> Mecánico asignado:</div>
                                        <div class="fw-bold"><?= $cita['mecanico'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($cita['notas'])): ?>
                                    <div class="mt-3">
                                        <small class="text-muted"><strong>Nota:</strong> <?= htmlspecialchars($cita['notas']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>