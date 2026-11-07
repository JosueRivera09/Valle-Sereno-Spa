<?php
/**
 * Capa de Servicios: Lógica de autenticación y seguridad
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthService {
    // Parámetros de seguridad del acceso
    private const MAX_INTENTOS    = 3;   // intentos fallidos antes de bloquear la cuenta
    private const MINUTOS_BLOQUEO = 15;  // duración del bloqueo temporal (minutos)
    private const DIAS_RECORDAR   = 30;  // vigencia de "Recordar este dispositivo" (días)

    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Autenticar por correo institucional o usuario (alias) + contraseña.
     * - Mensaje genérico ante credenciales inválidas (evita enumeración de cuentas).
     * - Bloqueo temporal tras N intentos fallidos (se auto-libera al expirar).
     * @return array [ 'success' => bool, 'message' => string, 'user' => array|null, 'redirect' => string|null ]
     */
    public function login(string $loginIdentifier, string $password, bool $remember = false): array {
        $loginIdentifier = trim($loginIdentifier);
        // Nota: la contraseña NO se recorta: los espacios forman parte válida de una contraseña

        if ($loginIdentifier === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Por favor complete todos los campos obligatorios.'
            ];
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $this->usuarioModel->findByLogin($loginIdentifier);

        // Mensaje único para credenciales inválidas: no revelamos si la cuenta existe
        $credencialesInvalidas = [
            'success' => false,
            'message' => 'Correo o contraseña incorrectos.'
        ];

        if (!$user) {
            return $credencialesInvalidas;
        }

        // Auto-liberación del bloqueo temporal una vez transcurrido el tiempo
        if ($user['estado'] === 'bloqueado'
            && !empty($user['bloqueado_hasta'])
            && strtotime($user['bloqueado_hasta']) <= time()) {
            $this->usuarioModel->desbloquear((int)$user['id']);
            $user['estado'] = 'activo';
            $user['intentos_fallidos'] = 0;
            $user['bloqueado_hasta'] = null;
        }

        // 1. Cuenta bloqueada (por intentos fallidos o por decisión de Administración)
        if ($user['estado'] === 'bloqueado') {
            return [
                'success'   => false,
                'bloqueado' => true,
                'message'   => $this->mensajeBloqueo($user['bloqueado_hasta'])
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
            $intentos = $this->usuarioModel->registerFailedAttempt(
                (int)$user['id'],
                self::MAX_INTENTOS,
                self::MINUTOS_BLOQUEO
            );

            if ($intentos >= self::MAX_INTENTOS) {
                return [
                    'success'   => false,
                    'bloqueado' => true,
                    'message'   => 'Su cuenta ha sido bloqueada temporalmente por ' . self::MINUTOS_BLOQUEO
                                 . ' minutos tras ' . self::MAX_INTENTOS
                                 . ' intentos fallidos. Intente nuevamente más tarde o contacte a un Administrador.'
                ];
            }

            return $credencialesInvalidas;
        }

        // 4. Si las credenciales son válidas, restablecer intentos fallidos
        $this->usuarioModel->resetFailedAttempts((int)$user['id']);

        // Regenerar el ID de sesión tras autenticar (previene fijación de sesión)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        $_SESSION['usuario_id']     = $user['id'];
        $_SESSION['usuario']        = $user['usuario'];
        $_SESSION['nombre']         = $user['nombre_completo'];
        $_SESSION['email']          = $user['email'];
        $_SESSION['rol_id']         = $user['id_rol'];
        $_SESSION['rol_nombre']     = $user['rol_nombre'];
        $_SESSION['id_empleado']    = $user['id_empleado'] ?? null;
        $_SESSION['cargo']          = $user['cargo'] ?? 'Personal';
        $_SESSION['auth_time']      = time();
        $_SESSION['last_activity']  = time();

        // "Recordar este dispositivo": extiende la vida de la cookie de sesión.
        // Si no se marca, la cookie es de sesión (se borra al cerrar el navegador).
        if ($remember) {
            $_SESSION['remember'] = true;
            $cookieParams = session_get_cookie_params();
            setcookie(session_name(), session_id(), [
                'expires'  => time() + (60 * 60 * 24 * self::DIAS_RECORDAR),
                'path'     => $cookieParams['path'] !== '' ? $cookieParams['path'] : '/',
                'secure'   => !empty($cookieParams['secure']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        } else {
            unset($_SESSION['remember']);
        }

        $this->usuarioModel->updateLastAccess((int)$user['id']);

        // Determinar redirección
        $redirect = ($user['rol_nombre'] === 'Terapeuta') 
            ? 'index.php?c=citas&a=index' 
            : 'index.php?c=dashboard&a=index';

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

    /**
     * Mensaje claro según el tipo de bloqueo (temporal por intentos o manual del Administrador).
     */
    private function mensajeBloqueo(?string $bloqueadoHasta): string {
        if (empty($bloqueadoHasta)) {
            return 'Su cuenta está bloqueada. Contacte a un Administrador del sistema para reactivar su acceso.';
        }
        $segundosRestantes = strtotime($bloqueadoHasta) - time();
        $minutosRestantes  = max(1, (int)ceil($segundosRestantes / 60));
        return 'Su cuenta está bloqueada temporalmente por seguridad. Podrá intentarlo de nuevo en '
             . $minutosRestantes . ' minuto(s) o contacte a un Administrador.';
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
