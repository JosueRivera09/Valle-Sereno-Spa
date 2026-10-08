<?php
/**
 * Controlador de Clientes
 */
require_once __DIR__ . '/../services/ClienteService.php';

class ClientesController {
    private ClienteService $service;

    public function __construct() {
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);
        $this->service = new ClienteService();
    }

    public function index(): void {
        $clientes = $this->service->listarClientes();
        $pageTitle = "Directorio de Clientes - Valle Sereno Spa";
        $activePage = 'clientes';
        $contentView = __DIR__ . '/../views/clientes/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json');
        $data = [
            'nombre_completo'  => trim($_POST['nombre_completo'] ?? ''),
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
            'telefono'         => trim($_POST['telefono'] ?? ''),
            'correo'           => trim($_POST['correo'] ?? ''),
            'estado'           => $_POST['estado'] ?? 'activo'
        ];

        if (!empty($_POST['id'])) {
            $res = $this->service->actualizarCliente((int)$_POST['id'], $data);
        } else {
            $res = $this->service->crearCliente($data);
        }

        echo json_encode($res);
    }

    public function cambiarEstado(): void {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? 'inactivo';
        $res = $this->service->cambiarEstado($id, $estado);
        echo json_encode($res);
    }

    public function historial(): void {
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);
        $historial = $this->service->obtenerHistorial($id);
        echo json_encode(['success' => true, 'data' => $historial]);
    }
}
