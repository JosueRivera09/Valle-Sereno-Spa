<?php
/**
 * Controlador de Clientes
 */
class ClientesController {
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
        $pageTitle = "Directorio de Clientes - Valle Sereno Spa";
        $activePage = 'clientes';
        $contentView = __DIR__ . '/../views/clientes/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
