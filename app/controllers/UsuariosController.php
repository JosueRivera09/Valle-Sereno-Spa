<?php
/**
 * Controlador de Usuarios & Roles
 * Spa Valle Sereno - Arquitectura N-Capas
 */

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../services/UsuarioService.php';

class UsuariosController {
    private UsuarioService $usuarioService;

    public function __construct() {
        // Exclusivo para Administrador con verificación de sesión y tiempo de inactividad
        AuthHelper::requireRole(['Administrador']);
        $this->usuarioService = new UsuarioService();
    }

    /**
     * Vista principal del CRUD de usuarios
     */
    public function index(): void {
        $usuarios = $this->usuarioService->listar();
        $catalogos = $this->usuarioService->obtenerCatalogos();

        $pageTitle = "Gestión de Usuarios & Seguridad - Valle Sereno Spa";
        $activePage = 'usuarios';
        $csrfToken = AuthHelper::csrfToken();
        $contentView = __DIR__ . '/../views/usuarios/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    /**
     * Endpoint AJAX para obtener los datos de un usuario por ID (edición)
     */
    public function get(): void {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->usuarioService->obtenerPorId($id);

        header('Content-Type: application/json; charset=utf-8');
        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            exit;
        }

        echo json_encode(['success' => true, 'data' => $user]);
        exit;
    }

    /**
     * Guardar nuevo usuario
     */
    public function guardar(): void {
        AuthHelper::requireCsrf();

        $response = $this->usuarioService->crear($_POST);

        if (AuthHelper::isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        if ($response['success']) {
            $_SESSION['success_flash'] = $response['message'];
        } else {
            $_SESSION['error_flash'] = $response['message'];
        }
        header('Location: index.php?c=usuarios&a=index');
        exit;
    }

    /**
     * Actualizar usuario existente
     */
    public function actualizar(): void {
        AuthHelper::requireCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $response = $this->usuarioService->actualizar($id, $_POST);

        if (AuthHelper::isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        if ($response['success']) {
            $_SESSION['success_flash'] = $response['message'];
        } else {
            $_SESSION['error_flash'] = $response['message'];
        }
        header('Location: index.php?c=usuarios&a=index');
        exit;
    }

    /**
     * Cambiar / resetear contraseña
     */
    public function cambiarPassword(): void {
        AuthHelper::requireCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $password = $_POST['password'] ?? '';

        $response = $this->usuarioService->cambiarPassword($id, $password);

        if (AuthHelper::isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        if ($response['success']) {
            $_SESSION['success_flash'] = $response['message'];
        } else {
            $_SESSION['error_flash'] = $response['message'];
        }
        header('Location: index.php?c=usuarios&a=index');
        exit;
    }

    /**
     * Alternar estado (activo / inactivo)
     */
    public function toggleEstado(): void {
        AuthHelper::requireCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $response = $this->usuarioService->toggleEstado($id);

        if (AuthHelper::isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        if ($response['success']) {
            $_SESSION['success_flash'] = $response['message'];
        } else {
            $_SESSION['error_flash'] = $response['message'];
        }
        header('Location: index.php?c=usuarios&a=index');
        exit;
    }

    /**
     * Desbloquear una cuenta que excedió el límite de intentos
     */
    public function desbloquear(): void {
        AuthHelper::requireCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $response = $this->usuarioService->desbloquear($id);

        if (AuthHelper::isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
        }

        if ($response['success']) {
            $_SESSION['success_flash'] = $response['message'];
        } else {
            $_SESSION['error_flash'] = $response['message'];
        }
        header('Location: index.php?c=usuarios&a=index');
        exit;
    }
}
