<?php
/**
 * Controlador de Dashboard
 */

class DashboardController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Proteger ruta: Requiere sesión activa
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
    }

    public function index(): void {
        $usuario = [
            'nombre' => $_SESSION['nombre'] ?? 'Usuario',
            'email'  => $_SESSION['email'] ?? '',
            'rol'    => $_SESSION['rol_nombre'] ?? 'Sin rol'
        ];

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
