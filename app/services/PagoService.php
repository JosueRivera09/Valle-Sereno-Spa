<?php
/**
 * Servicio de Lógica de Negocio para Caja y Pagos
 */
require_once __DIR__ . '/../models/PagoModel.php';

class PagoService {
    private PagoModel $model;

    public function __construct(?PagoModel $model = null) {
        $this->model = $model ?? new PagoModel();
    }

    public function listarPagos(): array {
        return $this->model->obtenerTodosPagos();
    }

    public function listarCitasPendientes(): array {
        return $this->model->obtenerCitasPendientesPago();
    }

    public function resumenCaja(): array {
        return $this->model->obtenerResumenCajaHoy();
    }

    public function obtenerDetallePagoConTicket(int $idPago): ?array {
        return $this->model->obtenerDetallePagoConTicket($idPago);
    }

    public function procesarPago(array $data): array {
        $val = $this->validarPago($data);
        if (!$val['valido']) {
            return ['success' => false, 'mensaje' => $val['mensaje']];
        }

        $res = $this->model->registrarPago($data);
        return $res ? ['success' => true, 'mensaje' => 'Pago procesado exitosamente. Recibo emitido.']
                    : ['success' => false, 'mensaje' => 'Error al registrar el cobro en caja.'];
    }

    public function validarPago(array $data): array {
        if (empty((int)($data['id_cita'] ?? 0))) {
            return ['valido' => false, 'mensaje' => 'Debes asociar el cobro a una cita válida.'];
        }
        if (!in_array($data['tipo_pago'] ?? '', ['Efectivo', 'Tarjeta', 'Transferencia'])) {
            return ['valido' => false, 'mensaje' => 'Tipo de pago no soportado. Selecciona Efectivo, Tarjeta o Transferencia.'];
        }
        if (empty($data['monto']) || (float)$data['monto'] <= 0) {
            return ['valido' => false, 'mensaje' => 'El monto a cobrar debe ser mayor a $0.00.'];
        }
        return ['valido' => true, 'mensaje' => 'OK'];
    }
}
