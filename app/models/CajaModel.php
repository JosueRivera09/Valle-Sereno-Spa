<?php
/**
 * Modelo de Gestión de Caja, Arqueos y Cierres de Turno
 */
require_once __DIR__ . '/Database.php';

class CajaModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener la sesión de caja abierta actualmente (si existe)
     */
    public function obtenerCajaAbierta(): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, u.usuario, u.nombre_completo 
            FROM caja_sesiones c 
            JOIN usuarios u ON c.id_usuario = u.id 
            WHERE c.estado = 'abierta' 
            ORDER BY c.fecha_apertura DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Abrir turno / sesión de caja con fondo inicial
     */
    public function abrirCaja(int $idUsuario, float $montoInicial, ?string $observaciones = null): int {
        $stmt = $this->db->prepare("
            INSERT INTO caja_sesiones (id_usuario, fecha_apertura, monto_inicial, observaciones, estado)
            VALUES (:id_usuario, NOW(), :monto_inicial, :observaciones, 'abierta')
        ");
        $stmt->execute([
            'id_usuario'    => $idUsuario,
            'monto_inicial' => $montoInicial,
            'observaciones' => $observaciones
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Obtener el desglose de ingresos cobrados desde la fecha de apertura de la caja
     */
    public function obtenerIngresosDesdeApertura(string $fechaApertura): array {
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN tipo_pago = 'Efectivo' THEN monto ELSE 0 END), 0) AS total_efectivo,
                COALESCE(SUM(CASE WHEN tipo_pago = 'Tarjeta' THEN monto ELSE 0 END), 0) AS total_tarjeta,
                COALESCE(SUM(CASE WHEN tipo_pago = 'Transferencia' THEN monto ELSE 0 END), 0) AS total_transferencia,
                COUNT(*) AS total_cobros
            FROM pagos 
            WHERE estado = 'Pagado' AND fecha_pago >= :fecha_apertura
        ");
        $stmt->execute(['fecha_apertura' => $fechaApertura]);
        return $stmt->fetch();
    }

    /**
     * Cerrar y realizar el arqueo final de la sesión de caja
     */
    public function cerrarCaja(int $idCaja, float $montoFinalEfectivo, float $montoEsperadoEfectivo, float $diferencia, float $totalTarjeta, float $totalTransferencia, ?string $observaciones = null): bool {
        $stmt = $this->db->prepare("
            UPDATE caja_sesiones 
            SET fecha_cierre = NOW(),
                monto_final_efectivo = :final_efectivo,
                monto_esperado_efectivo = :esperado_efectivo,
                diferencia_efectivo = :diferencia,
                monto_tarjeta = :tarjeta,
                monto_transferencia = :transferencia,
                observaciones = COALESCE(CONCAT(observaciones, ' | ', :obs), :obs2),
                estado = 'cerrada'
            WHERE id = :id AND estado = 'abierta'
        ");
        return $stmt->execute([
            'id'                => $idCaja,
            'final_efectivo'    => $montoFinalEfectivo,
            'esperado_efectivo' => $montoEsperadoEfectivo,
            'diferencia'        => $diferencia,
            'tarjeta'           => $totalTarjeta,
            'transferencia'     => $totalTransferencia,
            'obs'               => $observaciones ?? '',
            'obs2'              => $observaciones ?? ''
        ]);
    }

    /**
     * Obtener historial de sesiones de caja finalizadas
     */
    public function obtenerHistorialCajas(int $limit = 15): array {
        $stmt = $this->db->prepare("
            SELECT c.*, u.usuario, u.nombre_completo 
            FROM caja_sesiones c 
            JOIN usuarios u ON c.id_usuario = u.id 
            ORDER BY c.fecha_apertura DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
