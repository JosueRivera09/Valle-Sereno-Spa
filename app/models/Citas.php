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
            'estado'        => $data['estado'] ?? 'Pendiente',
            'observaciones' => $data['observaciones'] ?? null
        ]);
    }

    /**
     * Agendar una cita de forma ATÓMICA (transacción + bloqueo FOR UPDATE):
     *  - Verifica y bloquea el rango del terapeuta para evitar doble agendamiento por carreras (TOCTOU).
     *  - Si se indica un servicio del catálogo, calcula hora_fin y total en el SERVIDOR
     *    (el cliente nunca controla el monto) y persiste el detalle en cita_detalles.
     * @return array ['success' => bool, 'mensaje' => string, 'id' => int|null]
     */
    public function agendarCita(array $data, ?int $idServicio = null): array {
        try {
            $this->db->beginTransaction();

            $total   = $data['total'] ?? 0.00;
            $horaFin = $data['hora_fin'];

            // 1. Servicio del catálogo: total y duración se derivan en el servidor
            //    (el cliente nunca controla el monto de la cita)
            if ($idServicio !== null && $idServicio > 0) {
                $stmtSrv = $this->db->prepare("
                    SELECT costo, duracion_minutos FROM servicios 
                    WHERE id = :id AND estado = 'activo' 
                    LIMIT 1
                ");
                $stmtSrv->execute(['id' => $idServicio]);
                $servicio = $stmtSrv->fetch();

                if (!$servicio) {
                    $this->db->rollBack();
                    return ['success' => false, 'mensaje' => 'El servicio seleccionado no existe o está inactivo.', 'id' => null];
                }

                $total   = (float)$servicio['costo'];
                $horaFin = date('H:i', strtotime($data['fecha'] . ' ' . $data['hora_inicio']) + ((int)$servicio['duracion_minutos'] * 60));

                if ($horaFin <= $data['hora_inicio']) {
                    $this->db->rollBack();
                    return ['success' => false, 'mensaje' => 'La duración del servicio excede el día seleccionado. Elija otro horario.', 'id' => null];
                }
            }

            // 2. Bloqueo pesimista del rango del terapeuta (evita doble agendamiento por carreras)
            $lock = $this->db->prepare("
                SELECT id FROM citas 
                WHERE id_empleado = :id_empleado 
                  AND fecha = :fecha 
                  AND estado != 'Cancelada'
                  AND :hora_inicio < hora_fin 
                  AND :hora_fin > hora_inicio
                FOR UPDATE
            ");
            $lock->execute([
                'id_empleado' => $data['id_empleado'],
                'fecha'       => $data['fecha'],
                'hora_inicio' => $data['hora_inicio'],
                'hora_fin'    => $horaFin
            ]);

            if ($lock->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'mensaje' => 'El terapeuta ya tiene una cita que se cruza con este horario.', 'id' => null];
            }

            // 3. Insertar la cita con estado canónico 'Pendiente'
            $stmt = $this->db->prepare("
                INSERT INTO citas (
                    id_cliente, id_empleado, fecha, hora_inicio, hora_fin, total, estado, observaciones
                ) VALUES (
                    :id_cliente, :id_empleado, :fecha, :hora_inicio, :hora_fin, :total, 'Pendiente', :observaciones
                )
            ");
            $stmt->execute([
                'id_cliente'    => $data['id_cliente'],
                'id_empleado'   => $data['id_empleado'],
                'fecha'         => $data['fecha'],
                'hora_inicio'   => $data['hora_inicio'],
                'hora_fin'      => $horaFin,
                'total'         => $total,
                'observaciones' => $data['observaciones'] ?? null
            ]);

            $idCita = (int)$this->db->lastInsertId();

            // 4. Persistir el detalle del servicio contratado (alimenta Dashboard y Reportes)
            if ($idServicio !== null && $idServicio > 0) {
                $stmtDet = $this->db->prepare("
                    INSERT INTO cita_detalles (id_cita, id_servicio, precio_aplicado, duracion_aplicada)
                    VALUES (:id_cita, :id_servicio, :precio, :duracion)
                ");
                $stmtDet->execute([
                    'id_cita'     => $idCita,
                    'id_servicio' => $idServicio,
                    'precio'      => $total,
                    'duracion'    => (int)$servicio['duracion_minutos']
                ]);
            }

            $this->db->commit();
            return ['success' => true, 'mensaje' => 'Cita agendada correctamente.', 'id' => $idCita];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'mensaje' => 'Error al registrar la cita en la base de datos.', 'id' => null];
        }
    }

    /**
     * Actualizar el estado de una cita aplicando una máquina de transiciones permitidas.
     * Ej: Pendiente -> Confirmada/Cancelada ; Confirmada -> Completada/Cancelada ;
     *     Completada -> (terminal) ; Cancelada -> Pendiente (solo reapertura).
     * @param string|null $estadoActual Estado esperado actual (bloqueo optimista).
     */
    public function updateStatus(int $id, string $nuevoEstado, ?string $estadoActual = null): bool {
        $transicionesPermitidas = [
            'Pendiente' => ['Confirmada', 'Cancelada'],
            'Confirmada' => ['Completada', 'Cancelada', 'Pendiente'],
            'Completada' => [],
            'Cancelada' => ['Pendiente']
        ];

        if ($estadoActual !== null) {
            if (!in_array($nuevoEstado, $transicionesPermitidas[$estadoActual] ?? [], true)) {
                return false;
            }
        }

        $sql = "UPDATE citas SET estado = :estado WHERE id = :id";
        $params = ['estado' => $nuevoEstado, 'id' => $id];

        if ($estadoActual !== null) {
            $sql .= " AND estado = :estado_actual";
            $params['estado_actual'] = $estadoActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Monto ya cobrado (pagado) de una cita
     */
    public function totalPagado(int $idCita): float {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE id_cita = :id AND estado = 'Pagado'
        ");
        $stmt->execute(['id' => $idCita]);
        return (float)$stmt->fetchColumn();
    }
}
