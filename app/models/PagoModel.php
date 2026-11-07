<?php
/**
 * Modelo de Pagos y Arqueo de Caja - Spa Valle Sereno
 */
require_once __DIR__ . '/../../config/config.php';

class PagoModel {
    private PDO $db;

    public function __construct(?PDO $pdo = null) {
        if ($pdo !== null) {
            $this->db = $pdo;
        } else {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $this->db = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
    }

    public function obtenerTodosPagos(): array {
        $sql = "SELECT p.*, c.fecha as cita_fecha, cl.nombre_completo as cliente_nombre 
                FROM pagos p
                JOIN citas c ON p.id_cita = c.id
                JOIN clientes cl ON c.id_cliente = cl.id
                ORDER BY p.fecha_pago DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerCitasPendientesPago(): array {
        // Consolidado por cita: evita filas duplicadas y muestra el saldo realmente pendiente
        $sql = "SELECT c.id, c.fecha, c.hora_inicio, c.total, cl.nombre_completo as cliente_nombre,
                       GREATEST(c.total - COALESCE(SUM(CASE WHEN p.estado = 'Pagado' THEN p.monto END), 0), 0) AS saldo_pendiente
                FROM citas c
                JOIN clientes cl ON c.id_cliente = cl.id
                LEFT JOIN pagos p ON c.id = p.id_cita
                WHERE c.estado != 'Cancelada'
                GROUP BY c.id, c.fecha, c.hora_inicio, c.total, cl.nombre_completo
                HAVING saldo_pendiente > 0
                ORDER BY c.fecha DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Valida contra la base de datos que la cita sea cobrable:
     * existe, no está cancelada, no fue ya cobrada y el monto no excede el total.
     * @return array ['valido' => bool, 'mensaje' => string]
     */
    public function validarCobro(int $idCita, float $monto): array {
        $stmt = $this->db->prepare("
            SELECT c.total, c.estado,
                   COALESCE((SELECT SUM(monto) FROM pagos WHERE id_cita = c.id AND estado = 'Pagado'), 0) AS pagado
            FROM citas c 
            WHERE c.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $idCita]);
        $cita = $stmt->fetch();

        if (!$cita) {
            return ['valido' => false, 'mensaje' => 'La cita seleccionada no existe.'];
        }
        if ($cita['estado'] === 'Cancelada') {
            return ['valido' => false, 'mensaje' => 'La cita seleccionada está cancelada y no puede cobrarse.'];
        }

        $total   = (float)$cita['total'];
        $pagado  = (float)$cita['pagado'];
        $pendiente = round($total - $pagado, 2);

        if ($pendiente <= 0) {
            return ['valido' => false, 'mensaje' => 'Esta cita ya fue cobrada anteriormente.'];
        }
        if ($monto > $pendiente + 0.01) {
            return ['valido' => false, 'mensaje' => 'El monto excede el saldo pendiente de la cita (saldo: C$' . number_format($pendiente, 2) . ').'];
        }

        return ['valido' => true, 'mensaje' => 'OK'];
    }

    /**
     * Registra un pago de forma atómica:
     * bloquea la cita (FOR UPDATE), revalida cobro doble/exceso y solo completa
     * la cita si estaba Pendiente o Confirmada (nunca revive citas Canceladas).
     */
    public function registrarPago(array $data): bool {
        $this->db->beginTransaction();
        try {
            // Bloqueo de la cita para serializar cobros concurrentes sobre la misma cita
            $stmtCita = $this->db->prepare("
                SELECT total, estado FROM citas WHERE id = :id FOR UPDATE
            ");
            $stmtCita->execute([':id' => $data['id_cita']]);
            $cita = $stmtCita->fetch();

            if (!$cita || $cita['estado'] === 'Cancelada') {
                $this->db->rollBack();
                return false;
            }

            $stmtPagado = $this->db->prepare("
                SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE id_cita = :id AND estado = 'Pagado'
            ");
            $stmtPagado->execute([':id' => $data['id_cita']]);
            $pagado = (float)$stmtPagado->fetchColumn();

            if ($pagado + (float)$data['monto'] > (float)$cita['total'] + 0.01) {
                $this->db->rollBack();
                return false;
            }

            $sql = "INSERT INTO pagos (id_cita, tipo_pago, monto, estado) 
                    VALUES (:id_cita, :tipo_pago, :monto, :estado)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_cita'   => $data['id_cita'],
                ':tipo_pago' => $data['tipo_pago'],
                ':monto'     => $data['monto'],
                ':estado'    => $data['estado'] ?? 'Pagado'
            ]);

            // Actualizar estado de cita a Completada solo si estaba Pendiente o Confirmada
            if (($data['estado'] ?? 'Pagado') === 'Pagado') {
                $stmtCitaUpd = $this->db->prepare("
                    UPDATE citas SET estado = 'Completada' 
                    WHERE id = :id_cita AND estado IN ('Pendiente', 'Confirmada')
                ");
                $stmtCitaUpd->execute([':id_cita' => $data['id_cita']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('[PagoModel::registrarPago] ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerDetallePagoConTicket(int $idPago): ?array {
        $sql = "SELECT 
                    p.id AS pago_id,
                    p.fecha_pago,
                    p.tipo_pago,
                    p.monto,
                    p.estado AS estado_pago,
                    c.id AS cita_id,
                    c.fecha AS cita_fecha,
                    c.hora_inicio,
                    c.hora_fin,
                    c.observaciones,
                    cl.nombre_completo AS cliente_nombre,
                    COALESCE(cl.telefono, 'N/D') AS cliente_telefono,
                    COALESCE(cl.correo, 'N/D') AS cliente_correo,
                    COALESCE(e.nombre_completo, 'Sin asignación') AS terapeuta_nombre
                FROM pagos p
                JOIN citas c ON p.id_cita = c.id
                JOIN clientes cl ON c.id_cliente = cl.id
                LEFT JOIN empleados e ON c.id_empleado = e.id
                WHERE p.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idPago]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function obtenerResumenCajaHoy(): array {
        $sql = "SELECT tipo_pago, COUNT(*) as cantidad, SUM(monto) as total 
                FROM pagos 
                WHERE DATE(fecha_pago) = CURDATE() AND estado = 'Pagado'
                GROUP BY tipo_pago";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
