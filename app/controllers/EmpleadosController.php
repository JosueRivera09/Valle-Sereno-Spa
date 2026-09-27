<?php
/**
 * Controlador de Personal / Empleados
 */
class EmpleadosController {
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
        $pageTitle = "Personal Terapéutico - Valle Sereno Spa";
        $activePage = 'empleados';
        $contentView = __DIR__ . '/../views/empleados/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
