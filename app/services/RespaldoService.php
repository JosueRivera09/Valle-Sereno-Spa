<?php
/**
 * Servicio de Lógica de Negocio para Respaldos & Mantenimiento
 */
require_once __DIR__ . '/../models/RespaldoModel.php';

class RespaldoService {
    private RespaldoModel $model;
    private string $backupDir;

    public function __construct(?RespaldoModel $model = null) {
        $this->model = $model ?? new RespaldoModel();
        $this->backupDir = __DIR__ . '/../../storage/backups';

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    public function obtenerEstadoTablas(): array {
        return $this->model->obtenerEstadoTablas();
    }

    public function optimizarBaseDatos(): array {
        return $this->model->optimizarTablas();
    }

    public function generarYGuardarRespaldo(): array {
        $dumpSql = $this->model->generarDumpSQL();
        $filename = "backup_spa_db_" . date('Y-m-d_H-i-s') . ".sql";
        $filepath = $this->backupDir . '/' . $filename;

        if (file_put_contents($filepath, $dumpSql)) {
            return [
                'success' => true,
                'mensaje' => "Respaldo generado exitosamente: {$filename}",
                'filename' => $filename,
                'tamano_mb' => round(filesize($filepath) / (1024 * 1024), 2)
            ];
        }

        return ['success' => false, 'mensaje' => 'Error al escribir el archivo de respaldo en el servidor.'];
    }

    public function descargarRespaldoDirecto(): void {
        $dumpSql = $this->model->generarDumpSQL();
        $filename = "backup_spa_db_" . date('Y-m-d_H-i-s') . ".sql";

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($dumpSql));
        echo $dumpSql;
        exit;
    }

    public function obtenerListaRespaldos(): array {
        $archivos = [];
        if (is_dir($this->backupDir)) {
            $files = glob($this->backupDir . '/*.sql');
            foreach ($files as $file) {
                $archivos[] = [
                    'nombre'    => basename($file),
                    'tamano_kb' => round(filesize($file) / 1024, 2),
                    'fecha'     => date('Y-m-d H:i:s', filemtime($file))
                ];
            }
            usort($archivos, fn($a, $b) => strcmp($b['fecha'], $a['fecha']));
        }
        return $archivos;
    }

    public function restaurarDesdeArchivo(array $fileInfo): array {
        if (empty($fileInfo['tmp_name']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'mensaje' => 'Error en la subida del archivo SQL.'];
        }

        $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'sql') {
            return ['success' => false, 'mensaje' => 'Formato no permitido. Solo se permiten archivos .sql'];
        }

        $sqlContent = file_get_contents($fileInfo['tmp_name']);
        if (empty($sqlContent)) {
            return ['success' => false, 'mensaje' => 'El archivo SQL está vacío.'];
        }

        try {
            $this->model->restaurarDumpSQL($sqlContent);
            return ['success' => true, 'mensaje' => 'La base de datos ha sido restaurada con éxito.'];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error al procesar la restauración: ' . $e->getMessage()];
        }
    }

    public function eliminarRespaldo(string $filename): array {
        $filename = basename($filename);
        $filepath = $this->backupDir . '/' . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
            return ['success' => true, 'mensaje' => 'Archivo de respaldo eliminado.'];
        }

        return ['success' => false, 'mensaje' => 'El archivo de respaldo no existe.'];
    }
}
