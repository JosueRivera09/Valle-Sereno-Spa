<?php
/**
 * Controlador de Servicios & Terapias
 */
require_once __DIR__ . '/../services/ServicioService.php';

class ServiciosController {
    private ServicioService $service;

    public function __construct() {
        AuthHelper::requireRole(['Administrador', 'Recepcionista', 'Terapeuta']);
        $this->service = new ServicioService();
    }

    public function index(): void {
        $servicios = $this->service->listarServicios();
        $categorias = $this->service->listarCategorias();
        $pageTitle = "Catálogo de Servicios - Valle Sereno Spa";
        $activePage = 'servicios';
        $contentView = __DIR__ . '/../views/servicios/index.php';

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json');
        $data = [
            'id'               => $_POST['id'] ?? null,
            'id_categoria'     => (int)($_POST['id_categoria'] ?? 0),
            'nombre'           => trim($_POST['nombre'] ?? ''),
            'descripcion'      => trim($_POST['descripcion'] ?? ''),
            'costo'            => (float)($_POST['costo'] ?? 0.00),
            'duracion_minutos' => (int)($_POST['duracion_minutos'] ?? 0),
            'estado'           => $_POST['estado'] ?? 'activo'
        ];

        $res = $this->service->guardarServicio($data);
        echo json_encode($res);
    }

    public function guardarCategoria(): void {
        header('Content-Type: application/json');
        $data = [
            'nombre'      => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'estado'      => $_POST['estado'] ?? 'activo'
        ];
        $res = $this->service->crearCategoria($data);
        echo json_encode($res);
    }

    public function cambiarEstado(): void {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? 'inactivo';
        $res = $this->service->cambiarEstadoServicio($id, $estado);
        echo json_encode($res);
    }
}
