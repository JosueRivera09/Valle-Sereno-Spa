<?php
/**
 * Controlador de Autenticación
 */

require_once __DIR__ . '/../services/AuthService.php';

class AuthController {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * Muestra la vista del Login
     */
    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si ya está autenticado, enviarlo directo a su módulo inicial según rol
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . AuthHelper::homeUrl());
            exit;
        }

        // Vitrina del login: servicios más vendidos (con degradación segura si falla la BD)
        $serviciosDestacados = [];
        try {
            require_once __DIR__ . '/../models/Database.php';
            require_once __DIR__ . '/../models/ServicioModel.php';
            $servicioModel = new ServicioModel(Database::getConnection());
            $serviciosDestacados = $servicioModel->obtenerMasVendidos(4);
        } catch (Throwable $e) {
            $serviciosDestacados = [];
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesa la petición de inicio de sesión (Soporta AJAX / JSON y Form estándar)
     */
    public function authenticate(): void {
        AuthHelper::requireCsrf();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $response = $this->authService->login($email, $password, !empty($_POST['rememberMe']));

        // Si la petición viene por Fetch/AJAX (solicitud JSON)
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                  || isset($_POST['ajax']);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        if ($response['success']) {
            header('Location: ' . $response['redirect']);
            exit;
        } else {
            $_SESSION['error_flash'] = $response['message'];
            header('Location: index.php?c=auth&a=login');
            exit;
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(): void {
        $this->authService->logout();
        header('Location: index.php?c=auth&a=login&logout=success');
        exit;
    }
}
