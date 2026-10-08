<?php
/**
 * Controlador de Dashboard
 */

require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {
    public function __construct() {
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);
    }

    public function index(): void {
        $dashboardModel = new Dashboard();

        $metricas = $dashboardModel->getMetricas();
        $citasHoy = $dashboardModel->getCitasHoy(10);
        $serviciosPopulares = $dashboardModel->getServiciosPopulares(5);
        $resumenPagos = $dashboardModel->getResumenPagos();
        $ingresosGrafico = $dashboardModel->getIngresosUltimosDias();
        $citasCategorias = $dashboardModel->getCitasPorCategoria();

        $pageTitle = "Panel Principal - Valle Sereno Spa";
        $activePage = 'dashboard';
        $contentView = __DIR__ . '/../views/dashboard/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
