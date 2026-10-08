<?php
/**
 * Controlador de Caja & Pagos
 */
class PagosController {
    public function __construct() {
        // Exclusivo para Administrador y Recepcionista (Caja)
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);
    }

    public function index(): void {
        $pageTitle = "Caja & Registro de Pagos - Valle Sereno Spa";
        $activePage = 'pagos';
        $contentView = __DIR__ . '/../views/pagos/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
