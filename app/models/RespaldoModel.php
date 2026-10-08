<?php
/**
 * Modelo de Respaldos y Mantenimiento del Sistema - Spa Valle Sereno
 */
require_once __DIR__ . '/../../config/config.php';

class RespaldoModel {
    private PDO $db;

    public function __construct(?PDO $pdo = null) {
        if ($pdo !== null) {
            $this->db = $pdo;
        } else {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $this->db = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
    }

    /**
     * Obtener estadísticas de salud y tamaño de cada tabla en la BD
     */
    public function obtenerEstadoTablas(): array {
        $stmt = $this->db->query("SHOW TABLE STATUS FROM `" . DB_NAME . "`");
        $tablas = $stmt->fetchAll();
        $resultado = [];

        foreach ($tablas as $t) {
            $tamanoDataKB = round(($t['Data_length'] + $t['Index_length']) / 1024, 2);
            $resultado[] = [
                'nombre'     => $t['Name'],
                'filas'      => (int)$t['Rows'],
                'motor'      => $t['Engine'],
                'cotejamiento' => $t['Collation'],
                'tamano_kb'  => $tamanoDataKB
            ];
        }

        return $resultado;
    }

    /**
     * Optimización de tablas de MySQL (OPTIMIZE TABLE)
     */
    public function optimizarTablas(): array {
        $tables = $this->db->query("SHOW TABLES FROM `" . DB_NAME . "`")->fetchAll(PDO::FETCH_COLUMN);
        $resultados = [];

        foreach ($tables as $tabla) {
            $res = $this->db->query("OPTIMIZE TABLE `{$tabla}`")->fetch();
            $resultados[] = [
                'tabla'   => $tabla,
                'mensaje' => $res['Msg_text'] ?? 'Optimizado'
            ];
        }

        return $resultados;
    }

    /**
     * Generar Volcado SQL (Dump completo de la base de datos)
     */
    public function generarDumpSQL(): string {
        $sql = "-- ============================================================================\n";
        $sql .= "-- VALLE SERENO SPA S.A. - RESPALDO OFICIAL DE BASE DE DATOS\n";
        $sql .= "-- Base de datos: " . DB_NAME . "\n";
        $sql .= "-- Fecha de generación: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- SERVIDOR: " . DB_HOST . ":" . DB_PORT . "\n";
        $sql .= "-- ============================================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        $tables = $this->db->query("SHOW TABLES FROM `" . DB_NAME . "`")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $sql .= "-- ----------------------------------------------------------------------------\n";
            $sql .= "-- Estructura de tabla: `{$table}`\n";
            $sql .= "-- ----------------------------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $createTableStmt = $this->db->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
            $sql .= $createTableStmt[1] . ";\n\n";

            $rows = $this->db->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $sql .= "-- Datos de tabla: `{$table}`\n";
                foreach ($rows as $row) {
                    $keys = array_map(fn($k) => "`$k`", array_keys($row));
                    $values = array_map(function($v) {
                        if ($v === null) return 'NULL';
                        return $this->db->quote($v);
                    }, array_values($row));

                    $sql .= "INSERT INTO `{$table}` (" . implode(", ", $keys) . ") VALUES (" . implode(", ", $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- ============================================================================\n";
        $sql .= "-- FIN DEL RESPALDO SQL - VALLE SERENO SPA\n";
        $sql .= "-- ============================================================================\n";

        return $sql;
    }

    /**
     * Restaurar la base de datos a partir de una cadena SQL
     */
    public function restaurarDumpSQL(string $sqlContent): bool {
        try {
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $this->db->exec($sqlContent);
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            return true;
        } catch (PDOException $e) {
            $this->db->exec("SET FOREIGN_KEY_CHECKS = 1;");
            throw $e;
        }
    }
}
