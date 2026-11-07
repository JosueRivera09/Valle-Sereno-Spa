<?php
/**
 * Punto de Entrada Principal (Front Controller)
 * Spa Valle Sereno - Arquitectura MVC & N-Capas
 */

// Detección dinámica de HTTPS
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// Cabeceras HTTP de Seguridad Defensivas
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
if ($isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Iniciar sesión global con cookies endurecidas (httponly + samesite + secure dinámico)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Lista blanca de controladores permitidos (evita path traversal / LFI en el Front Controller)
$controladoresPermitidos = [
    'auth'      => 'AuthController',
    'dashboard' => 'DashboardController',
    'citas'     => 'CitasController',
    'clientes'  => 'ClientesController',
    'servicios' => 'ServiciosController',
    'empleados' => 'EmpleadosController',
    'pagos'     => 'PagosController',
    'reportes'  => 'ReportesController',
    'usuarios'  => 'UsuariosController',
    'respaldos' => 'RespaldosController'
];

// Obtener controlador y acción por URL (por defecto: auth / login)
$controllerKey = isset($_GET['c']) ? strtolower(preg_replace('/[^a-zA-Z]/', '', $_GET['c'])) : 'auth';
$actionName    = isset($_GET['a']) ? strtolower(preg_replace('/[^a-zA-Z]/', '', $_GET['a'])) : 'login';

$controllerName   = $controladoresPermitidos[$controllerKey] ?? null;
$controllerFile   = $controllerName ? __DIR__ . '/app/controllers/' . $controllerName . '.php' : null;

// Verificación de existencia del controlador y método público de acción
if ($controllerName !== null && file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        // Solo invocar métodos públicos reales (no __construct ni métodos internos)
        if (is_callable([$controller, $actionName])) {
            $reflection = new ReflectionMethod($controller, $actionName);
            if ($reflection->isPublic() && !$reflection->isConstructor()) {
                $controller->$actionName();
                exit;
            }
        }
    }
}

// 404 - Controlador o acción no encontrados
http_response_code(404);
echo "<h3>Error 404: La página o acción solicitada no existe.</h3>";
echo "<a href='index.php'>Volver al inicio</a>";
exit;
