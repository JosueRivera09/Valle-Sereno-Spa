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
     * Límite de seguridad: 3 intentos fallidos consecutivos bloquean la cuenta.
     * @return array [ 'success' => bool, 'message' => string, 'user' => array|null, 'redirect' => string|null ]
     */
    public function login(string $loginIdentifier, string $password): array {
        $loginIdentifier = trim($loginIdentifier);
        $password = trim($password);
        $maxAttempts = 3;

        if (empty($loginIdentifier) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Por favor complete todos los campos obligatorios.'
            ];
        }

        // Iniciar sesión para gestionar control de intentos en memoria de sesión si no existe aún
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $this->usuarioModel->findByLogin($loginIdentifier);

        // Si el usuario o correo no existe en el sistema
        if (!$user) {
            $_SESSION['intentos_usuario_inexistente'] = ($_SESSION['intentos_usuario_inexistente'] ?? 0) + 1;
            $fallosInexistentes = $_SESSION['intentos_usuario_inexistente'];
            $restantesInexistentes = max(0, $maxAttempts - $fallosInexistentes);

            if ($restantesInexistentes <= 0) {
                return [
                    'success'    => false,
                    'bloqueado'  => true,
                    'inexistente'=> true,
                    'message'    => "El usuario o correo institucional ingresado no existe en nuestros registros. Ha alcanzado el límite de {$maxAttempts} intentos permitidos desde este dispositivo. Ingrese un usuario/correo corporativo válido o contacte a Administración."
                ];
            }

            return [
                'success'    => false,
                'inexistente'=> true,
                'message'    => "El usuario o correo institucional ingresado no existe. Ingrese un usuario o correo corporativo válido. (Le restan {$restantesInexistentes} intento(s))."
            ];
        }

        // Si el usuario existe pero antes hubo intentos erróneos de usuario inexistente, limpiar ese contador
        unset($_SESSION['intentos_usuario_inexistente']);

        // 1. Verificar si la cuenta ya está bloqueada por exceso de intentos
        if ($user['estado'] === 'bloqueado') {
            return [
                'success'   => false,
                'bloqueado' => true,
                'message'   => 'Su cuenta ha sido bloqueada por exceder el límite de intentos fallidos (' . $maxAttempts . '). Para restablecer el acceso, debe contactar obligatoriamente a un Administrador del Spa.'
            ];
        }

        // 2. Verificar si está inactiva por administración
        if ($user['estado'] !== 'activo') {
            return [
                'success' => false,
                'message' => 'Su cuenta se encuentra inactiva. Contacte a la administración de Valle Sereno Spa.'
            ];
        }

        // 3. Validar contraseña
        if (!password_verify($password, $user['password_hash'])) {
            $intentosActuales = $this->usuarioModel->registerFailedAttempt((int)$user['id'], $maxAttempts);
            $restantes = $maxAttempts - $intentosActuales;

            if ($restantes <= 0) {
                return [
                    'success'   => false,
                    'bloqueado' => true,
                    'message'   => '¡Cuenta bloqueada por seguridad! Ha superado el límite de ' . $maxAttempts . ' intentos fallidos. Deberá contactar a un Administrador del sistema para reactivar su cuenta.'
                ];
            }

            return [
                'success' => false,
                'message' => "Contraseña incorrecta. Le restan {$restantes} intento(s) antes de que su cuenta sea bloqueada."
            ];
        }

        // 4. Si las credenciales son válidas, restablecer intentos fallidos
        $this->usuarioModel->resetFailedAttempts((int)$user['id']);

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
