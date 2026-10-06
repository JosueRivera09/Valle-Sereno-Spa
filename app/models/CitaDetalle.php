<?php
require_once __DIR__ . '/Database.php';

class CitaDetalle {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener los servicios asociados a una cita específica
     */
    public function getByCitaId(int $citaId): array {
        $stmt = $this->db->prepare("
            SELECT 
                cd.id,
                cd.id_cita,
                cd.id_servicio,
                s.nombre AS servicio_nombre,
                cd.precio_aplicado,
                cd.duracion_aplicada
            FROM cita_detalles cd
            LEFT JOIN servicios s ON cd.id_servicio = s.id
            WHERE cd.id_cita = :cita_id
        ");
        $stmt->execute(['cita_id' => $citaId]);
        return $stmt->fetchAll();
    }

    /**
     * Registrar un detalle de servicio para una cita
     */
    public function create(int $citaId, int $servicioId, float $precio, int $duracion): bool {
        $stmt = $this->db->prepare("
            INSERT INTO cita_detalles (id_cita, id_servicio, precio_aplicado, duracion_aplicada)
            VALUES (:id_cita, :id_servicio, :precio_aplicado, :duracion_aplicada)
        ");
        return $stmt->execute([
            'id_cita'           => $citaId,
            'id_servicio'       => $servicioId,
            'precio_aplicado'   => $precio,
            'duracion_aplicada' => $duracion
        ]);
    }
}