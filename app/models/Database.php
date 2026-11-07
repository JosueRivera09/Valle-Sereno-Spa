<?php
/**
 * Capa de Datos / Acceso a Base de Datos
 * Patrón Singleton con PDO
 */

require_once __DIR__ . '/../../config/config.php';

class Database {
    private static ?PDO $instance = null;

    private function __construct() {
        // Evitamos instanciación directa
    }

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                http_response_code(500);
                $msg = (defined('APP_ENV') && APP_ENV === 'development')
                    ? 'Error de conexión a la base de datos: ' . $e->getMessage()
                    : 'Error del servidor: No se pudo conectar a la base de datos. Contacte al administrador.';
                die(json_encode([
                    'success' => false,
                    'message' => $msg
                ]));
            }
        }
        return self::$instance;
    }
}
