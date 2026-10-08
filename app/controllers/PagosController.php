<?php
/**
 * Controlador de Caja & Pagos
 */
require_once __DIR__ . '/../services/PagoService.php';
require_once __DIR__ . '/../services/CajaService.php';

class PagosController {
    private PagoService $service;
    private CajaService $cajaService;

    public function __construct() {
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);
        $this->service = new PagoService();
        $this->cajaService = new CajaService();
    }

    public function index(): void {
        $pagos = $this->service->listarPagos();
        $citasPendientes = $this->service->listarCitasPendientes();
        $resumenCaja = $this->service->resumenCaja();
        $estadoCaja = $this->cajaService->obtenerEstadoCaja();
        $historialCajas = $this->cajaService->obtenerHistorial();

        $pageTitle = "Caja & Registro de Pagos - Valle Sereno Spa";
        $activePage = 'pagos';
        $contentView = __DIR__ . '/../views/pagos/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function cobrar(): void {
        header('Content-Type: application/json');

        // Verificar si la caja está abierta antes de cobrar
        $estadoCaja = $this->cajaService->obtenerEstadoCaja();
        if (!$estadoCaja['abierta']) {
            echo json_encode(['success' => false, 'mensaje' => 'Debes realizar la APERTURA DE CAJA con monto inicial antes de registrar cobros.']);
            return;
        }

        $data = [
            'id_cita'   => (int)($_POST['id_cita'] ?? 0),
            'tipo_pago' => $_POST['tipo_pago'] ?? '',
            'monto'     => (float)($_POST['monto'] ?? 0.00),
            'estado'    => 'Pagado'
        ];

        $res = $this->service->procesarPago($data);
        echo json_encode($res);
    }

    public function abrirCaja(): void {
        header('Content-Type: application/json');
        $idUsuario = (int)($_SESSION['usuario_id'] ?? 0);
        $montoInicial = (float)($_POST['monto_inicial'] ?? 0.00);
        $observaciones = trim($_POST['observaciones'] ?? '');

        $res = $this->cajaService->abrirCaja($idUsuario, $montoInicial, $observaciones);
        echo json_encode($res);
    }

    public function cerrarCaja(): void {
        header('Content-Type: application/json');
        $montoFinalEfectivo = (float)($_POST['monto_final_efectivo'] ?? 0.00);
        $observaciones = trim($_POST['observaciones'] ?? '');

        $res = $this->cajaService->realizarArqueoYCierre($montoFinalEfectivo, $observaciones);
        echo json_encode($res);
    }

    public function ticket(): void {
        $idPago = (int)($_GET['id'] ?? 0);
        if ($idPago <= 0) {
            die("ID de pago no válido.");
        }

        $pagoDetails = $this->service->obtenerDetallePagoConTicket($idPago);
        if (!$pagoDetails) {
            die("El pago solicitado no existe.");
        }

        require_once __DIR__ . '/../views/pagos/ticket_template.php';
        exit;
    }
}
