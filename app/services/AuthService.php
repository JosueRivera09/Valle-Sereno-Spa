<?php
/**
 * Capa de Servicios: Lógica de autenticación y seguridad
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthService {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Autenticar usuario por credenciales (Acepta usuario o correo)
     * @return array [ 'success' => bool, 'message' => string, 'user' => array|null, 'redirect' => string|null ]
     */
    public function login(string $loginIdentifier, string $password): array {
        $loginIdentifier = trim($loginIdentifier);
        $password = trim($password);

        if (empty($loginIdentifier) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Por favor complete todos los campos obligatorios.'
            ];
        }

        $user = $this->usuarioModel->findByLogin($loginIdentifier);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas. Verifique su usuario y contraseña.'
            ];
        }

        if ($user['estado'] !== 'activo') {
            return [
                'success' => false,
                'message' => 'Su cuenta se encuentra inactiva. Contacte a la administración de Valle Sereno.'
            ];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas. Verifique su usuario y contraseña.'
            ];
        }

        // Iniciar sesión y guardar datos del usuario
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_id']     = $user['id'];
        $_SESSION['usuario']        = $user['usuario'];
        $_SESSION['nombre']         = $user['nombre_completo'];
        $_SESSION['email']          = $user['email'];
        $_SESSION['rol_id']         = $user['id_rol'];
        $_SESSION['rol_nombre']     = $user['rol_nombre'];
        $_SESSION['cargo']          = $user['cargo'] ?? 'Personal';
        $_SESSION['auth_time']      = time();

        $this->usuarioModel->updateLastAccess((int)$user['id']);

        // Determinar redirección
        $redirect = 'index.php?c=dashboard&a=index';

        return [
            'success'  => true,
            'message'  => '¡Bienvenido(a) a Valle Sereno!',
            'user'     => [
                'nombre'  => $user['nombre_completo'],
                'usuario' => $user['usuario'],
                'rol'     => $user['rol_nombre']
            ],
            'redirect' => $redirect
        ];
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
