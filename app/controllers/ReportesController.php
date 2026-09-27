<?php
/**
 * Controlador de Reportes & Métricas
 */
class ReportesController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Requiere sesión y rol Administrador
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        if (($_SESSION['rol_nombre'] ?? '') !== 'Administrador') {
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }
    }

    public function index(): void {
        $pageTitle = "Reportes & Analítica - Valle Sereno Spa";
        $activePage = 'reportes';
        $contentView = __DIR__ . '/../views/reportes/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
