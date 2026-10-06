<?php
/**
 * Modelo DisponibilidadEmpleado - Capa de Datos
 * Tabla: disponibilidad_empleados
 * dia_semana: 1=Lunes ... 7=Domingo
 */
require_once __DIR__ . '/Database.php';

class DisponibilidadEmpleado {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByEmpleado(int $idEmpleado): array {
        $stmt = $this->db->prepare("
            SELECT * FROM disponibilidad_empleados
            WHERE id_empleado = :id_empleado
            ORDER BY dia_semana ASC, hora_inicio ASC
        ");
        $stmt->execute(['id_empleado' => $idEmpleado]);
        return $stmt->fetchAll();
    }

    public function getAllActivas(): array {
        $stmt = $this->db->query("
            SELECT d.*, e.nombre_completo
            FROM disponibilidad_empleados d
            INNER JOIN empleados e ON e.id = d.id_empleado
            WHERE d.estado = 'activo' AND e.estado = 'activo'
            ORDER BY e.nombre_completo, d.dia_semana, d.hora_inicio
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO disponibilidad_empleados (id_empleado, dia_semana, hora_inicio, hora_fin, estado)
            VALUES (:id_empleado, :dia_semana, :hora_inicio, :hora_fin, :estado)
        ");
        $stmt->execute([
            'id_empleado' => $data['id_empleado'],
            'dia_semana'  => $data['dia_semana'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin'    => $data['hora_fin'],
            'estado'      => $data['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE disponibilidad_empleados SET
                dia_semana = :dia_semana,
                hora_inicio = :hora_inicio,
                hora_fin = :hora_fin,
                estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => $id,
            'dia_semana'  => $data['dia_semana'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin'    => $data['hora_fin'],
            'estado'      => $data['estado'] ?? 'activo'
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM disponibilidad_empleados WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deleteByEmpleado(int $idEmpleado): bool {
        $stmt = $this->db->prepare("DELETE FROM disponibilidad_empleados WHERE id_empleado = :id_empleado");
        return $stmt->execute(['id_empleado' => $idEmpleado]);
    }
}