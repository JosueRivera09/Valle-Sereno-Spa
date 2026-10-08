<?php
/**
 * Servicio de Lógica de Negocio para Caja y Arqueo Diario
 */
require_once __DIR__ . '/../models/CajaModel.php';

class CajaService {
    private CajaModel $model;

    public function __construct(?CajaModel $model = null) {
        $this->model = $model ?? new CajaModel();
    }

    public function obtenerEstadoCaja(): array {
        $cajaAbierta = $this->model->obtenerCajaAbierta();

        if (!$cajaAbierta) {
            return [
                'abierta' => false,
                'caja'    => null,
                'ingresos'=> null
            ];
        }

        $ingresos = $this->model->obtenerIngresosDesdeApertura($cajaAbierta['fecha_apertura']);
        $montoInicial = (float)$cajaAbierta['monto_inicial'];
        $efectivoCobrado = (float)$ingresos['total_efectivo'];
        $montoEsperadoEfectivo = $montoInicial + $efectivoCobrado;

        return [
            'abierta'                 => true,
            'caja'                    => $cajaAbierta,
            'monto_inicial'           => $montoInicial,
            'efectivo_cobrado'        => $efectivoCobrado,
            'monto_esperado_efectivo' => $montoEsperadoEfectivo,
            'tarjeta_cobrada'         => (float)$ingresos['total_tarjeta'],
            'transferencia_cobrada'   => (float)$ingresos['total_transferencia'],
            'total_cobros'            => (int)$ingresos['total_cobros']
        ];
    }

    public function abrirCaja(int $idUsuario, float $montoInicial, ?string $observaciones = null): array {
        $existente = $this->model->obtenerCajaAbierta();
        if ($existente) {
            return ['success' => false, 'mensaje' => 'Ya existe una sesión de caja abierta en este momento.'];
        }

        if ($montoInicial < 0) {
            return ['success' => false, 'mensaje' => 'El monto inicial no puede ser negativo.'];
        }

        $idNew = $this->model->abrirCaja($idUsuario, $montoInicial, $observaciones);
        return ['success' => true, 'mensaje' => 'Sesión de caja abierta correctamente.', 'id' => $idNew];
    }

    public function realizarArqueoYCierre(float $montoFinalEfectivo, ?string $observaciones = null): array {
        $estadoCaja = $this->obtenerEstadoCaja();
        if (!$estadoCaja['abierta']) {
            return ['success' => false, 'mensaje' => 'No hay una sesión de caja abierta para realizar el cierre.'];
        }

        $caja = $estadoCaja['caja'];
        $montoEsperado = (float)$estadoCaja['monto_esperado_efectivo'];
        $diferencia = $montoFinalEfectivo - $montoEsperado;

        $res = $this->model->cerrarCaja(
            (int)$caja['id'],
            $montoFinalEfectivo,
            $montoEsperado,
            $diferencia,
            (float)$estadoCaja['tarjeta_cobrada'],
            (float)$estadoCaja['transferencia_cobrada'],
            $observaciones
        );

        if ($res) {
            $msgDiferencia = ($diferencia === 0.0) 
                ? 'Cuadre perfecto de efectivo.' 
                : ($diferencia > 0 ? "Sobrante de efectivo: C$" . number_format($diferencia, 2) : "Faltante de efectivo: C$" . number_format(abs($diferencia), 2));

            return [
                'success' => true,
                'mensaje' => "Caja cerrada exitosamente. {$msgDiferencia}",
                'diferencia' => $diferencia
            ];
        }

        return ['success' => false, 'mensaje' => 'Error al registrar el cierre de caja.'];
    }

    public function obtenerHistorial(): array {
        return $this->model->obtenerHistorialCajas();
    }
}
