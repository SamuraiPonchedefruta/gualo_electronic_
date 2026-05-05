<?php
class Notificacion {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function contarNoLeidas() {
        // Según tu SQL, filtramos por leida = 0 y estado = 1
        $sql = "SELECT COUNT(*) FROM alertas_stock WHERE leida = 0 AND estado = 1";
        return $this->db->query($sql)->fetchColumn();
    }

    public function obtenerRecientes($limite = 8) {
    $sql = "SELECT n.* FROM alertas_stock n WHERE n.estado = 1 ORDER BY n.fecha_alerta DESC LIMIT :limite";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function marcarTodasLeidas(): void {
        $this->db->exec("UPDATE alertas_stock SET leida = 1 WHERE leida = 0");
    }

    // Dentro de la clase Notificacion en Notificacion.php
public function desactivarAlerta(int $id): bool {
    $sql = "UPDATE alertas_stock SET estado = 0, leida = 1 WHERE id_alerta = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $id]);
}
}