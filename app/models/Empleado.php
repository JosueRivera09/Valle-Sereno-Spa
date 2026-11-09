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

    /**
     * Obtener el catálogo de roles registrados en la base de datos
     */
    public function getRoles(): array {
        $stmt = $this->db->query("SELECT id, nombre, descripcion FROM roles ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener catálogo de servicios/terapias activas para selección de especialidades
     */
    public function getServicios(): array {
        $sql = "
            SELECT s.id, s.id_categoria, s.nombre, s.costo, s.duracion_minutos,
                   COALESCE(c.nombre, 'General') AS categoria_nombre
            FROM servicios s
            LEFT JOIN categorias c ON s.id_categoria = c.id
            WHERE s.estado = 'activo'
            ORDER BY c.nombre ASC, s.nombre ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Sincronizar los servicios asignados al empleado en la tabla relacional empleado_servicios
     */
    public function syncServicios(int $idEmpleado, array $serviciosIds): void {
        $stmtDel = $this->db->prepare("DELETE FROM empleado_servicios WHERE id_empleado = :id");
        $stmtDel->execute(['id' => $idEmpleado]);

        if (!empty($serviciosIds)) {
            $stmtIns = $this->db->prepare("INSERT INTO empleado_servicios (id_empleado, id_servicio) VALUES (:emp, :srv)");
            foreach ($serviciosIds as $srvId) {
                $srvId = (int)$srvId;
                if ($srvId > 0) {
                    $stmtIns->execute(['emp' => $idEmpleado, 'srv' => $srvId]);
                }
            }
        }
    }
}