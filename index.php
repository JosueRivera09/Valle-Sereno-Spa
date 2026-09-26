<?php
/**
 * Punto de Entrada Principal (Front Controller)
 * Spa Valle Sereno - Arquitectura MVC & N-Capas
 */

// Iniciar sesión global
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Obtener controlador y acción por URL (por defecto: auth / login)
$controllerName = isset($_GET['c']) ? ucfirst(strtolower($_GET['c'])) . 'Controller' : 'AuthController';
$actionName = isset($_GET['a']) ? strtolower($_GET['a']) : 'login';

$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';

// Verificación de existencia del controlador y método
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
            exit;
        }
    }
}

// 404 - Controlador o acción no encontrados
http_response_code(404);
echo "<h3>Error 404: La página o acción solicitada no existe.</h3>";
echo "<a href='index.php'>Volver al inicio</a>";
