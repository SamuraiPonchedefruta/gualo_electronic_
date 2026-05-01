<?php
session_start();
require_once '../models/Producto.php';
require_once '../config/conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id_prod = $_GET['prod_id'] ?? null;
if (!$id_prod) { header("Location: ../index.php"); exit(); }

$productoModel = new Producto();
$producto = $productoModel->obtenerPorId($id_prod);

// CONEXIÓN Y CONSULTA DE VEHÍCULOS DEL USUARIO
// Se cambió el alias 'mod' por 'mdl' para evitar errores de palabra reservada
$db = DB::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT vc.id_vehiculo, m.nombre_marca, mdl.nombre_modelo, vc.anio 
    FROM vehiculos_cliente vc
    JOIN modelos mdl ON vc.id_modelo = mdl.id_modelo
    JOIN marcas m ON mdl.id_marca = m.id_marca
    WHERE vc.id_cliente = ?
");
$stmt->execute([$_SESSION['user_id']]);
$mis_vehiculos = $stmt->fetchAll();

// CONSULTA DE SERVICIOS DISPONIBLES
$stmtServ = $db->query("SELECT id_servicio, nombre_servicio FROM servicios");
$servicios = $stmtServ->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Instalación | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1a1a1a; color: white; }
        .card { background: #000; border: 3px solid #f7ca04; border-radius: 15px; }
        .form-control, .form-select { background: #333; border: 1px solid #f7ca04; color: white; }
        .form-control:focus, .form-select:focus { background: #444; color: #fff; border-color: #f7ca04; box-shadow: none; }
        label { font-weight: bold; margin-bottom: 5px; color: #f7ca04; }
        .btn-outline-warning:hover { color: #000; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow-lg">
                    <h2 class="text-center fw-bold text-warning mb-4">SOLICITAR INSTALACIÓN</h2>
                    
                    <div class="alert alert-secondary border-warning text-dark">
                        <!-- Usamos nombre_prod según el DESCRIBE de tu tabla productos -->
                        <strong>Accesorio:</strong> <?php echo htmlspecialchars($producto['nombre_prod']); ?><br>
                        <strong>Taller:</strong> Gualo Electronic (Santiago)
                    </div>

                    <form action="../actions/guardar_cita.php" method="POST">
                        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($id_prod); ?>">

                        <!-- SELECCIÓN DE VEHÍCULO REGISTRADO -->
                        <div class="mb-3">
                            <label>Selecciona tu Vehículo:</label>
                            <select name="id_vehiculo" class="form-select" required>
                                <?php if (empty($mis_vehiculos)): ?>
                                    <option value="">No tienes vehículos registrados...</option>
                                <?php else: ?>
                                    <option value="">Selecciona un vehículo...</option>
                                    <?php foreach ($mis_vehiculos as $v): ?>
                                        <option value="<?= $v['id_vehiculo']; ?>">
                                            <?= htmlspecialchars("{$v['nombre_marca']} {$v['nombre_modelo']} ({$v['anio']})"); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="mt-2 text-end">
                                <a href="registrar_carro.php" class="btn btn-sm btn-outline-warning small"> + Registrar nuevo vehículo</a>
                            </div>
                        </div>

                        <!-- SELECCIÓN DE SERVICIO -->
                        <div class="mb-3">
                            <label>Tipo de Servicio:</label>
                            <select name="id_servicio" class="form-select" required>
                                <option value="">¿Qué instalación deseas?</option>
                                <?php foreach ($servicios as $s): ?>
                                    <option value="<?= $s['id_servicio']; ?>"><?= htmlspecialchars($s['nombre_servicio']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label>Notas para el taller:</label>
                            <textarea name="notas" class="form-control" rows="2" placeholder="Ej: El accesorio es un regalo..."></textarea>
                        </div>

                        <?php if (!empty($mis_vehiculos)): ?>
                            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-uppercase">Enviar Solicitud de Cita</button>
                        <?php else: ?>
                            <div class="alert alert-danger text-center">
                                Debes registrar un vehículo antes de pedir una cita.
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <a href="detalle_producto.php?id=<?php echo $id_prod; ?>" class="text-muted small">Cancelar y volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>