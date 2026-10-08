<?php

require_once __DIR__ . '/../models/Citas.php';
require_once __DIR__ . '/../models/CitaDetalle.php';
require_once __DIR__ . '/../services/CitaServicio.php';
class CitasController {
    private Citas $citaModel;
    private CitaDetalle $citadetalleModel;
    private CitaServicio $citaService; 

    public function __construct() {
        AuthHelper::requireRole(['Administrador', 'Recepcionista', 'Terapeuta']);
        $this->citaModel = new Citas();
        $this->citadetalleModel = new CitaDetalle();
        $this->citaService = new CitaServicio();
    }


    public function index(): void {
        require_once __DIR__ . '/../models/Cliente.php';
        require_once __DIR__ . '/../models/Empleado.php';
        require_once __DIR__ . '/../models/ServicioModel.php';

        $clienteModel = new Cliente();
        $empleadoModel = new Empleado();
        $servicioModel = new ServicioModel();

        $clientes = $clienteModel->obtenerTodos();
        $terapeutas = $empleadoModel->getAll('activo');
        $servicios = $servicioModel->obtenerServicios();
        $citas = $this->citaModel->getAll();

        $pageTitle = "Citas & Agenda - Valle Sereno Spa";
        $activePage = 'citas';
        $contentView = __DIR__ . '/../views/citas/index.php';
        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function consultarDisponibilidad(): void {
        header('Content-Type: application/json');
        $fecha      = $_GET['fecha'] ?? '';
        $horaInicio = $_GET['hora_inicio'] ?? '';
        $horaFin    = $_GET['hora_fin'] ?? '';

        if (empty($fecha) || empty($horaInicio) || empty($horaFin)) {
            echo json_encode(['success' => false, 'data' => []]);
            return;
        }

        $disponibles = $this->citaService->obtenerTerapeutasDisponibles($fecha, $horaInicio, $horaFin);
        echo json_encode(['success' => true, 'data' => $disponibles]);
    }

    public function crearClienteExpress(): void {
        header('Content-Type: application/json');
        $nombre   = $_POST['nombre_completo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $fechaNac = $_POST['fecha_nacimiento'] ?? '';
        $correo   = $_POST['correo'] ?? '';

        $res = $this->citaService->registrarClienteRapido($nombre, $telefono, $fechaNac, $correo);
        echo json_encode($res);
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idEmpleado  = (int)($_POST['id_empleado'] ?? 0);
            $fecha       = $_POST['fecha'] ?? '';
            $horaInicio  = $_POST['hora_inicio'] ?? '';
            $horaFin     = $_POST['hora_fin'] ?? '';

            // 1. Validar cruce de horarios (Checklist de GitHub)
            if ($this->citaService->verificarCruceHorario($idEmpleado, $fecha, $horaInicio, $horaFin)) {
                $_SESSION['mensaje'] = "Error: El terapeuta ya cuenta con una cita asignada en este horario (cruce detectado).";
                $_SESSION['mensaje_tipo'] = "danger";
                header('Location: index.php?c=citas&a=index');
                exit;
            }

            $data = [
                'id_cliente'    => (int)($_POST['id_cliente'] ?? 0),
                'id_empleado'   => $idEmpleado,
                'fecha'         => $fecha,
                'hora_inicio'   => $horaInicio,
                'hora_fin'      => $horaFin,
                'total'         => (float)($_POST['total'] ?? 0.00),
                'estado'        => 'pendiente',
                'observaciones' => trim($_POST['observaciones'] ?? '')
            ];

            $resultado = $this->citaModel->create($data);

            if ($resultado) {
                $_SESSION['mensaje'] = "Cita agendada correctamente sin cruces de horario.";
                $_SESSION['mensaje_tipo'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error al registrar la cita en la base de datos.";
                $_SESSION['mensaje_tipo'] = "danger";
            }

            header('Location: index.php?c=citas&a=index');
            exit;
        }
    }
}