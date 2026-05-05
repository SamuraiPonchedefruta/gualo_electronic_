<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = DB::getInstance()->getConnection();

    // 1. CASO: ADMINISTRADOR AGENDANDO CITA EXISTENTE
    if (isset($_POST['id_cita'])) {
        $id_cita = $_POST['id_cita'];
        $fecha_completa = $_POST['fecha_cita']; 
        $id_mecanico = $_POST['id_mecanico'];

        // Validar que la fecha no venga vacía
        if (empty($fecha_completa)) {
            header("Location: ../views/admin_dashboard.php?res=error");
            exit();
        }

        // Separamos fecha y hora del input datetime-local
        $fecha = date('Y-m-d', strtotime($fecha_completa));
        $hora = date('H:i:s', strtotime($fecha_completa));

        try {
            $sql = "UPDATE citas SET 
                        fecha_cita = ?, 
                        hora_cita = ?, 
                        id_mecanico = ?, 
                        estado = 1 
                    WHERE id_cita = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha, $hora, $id_mecanico, $id_cita]);

            // CORRECCIÓN DE RUTA: admin_dashboard.php en lugar de panel_admin.php
            header("Location: ../views/admin_dashboard.php?res=success");
            exit();

        } catch (PDOException $e) {
            header("Location: ../views/admin_dashboard.php?res=error");
            exit();
        }
    }

    // 2. CASO: CLIENTE CREANDO CITA NUEVA
    else {
        $id_cliente = $_SESSION['user_id'];
        $id_producto = $_POST['id_producto'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $hora = $_POST['hora'] ?? null;
        $notas = $_POST['notas'] ?? "";

        try {
            $sql = "INSERT INTO citas (id_cliente, id_producto, fecha_cita, hora_cita, notas, estado) 
                    VALUES (?, ?, ?, ?, ?, 0)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_cliente, $id_producto, $fecha, $hora, $notas]);

            header("Location: ../views/mis_citas.php?success=1");
            exit();
        } catch (PDOException $e) {
            header("Location: ../views/mis_citas.php?error=1");
            exit();
        }
    }
}