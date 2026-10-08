<?php
/**
 * Controlador de Reportes & Métricas
 */
require_once __DIR__ . '/../services/ReporteService.php';

class ReportesController {
    private ReporteService $service;

    public function __construct() {
        AuthHelper::requireRole(['Administrador']);
        $this->service = new ReporteService();
    }

    public function index(): void {
        $ventasMes = $this->service->obtenerVentasMensuales();
        $rankingTerapeutas = $this->service->obtenerRankingTerapeutas();

        $pageTitle = "Reportes & Analítica - Valle Sereno Spa";
        $activePage = 'reportes';
        $contentView = __DIR__ . '/../views/reportes/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function filtrar(): void {
        header('Content-Type: application/json');
        $inicio = $_GET['inicio'] ?? '';
        $fin = $_GET['fin'] ?? '';

        $res = $this->service->generarReporteRango($inicio, $fin);
        echo json_encode($res);
    }

    public function exportarExcel(): void {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fin = $_GET['fin'] ?? date('Y-m-d');

        $reporte = $this->service->generarReporteRango($inicio, $fin);
        $data = $reporte['data'] ?? [];
        $resumen = $reporte['resumen_contable'] ?? [];

        $filename = "Reporte_Contable_ValleSereno_{$inicio}_al_{$fin}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Encabezado Fiscal / Contable
        fputcsv($output, ['VALLE SERENO SPA S.A. - REPORTE CONTABLE Y TRIBUTARIO']);
        fputcsv($output, ['RUC:', $resumen['ruc_empresa'] ?? 'J031000029384']);
        fputcsv($output, ['PERIODO:', "Del {$inicio} al {$fin}"]);
        fputcsv($output, ['MONEDA:', 'Cordobas Nicaraguenses (C$)']);
        fputcsv($output, ['FECHA EMISION:', date('d/m/Y H:i:s')]);
        fputcsv($output, []);

        // Columnas de Datos
        fputcsv($output, [
            'ID Pago', 
            'ID Cita', 
            'Fecha y Hora Pago', 
            'Cliente / Paciente', 
            'Telefono', 
            'Correo', 
            'Terapeuta Asignado', 
            'Metodo de Pago', 
            'Estado Fiscal', 
            'Monto (C$)'
        ]);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['pago_id'],
                $row['id_cita'],
                $row['fecha_pago'],
                $row['cliente_nombre'],
                $row['cliente_telefono'],
                $row['cliente_correo'],
                $row['terapeuta_nombre'],
                $row['tipo_pago'],
                $row['estado_pago'],
                number_format((float)$row['monto'], 2, '.', '')
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, ['RESUMEN CONTABLE CONSOLIDADO']);
        fputcsv($output, ['Total Transacciones:', $resumen['total_transacciones'] ?? 0]);
        fputcsv($output, ['Total Efectivo (C$):', number_format($resumen['total_efectivo'] ?? 0, 2, '.', '')]);
        fputcsv($output, ['Total Tarjeta (C$):', number_format($resumen['total_tarjeta'] ?? 0, 2, '.', '')]);
        fputcsv($output, ['Total Transferencia (C$):', number_format($resumen['total_transferencia'] ?? 0, 2, '.', '')]);
        fputcsv($output, ['TOTAL RECAUDADO (C$):', number_format($resumen['total_recaudado'] ?? 0, 2, '.', '')]);

        fclose($output);
        exit;
    }

    public function exportarPdf(): void {
        $inicio = $_GET['inicio'] ?? date('Y-m-01');
        $fin = $_GET['fin'] ?? date('Y-m-d');

        $reporte = $this->service->generarReporteRango($inicio, $fin);
        $data = $reporte['data'] ?? [];
        $resumen = $reporte['resumen_contable'] ?? [];

        require_once __DIR__ . '/../views/reportes/pdf_template.php';
        exit;
    }
}
