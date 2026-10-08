<?php
/**
 * Configuración Global del Sistema - Spa Valle Sereno
 */

// Configuración de visualización de errores (Modo desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Rutas base
define('APP_NAME', 'Valle Sereno Spa & Wellness');
define('BASE_URL', 'http://localhost/SpaValleSereno');

// Credenciales Base de Datos XAMPP
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'spa_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Helper global de autenticación y seguridad
require_once __DIR__ . '/../app/helpers/AuthHelper.php';
