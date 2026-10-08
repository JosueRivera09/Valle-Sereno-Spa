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
        $sql = "SELECT c.id, c.fecha, c.hora_inicio, c.total, cl.nombre_completo as cliente_nombre 
                FROM citas c
                JOIN clientes cl ON c.id_cliente = cl.id
                LEFT JOIN pagos p ON c.id = p.id_cita
                WHERE (p.id IS NULL OR p.estado = 'Pendiente') AND c.estado != 'Cancelada'
                ORDER BY c.fecha DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function registrarPago(array $data): bool {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO pagos (id_cita, tipo_pago, monto, estado) 
                    VALUES (:id_cita, :tipo_pago, :monto, :estado)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_cita'   => $data['id_cita'],
                ':tipo_pago' => $data['tipo_pago'],
                ':monto'     => $data['monto'],
                ':estado'    => $data['estado'] ?? 'Pagado'
            ]);

            // Actualizar estado de cita a Completada si fue Pagado
            if (($data['estado'] ?? 'Pagado') === 'Pagado') {
                $stmtCita = $this->db->prepare("UPDATE citas SET estado = 'Completada' WHERE id = :id_cita");
                $stmtCita->execute([':id_cita' => $data['id_cita']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
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
