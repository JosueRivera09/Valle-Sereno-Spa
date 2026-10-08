<?php
/**
 * Controlador de Reportes & Métricas
 */
class ReportesController {
    public function __construct() {
        // Exclusivo para Administrador
        AuthHelper::requireRole(['Administrador']);
    }

    public function index(): void {
        $pageTitle = "Reportes & Analítica - Valle Sereno Spa";
        $activePage = 'reportes';
        $contentView = __DIR__ . '/../views/reportes/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
