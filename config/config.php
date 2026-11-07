<?php
/**
 * Configuración Global del Sistema - Spa Valle Sereno
 */

// Entorno de ejecución: 'development' o 'production'
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Configuración de errores según el entorno
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Zona horaria coherente con los NOW()/CURDATE() de MySQL (Nicaragua)
date_default_timezone_set('America/Managua');

// Rutas base
define('APP_NAME', 'Valle Sereno Spa & Wellness');
define('BASE_URL', 'http://localhost/Valle-Sereno-Spa');
define('CURRENCY_SYMBOL', 'C$');

// Credenciales Base de Datos XAMPP
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'spa_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Helper global de autenticación y seguridad
require_once __DIR__ . '/../app/helpers/AuthHelper.php';
