<?php
/**
 * Modelo de Reportes & Analítica Operativa - Spa Valle Sereno
 */
require_once __DIR__ . '/../../config/config.php';

class ReporteModel {
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

    public function obtenerVentasPorMes(): array {
        $sql = "SELECT DATE_FORMAT(fecha_pago, '%Y-%m') as mes, SUM(monto) as total_ventas, COUNT(*) as total_transacciones 
                FROM pagos 
                WHERE estado = 'Pagado'
                GROUP BY DATE_FORMAT(fecha_pago, '%Y-%m') 
                ORDER BY mes DESC 
                LIMIT 12";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerTopTerapeutas(): array {
        $sql = "SELECT e.nombre_completo as terapeuta, COUNT(c.id) as total_citas, SUM(c.total) as total_generado 
                FROM citas c 
                JOIN empleados e ON c.id_empleado = e.id 
                WHERE c.estado = 'Completada'
                GROUP BY e.id 
                ORDER BY total_citas DESC 
                LIMIT 5";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerFiltroRangoFechas(string $fechaInicio, string $fechaFin): array {
        $sql = "SELECT 
                    p.id AS pago_id, 
                    p.id_cita, 
                    p.fecha_pago, 
                    p.tipo_pago, 
                    p.monto, 
                    p.estado AS estado_pago, 
                    cl.nombre_completo AS cliente_nombre, 
                    COALESCE(cl.telefono, 'N/D') AS cliente_telefono, 
                    COALESCE(cl.correo, 'N/D') AS cliente_correo, 
                    COALESCE(e.nombre_completo, 'Sin asignación') AS terapeuta_nombre
                FROM pagos p 
                JOIN citas c ON p.id_cita = c.id 
                LEFT JOIN clientes cl ON c.id_cliente = cl.id 
                LEFT JOIN empleados e ON c.id_empleado = e.id
                WHERE DATE(p.fecha_pago) BETWEEN :inicio AND :fin AND p.estado = 'Pagado'
                ORDER BY p.fecha_pago DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':inicio' => $fechaInicio, ':fin' => $fechaFin]);
        return $stmt->fetchAll();
    }
}
