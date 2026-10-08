<?php
/**
 * Modelo para Métricas y Consultas del Dashboard
 */

require_once __DIR__ . '/Database.php';

class Dashboard {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener estadísticas clave (KPIs)
     */
    public function getMetricas(): array {
        // 1. Citas de hoy y desglose de confirmadas
        $stmtCitas = $this->db->query("
            SELECT 
                COUNT(*) as total_hoy,
                SUM(CASE WHEN estado = 'Confirmada' THEN 1 ELSE 0 END) as confirmadas_hoy,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes_hoy,
                SUM(CASE WHEN estado = 'Completada' THEN 1 ELSE 0 END) as completadas_hoy
            FROM citas 
            WHERE fecha = CURRENT_DATE()
        ");
        $citasHoy = $stmtCitas->fetch() ?: ['total_hoy' => 0, 'confirmadas_hoy' => 0, 'pendientes_hoy' => 0, 'completadas_hoy' => 0];

        // 2. Terapeutas activos en plantilla
        $stmtTerapeutas = $this->db->query("
            SELECT COUNT(*) as total_terapeutas 
            FROM empleados 
            WHERE estado = 'activo' AND cargo LIKE '%Terapeuta%'
        ");
        $terapeutas = $stmtTerapeutas->fetch()['total_terapeutas'] ?? 0;

        // Si no hay especificados como 'Terapeuta' en cargo, contar empleados activos en general
        if ($terapeutas === 0) {
            $stmtEmp = $this->db->query("SELECT COUNT(*) as total FROM empleados WHERE estado = 'activo'");
            $terapeutas = $stmtEmp->fetch()['total'] ?? 0;
        }

        // 3. Clientes registrados activos
        $stmtClientes = $this->db->query("
            SELECT COUNT(*) as total_clientes 
            FROM clientes 
            WHERE estado = 'activo'
        ");
        $clientes = $stmtClientes->fetch()['total_clientes'] ?? 0;

        // 4. Ingresos del día actual (pagos con fecha de hoy)
        $stmtIngresos = $this->db->query("
            SELECT 
                COALESCE(SUM(monto), 0) as total_ingresos_hoy,
                COUNT(*) as total_transacciones_hoy
            FROM pagos 
            WHERE DATE(fecha_pago) = CURRENT_DATE() AND estado = 'Pagado'
        ");
        $ingresosHoy = $stmtIngresos->fetch() ?: ['total_ingresos_hoy' => 0, 'total_transacciones_hoy' => 0];

        // 5. Total de citas globales y servicios activos para métricas extra
        $stmtServicios = $this->db->query("SELECT COUNT(*) as total FROM servicios WHERE estado = 'activo'");
        $totalServicios = $stmtServicios->fetch()['total'] ?? 0;

        return [
            'citas_hoy' => (int)($citasHoy['total_hoy'] ?? 0),
            'citas_confirmadas' => (int)($citasHoy['confirmadas_hoy'] ?? 0),
            'citas_pendientes' => (int)($citasHoy['pendientes_hoy'] ?? 0),
            'citas_completadas' => (int)($citasHoy['completadas_hoy'] ?? 0),
            'terapeutas_activos' => (int)$terapeutas,
            'clientes_registrados' => (int)$clientes,
            'ingresos_hoy' => (float)($ingresosHoy['total_ingresos_hoy'] ?? 0),
            'transacciones_hoy' => (int)($ingresosHoy['total_transacciones_hoy'] ?? 0),
            'servicios_activos' => (int)$totalServicios
        ];
    }

    /**
     * Obtener citas programadas para hoy con datos de cliente, terapeuta y servicio principal
     */
    public function getCitasHoy(int $limit = 8): array {
        $stmt = $this->db->prepare("
            SELECT 
                c.id,
                c.fecha,
                c.hora_inicio,
                c.hora_fin,
                c.total,
                c.estado,
                c.observaciones,
                cl.nombre_completo AS cliente_nombre,
                cl.telefono AS cliente_telefono,
                e.nombre_completo AS terapeuta_nombre,
                COALESCE(
                    (
                        SELECT s.nombre 
                        FROM cita_detalles cd 
                        INNER JOIN servicios s ON cd.id_servicio = s.id 
                        WHERE cd.id_cita = c.id 
                        LIMIT 1
                    ), 
                    'Servicio Personalizado'
                ) AS servicio_nombre
            FROM citas c
            INNER JOIN clientes cl ON c.id_cliente = cl.id
            INNER JOIN empleados e ON c.id_empleado = e.id
            WHERE c.fecha = CURRENT_DATE()
            ORDER BY c.hora_inicio ASC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener los servicios más populares / solicitados
     */
    public function getServiciosPopulares(int $limit = 4): array {
        $stmt = $this->db->prepare("
            SELECT 
                s.id,
                s.nombre,
                s.costo,
                s.duracion_minutos,
                cat.nombre AS categoria_nombre,
                COUNT(cd.id) AS total_solicitudes
            FROM servicios s
            LEFT JOIN categorias cat ON s.id_categoria = cat.id
            LEFT JOIN cita_detalles cd ON s.id = cd.id_servicio
            WHERE s.estado = 'activo'
            GROUP BY s.id, s.nombre, s.costo, s.duracion_minutos, cat.nombre
            ORDER BY total_solicitudes DESC, s.costo DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Resumen de métodos de pago utilizados
     */
     public function getResumenPagos(): array {
        $stmt = $this->db->query("
            SELECT 
                tipo_pago,
                COUNT(*) as cantidad,
                COALESCE(SUM(monto), 0) as total
            FROM pagos
            WHERE estado = 'Pagado'
            GROUP BY tipo_pago
        ");
        return $stmt->fetchAll();
    }

    /**
     * Ingresos por día de los últimos 7 días para gráfico de tendencia
     */
    public function getIngresosUltimosDias(): array {
        $sql = "
            SELECT 
                DATE(fecha_pago) as fecha,
                COALESCE(SUM(monto), 0) as total
            FROM pagos
            WHERE estado = 'Pagado' AND fecha_pago >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY)
            GROUP BY DATE(fecha_pago)
        ";
        $stmt = $this->db->query($sql);
        $pagosPorFecha = [];
        foreach ($stmt->fetchAll() as $row) {
            $pagosPorFecha[$row['fecha']] = (float)$row['total'];
        }

        $resultado = [];
        for ($i = 6; $i >= 0; $i--) {
            $fechaStr = date('Y-m-d', strtotime("-$i days"));
            $resultado[] = [
                'fecha' => $fechaStr,
                'total' => $pagosPorFecha[$fechaStr] ?? 0.00
            ];
        }
        return $resultado;
    }

    /**
     * Citas según categoría de servicio para gráfico de dona/torta
     */
    public function getCitasPorCategoria(): array {
        $stmt = $this->db->query("
            SELECT 
                COALESCE(cat.nombre, 'Otras') as categoria,
                COUNT(cd.id) as total_citas
            FROM cita_detalles cd
            INNER JOIN servicios s ON cd.id_servicio = s.id
            LEFT JOIN categorias cat ON s.id_categoria = cat.id
            GROUP BY cat.nombre
            ORDER BY total_citas DESC
        ");
        return $stmt->fetchAll();
    }
}
