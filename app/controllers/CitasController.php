<?php
/**
 * Controlador de Citas & Agenda
 */
class CitasController {
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
        $pageTitle = "Citas & Agenda - Valle Sereno Spa";
        $activePage = 'citas';
        $contentView = __DIR__ . '/../views/citas/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
