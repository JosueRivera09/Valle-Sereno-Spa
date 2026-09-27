<?php
/**
 * Controlador de Caja & Pagos
 */
class PagosController {
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
        $pageTitle = "Caja & Registro de Pagos - Valle Sereno Spa";
        $activePage = 'pagos';
        $contentView = __DIR__ . '/../views/pagos/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
