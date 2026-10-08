<?php
/**
 * Controlador de Servicios & Terapias
 */
class ServiciosController {
    public function __construct() {
        // Accesible para todos los roles autenticados
        AuthHelper::requireAuth();
    }

    public function index(): void {
        $pageTitle = "Catálogo de Servicios - Valle Sereno Spa";
        $activePage = 'servicios';
        $contentView = __DIR__ . '/../views/servicios/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
