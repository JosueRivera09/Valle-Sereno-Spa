<?php
/**
 * Controlador de Personal / Empleados
 */
require_once __DIR__ . '/../services/EmpleadoService.php';

class EmpleadosController {
    private EmpleadoService $service;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?c=auth&a=login');
            exit;
        }
        $this->service = new EmpleadoService();
    }

    public function index(): void {
        $empleados = $this->service->listarEmpleados();
        $pageTitle = "Personal Terapéutico - Valle Sereno Spa";
        $activePage = 'empleados';
        $contentView = __DIR__ . '/../views/empleados/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function guardar(): void {
        header('Content-Type: application/json');
        $data = [
            'nombre_completo' => $_POST['nombre_completo'] ?? '',
            'telefono'        => $_POST['telefono'] ?? '',
            'correo'          => $_POST['correo'] ?? '',
            'cargo'           => $_POST['cargo'] ?? '',
            'especialidades'  => $_POST['especialidades'] ?? null,
            'estado'          => $_POST['estado'] ?? 'activo'
        ];

        if (!empty($_POST['id'])) {
            $result = $this->service->actualizarEmpleado((int)$_POST['id'], $data);
        } else {
            $result = $this->service->crearEmpleado($data);
        }
        echo json_encode($result);
    }

    public function disponibilidad(): void {
        header('Content-Type: application/json');
        $idEmpleado = (int)($_GET['id_empleado'] ?? 0);
        $horarios = $this->service->obtenerDisponibilidad($idEmpleado);
        echo json_encode(['success' => true, 'data' => $horarios]);
    }

    public function guardarHorario(): void {
        header('Content-Type: application/json');
        $data = [
            'id'          => $_POST['id'] ?? null,
            'id_empleado' => $_POST['id_empleado'] ?? 0,
            'dia_semana'  => $_POST['dia_semana'] ?? 0,
            'hora_inicio' => $_POST['hora_inicio'] ?? '',
            'hora_fin'    => $_POST['hora_fin'] ?? '',
            'estado'      => $_POST['estado'] ?? 'activo'
        ];
        $result = $this->service->guardarDisponibilidad($data);
        echo json_encode($result);
    }

    public function eliminarHorario(): void {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->service->eliminarDisponibilidad($id);
        echo json_encode($result);
    }
}