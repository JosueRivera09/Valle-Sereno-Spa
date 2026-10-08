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
}
