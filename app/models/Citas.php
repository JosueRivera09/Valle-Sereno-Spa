<?php
/**
 * Modelo de Cita (Capa de Acceso y Modelo de Datos para Citas y Agenda Terapéutica)
 * Alineado con la estructura oficial de spa_db:
 * - Tabla citas: (id, id_cliente, id_empleado, fecha, hora_inicio, hora_fin, total, estado, observaciones, creado_en)
 * - Tabla clientes: (id, nombre_completo, correo, telefono)
 * - Tabla empleados: (id, nombre_completo, correo, telefono, cargo)
 */

require_once __DIR__ . '/Database.php';

class Citas {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todas las citas para el listado general / agenda, 
     * incluyendo nombres de cliente y empleado (terapeuta).
     */
    public function getAll(?int $idEmpleado = null): array {
        $sql = "
            SELECT 
                c.id,
                c.id_cliente,
                c.id_empleado,
                c.fecha,
                c.hora_inicio,
                c.hora_fin,
                c.total,
                c.estado,
                c.observaciones,
                c.creado_en,
                coalesce(cl.nombre_completo, 'Sin Cliente') AS cliente_nombre,
                coalesce(e.nombre_completo, 'Sin Asignar') AS empleado_nombre
            FROM citas c
            LEFT JOIN clientes cl ON c.id_cliente = cl.id
            LEFT JOIN empleados e ON c.id_empleado = e.id
        ";

        $params = [];
        if ($idEmpleado !== null) {
            $sql .= " WHERE c.id_empleado = :id_empleado";
            $params['id_empleado'] = $idEmpleado;
        }

        $sql .= " ORDER BY c.fecha DESC, c.hora_inicio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Buscar una cita por su ID primario
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                c.id,
                c.id_cliente,
                c.id_empleado,
                c.fecha,
                c.hora_inicio,
                c.hora_fin,
                c.total,
                c.estado,
                c.observaciones,
                c.creado_en,
                coalesce(cl.nombre_completo, 'Sin Cliente') AS cliente_nombre,
                coalesce(e.nombre_completo, 'Sin Asignar') AS empleado_nombre
            FROM citas c
            LEFT JOIN clientes cl ON c.id_cliente = cl.id
            LEFT JOIN empleados e ON c.id_empleado = e.id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $cita = $stmt->fetch();
        return $cita ?: null;
    }

    /**
     * Registrar una nueva cita en el sistema
     */
    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO citas (
                id_cliente, 
                id_empleado, 
                fecha, 
                hora_inicio, 
                hora_fin, 
                total, 
                estado, 
                observaciones, 
                creado_en
            ) VALUES (
                :id_cliente, 
                :id_empleado, 
                :fecha, 
                :hora_inicio, 
                :hora_fin, 
                :total, 
                :estado, 
                :observaciones, 
                NOW()
            )
        ");

        return $stmt->execute([
            'id_cliente'    => $data['id_cliente'],
            'id_empleado'   => $data['id_empleado'],
            'fecha'         => $data['fecha'],
            'hora_inicio'   => $data['hora_inicio'],
            'hora_fin'      => $data['hora_fin'],
            'total'         => $data['total'] ?? 0.00,
            'estado'        => $data['estado'] ?? 'pendiente',
            'observaciones' => $data['observaciones'] ?? null
        ]);
    }

    /**
     * Actualizar el estado de una cita (ej. 'pendiente', 'atendido', 'cancelado')
     */
    public function updateStatus(int $id, string $nuevoEstado): bool {
        $stmt = $this->db->prepare("
            UPDATE citas 
            SET estado = :estado 
            WHERE id = :id
        ");
        return $stmt->execute([
            'estado' => $nuevoEstado,
            'id'     => $id
        ]);
    }
}
