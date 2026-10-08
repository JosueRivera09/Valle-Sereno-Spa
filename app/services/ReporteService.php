<?php
/**
 * Servicio de Lógica de Negocio para Reportes & Analítica
 */
require_once __DIR__ . '/../models/ReporteModel.php';

class ReporteService {
    private ReporteModel $model;

    public function __construct(?ReporteModel $model = null) {
        $this->model = $model ?? new ReporteModel();
    }

    public function obtenerVentasMensuales(): array {
        return $this->model->obtenerVentasPorMes();
    }

    public function obtenerRankingTerapeutas(): array {
        return $this->model->obtenerTopTerapeutas();
    }

    public function generarReporteRango(string $inicio, string $fin): array {
        if (empty($inicio) || empty($fin)) {
            return ['success' => false, 'mensaje' => 'Debes proporcionar una fecha de inicio y de fin.'];
        }
        if ($inicio > $fin) {
            return ['success' => false, 'mensaje' => 'La fecha de inicio no puede ser posterior a la fecha fin.'];
        }

        $data = $this->model->obtenerFiltroRangoFechas($inicio, $fin);
        return ['success' => true, 'data' => $data];
    }
}
