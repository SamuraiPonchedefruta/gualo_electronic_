<?php
/**
 * Clase Notificacion - Gualo Electronic (Observer System)
 */
class Notificacion {
    private PDO $db;
 
    public function __construct(PDO $db) {
        $this->db = $db;
    }
 
    public function contarNoLeidas() {
        $sql = "SELECT COUNT(*) FROM alertas_stock WHERE leida = 0 AND estado = 1";
        return $this->db->query($sql)->fetchColumn();
    }
 
    public function obtenerRecientes($limite = 8) {
        // ✅ LEFT JOIN en ambas tablas: si no encuentra el producto o proveedor,
        //    igual muestra la alerta (no la descarta como haría INNER JOIN)
        // ✅ contacto_nombre es el nombre correcto en tu tabla proveedores
        $sql = "SELECT 
                    n.id_alerta, 
                    n.id_producto, 
                    n.stock_actual, 
                    n.fecha_alerta, 
                    n.estado, 
                    n.leida,
                    n.nombre_prod,
                    prov.id_provider,
                    prov.nombre_empresa, 
                    prov.contacto_nombre,
                    prov.telefono 
                FROM alertas_stock n
                LEFT JOIN productos p   ON n.id_producto = p.id_producto
                LEFT JOIN proveedores prov ON p.id_provider = prov.id_provider
                WHERE n.estado = 1 
                ORDER BY n.fecha_alerta DESC 
                LIMIT :limite";
 
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en Notificacion::obtenerRecientes: " . $e->getMessage());
            return [];
        }
    }
 
    public function marcarTodasLeidas(): void {
        $this->db->exec("UPDATE alertas_stock SET leida = 1 WHERE leida = 0");
    }
 
    public function desactivarAlerta(int $id): bool {
        try {
            $sql = "UPDATE alertas_stock SET estado = 0, leida = 1 WHERE id_alerta = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            error_log("Error al desactivar alerta ID $id: " . $e->getMessage());
            return false;
        }
    }
}
