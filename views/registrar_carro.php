<?php
session_start();
require_once '../config/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$db = DB::getInstance()->getConnection();

// Traemos todas las marcas disponibles
$marcas = $db->query("SELECT * FROM marcas ORDER BY nombre_marca ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Vehículo | Gualo Electronic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1a1a1a; color: white; }
        .card { background: #000; border: 3px solid #f7ca04; border-radius: 15px; }
        .form-select, .form-control { background: #333; border: 1px solid #f7ca04; color: white; }
        label { color: #f7ca04; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4 shadow-lg">
                    <h3 class="text-center text-warning mb-4">REGISTRAR MI AUTO</h3>
                    
                    <form action="../actions/guardar_vehiculo.php" method="POST">
                        
                        <div class="mb-3">
                            <label>1. Selecciona la Marca:</label>
                            <select id="marca" class="form-select" required>
                                <option value="">Seleccione marca...</option>
                                <?php foreach($marcas as $m): ?>
                                    <option value="<?= $m['id_marca'] ?>"><?= $m['nombre_marca'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>2. Selecciona el Modelo:</label>
                            <!-- Este select se llenará automáticamente con JS según la marca -->
                            <select name="id_modelo" id="modelo" class="form-select" required disabled>
                                <option value="">Primero elige una marca...</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label>3. Año del Vehículo:</label>
                            <input type="number" name="anio" class="form-control" placeholder="Ej: 2024" min="1950" max="2027" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold">GUARDAR VEHÍCULO</button>
                        
                        <div class="text-center mt-3">
                            <a href="javascript:history.back()" class="text-muted small">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para cargar modelos según la marca sin recargar la página -->
    <script>
        document.getElementById('marca').addEventListener('change', function() {
            let idMarca = this.value;
            let selectModelo = document.getElementById('modelo');
            
            if(idMarca) {
                fetch(`../actions/get_modelos.php?id_marca=${idMarca}`)
                    .then(res => res.json())
                    .then(data => {
                        selectModelo.innerHTML = '<option value="">Seleccione modelo...</option>';
                        data.forEach(m => {
                            selectModelo.innerHTML += `<option value="${m.id_modelo}">${m.nombre_modelo}</option>`;
                        });
                        selectModelo.disabled = false;
                    });
            } else {
                selectModelo.disabled = true;
                selectModelo.innerHTML = '<option value="">Primero elige una marca...</option>';
            }
        });
    </script>
</body>
</html>