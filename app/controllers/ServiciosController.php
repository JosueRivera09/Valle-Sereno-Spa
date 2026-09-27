<?php
/**
 * Controlador de Servicios & Terapias
 */
class ServiciosController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
    }

    public function index(): void {
        $pageTitle = "Catálogo de Servicios - Valle Sereno Spa";
        $activePage = 'servicios';
        $contentView = __DIR__ . '/../views/servicios/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
