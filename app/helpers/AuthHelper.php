<?php
/**
 * Helper de Autenticación, Autorización y Seguridad CSRF
 * Spa Valle Sereno - Arquitectura N-Capas
 */

class AuthHelper {
    private const SESSION_TIMEOUT = 1800; // 30 minutos de inactividad

    /**
     * Iniciar sesión segura si no está iniciada
     */
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verificar si el usuario está autenticado y comprobar tiempo de inactividad
     */
    public static function checkSession(): bool {
        self::initSession();

        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        // Control de expiración por inactividad (30 min)
        $now = time();
        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > self::SESSION_TIMEOUT) {
            self::destroySession();
            return false;
        }

        $_SESSION['last_activity'] = $now;
        return true;
    }

    /**
     * Forzar autenticación; redirigir al login si no tiene sesión válida
     */
    public static function requireAuth(): void {
        if (!self::checkSession()) {
            if (self::isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Sesión expirada. Por favor inicie sesión nuevamente.']);
                exit;
            }
            header('Location: index.php?c=auth&a=login&expired=1');
            exit;
        }
    }

    /**
     * Forzar que el usuario tenga uno de los roles permitidos
     * @param array<string> $rolesPermitidos Ej: ['Administrador', 'Recepcionista']
     */
    public static function requireRole(array $rolesPermitidos): void {
        self::requireAuth();

        $rolActual = $_SESSION['rol_nombre'] ?? '';
        if (!in_array($rolActual, $rolesPermitidos, true)) {
            if (self::isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Acceso denegado: No tiene permisos suficientes.']);
                exit;
            }
            $_SESSION['warning_flash'] = 'No tienes permiso para acceder a esa sección.';
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }
    }

    /**
     * Verificar si el rol actual coincide sin redirigir
     */
    public static function hasRole(string ...$roles): bool {
        self::initSession();
        $rolActual = $_SESSION['rol_nombre'] ?? '';
        return in_array($rolActual, $roles, true);
    }

    /**
     * Generar o recuperar token CSRF
     */
    public static function csrfToken(): string {
        self::initSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validar token CSRF recibido por POST o Header HTTP_X_CSRF_TOKEN
     */
    public static function validateCsrf(): bool {
        self::initSession();
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validar CSRF estricto: terminar con error si es inválido
     */
    public static function requireCsrf(): void {
        if (!self::validateCsrf()) {
            if (self::isAjax()) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido o expirado. Recargue la página.']);
                exit;
            }
            die("Error de seguridad: Solicitud CSRF inválida.");
        }
    }

    /**
     * Detectar si la petición es AJAX / Fetch
     */
    public static function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
            || isset($_POST['ajax']);
    }

    /**
     * Destruir sesión limpiamente
     */
    public static function destroySession(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }
}
