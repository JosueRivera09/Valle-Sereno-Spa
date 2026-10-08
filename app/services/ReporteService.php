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

        // Cálculos contables consolidables
        $totalGeneral = 0.0;
        $totalEfectivo = 0.0;
        $totalTarjeta = 0.0;
        $totalTransferencia = 0.0;

        foreach ($data as $item) {
            $monto = (float)$item['monto'];
            $totalGeneral += $monto;
            if ($item['tipo_pago'] === 'Efectivo') $totalEfectivo += $monto;
            elseif ($item['tipo_pago'] === 'Tarjeta') $totalTarjeta += $monto;
            elseif ($item['tipo_pago'] === 'Transferencia') $totalTransferencia += $monto;
        }

        return [
            'success' => true,
            'data' => $data,
            'resumen_contable' => [
                'total_recaudado'    => $totalGeneral,
                'total_efectivo'     => $totalEfectivo,
                'total_tarjeta'      => $totalTarjeta,
                'total_transferencia'=> $totalTransferencia,
                'total_transacciones'=> count($data),
                'moneda'             => 'NIO (C$ Córdobas)',
                'ruc_empresa'        => 'J031000029384',
                'razon_social'       => 'Valle Sereno Spa S.A.'
            ]
        ];
    }
}
