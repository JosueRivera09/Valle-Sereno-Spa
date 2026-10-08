<?php
/**
 * Controlador de Caja & Pagos
 */
require_once __DIR__ . '/../services/PagoService.php';

class PagosController {
    private PagoService $service;

    public function __construct() {
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);
        $this->service = new PagoService();
    }

    public function index(): void {
        $pagos = $this->service->listarPagos();
        $citasPendientes = $this->service->listarCitasPendientes();
        $resumenCaja = $this->service->resumenCaja();

        $pageTitle = "Caja & Registro de Pagos - Valle Sereno Spa";
        $activePage = 'pagos';
        $contentView = __DIR__ . '/../views/pagos/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function cobrar(): void {
        header('Content-Type: application/json');
        $data = [
            'id_cita'   => (int)($_POST['id_cita'] ?? 0),
            'tipo_pago' => $_POST['tipo_pago'] ?? '',
            'monto'     => (float)($_POST['monto'] ?? 0.00),
            'estado'    => 'Pagado'
        ];

        $res = $this->service->procesarPago($data);
        echo json_encode($res);
    }
}
