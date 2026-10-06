<?php
/**
 * Modelo Empleado - Capa de Datos
 * Tabla: empleados
 */
require_once __DIR__ . '/Database.php';

class Empleado {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(string $estado = null): array {
        $sql = "SELECT * FROM empleados";
        $params = [];
        if ($estado !== null) {
            $sql .= " WHERE estado = :estado";
            $params['estado'] = $estado;
        }
        $sql .= " ORDER BY nombre_completo ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM empleados WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO empleados (nombre_completo, telefono, correo, cargo, especialidades, estado)
            VALUES (:nombre_completo, :telefono, :correo, :cargo, :especialidades, :estado)
        ");
        $stmt->execute([
            'nombre_completo' => $data['nombre_completo'],
            'telefono'        => $data['telefono'],
            'correo'          => $data['correo'],
            'cargo'           => $data['cargo'],
            'especialidades'  => $data['especialidades'] ?? null,
            'estado'          => $data['estado'] ?? 'activo'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE empleados SET
                nombre_completo = :nombre_completo,
                telefono = :telefono,
                correo = :correo,
                cargo = :cargo,
                especialidades = :especialidades,
                estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'              => $id,
            'nombre_completo' => $data['nombre_completo'],
            'telefono'        => $data['telefono'],
            'correo'          => $data['correo'],
            'cargo'           => $data['cargo'],
            'especialidades'  => $data['especialidades'] ?? null,
            'estado'          => $data['estado'] ?? 'activo'
        ]);
    }

    public function delete(int $id): bool {
        // Soft delete recomendado
        $stmt = $this->db->prepare("UPDATE empleados SET estado = 'inactivo' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}