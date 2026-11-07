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

        // Si el rol es 'Terapeuta', filtrar solo sus citas asignadas
        $rolActual = $_SESSION['rol_nombre'] ?? '';
        $idEmpleadoSesion = (int)($_SESSION['id_empleado'] ?? 0);

        if ($rolActual === 'Terapeuta' && $idEmpleadoSesion > 0) {
            $citas = $this->citaModel->getAll($idEmpleadoSesion);
        } else {
            $citas = $this->citaModel->getAll();
        }

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
        $idServicio = isset($_GET['id_servicio']) && is_numeric($_GET['id_servicio']) ? (int)$_GET['id_servicio'] : null;

        // Validar formatos antes de tocar la base de datos (evita excepciones PDO con cadenas basura)
        $validacion = $this->citaService->validarFechaHorario($fecha, $horaInicio, $horaFin);
        if (!$validacion['valido']) {
            echo json_encode(['success' => false, 'mensaje' => $validacion['mensaje'], 'data' => []]);
            return;
        }

        $disponibles = $this->citaService->obtenerTerapeutasDisponibles($fecha, $horaInicio, $horaFin, $idServicio);
        echo json_encode(['success' => true, 'data' => $disponibles]);
    }

    /**
     * Registro express de cliente nuevo: función de Recepción/Administración.
     * Un Terapeuta NO debe crear expedientes de clientes.
     */
    public function crearClienteExpress(): void {
        header('Content-Type: application/json');
        AuthHelper::requireCsrf();
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);

        $nombre   = $_POST['nombre_completo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $fechaNac = $_POST['fecha_nacimiento'] ?? '';
        $correo   = $_POST['correo'] ?? '';

        $res = $this->citaService->registrarClienteRapido($nombre, $telefono, $fechaNac, $correo);
        echo json_encode($res);
    }

    /**
     * Agendar una nueva cita: función exclusiva de Recepción/Administración.
     * El total y la duración se calculan en el servidor a partir del servicio seleccionado.
     */
    public function guardar(): void {
        AuthHelper::requireCsrf();
        AuthHelper::requireRole(['Administrador', 'Recepcionista']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=citas&a=index');
            exit;
        }

        $error = function(string $mensaje) {
            $_SESSION['mensaje'] = $mensaje;
            $_SESSION['mensaje_tipo'] = "danger";
            header('Location: index.php?c=citas&a=index');
            exit;
        };

        $idCliente   = (int)($_POST['id_cliente'] ?? 0);
        $idEmpleado  = (int)($_POST['id_empleado'] ?? 0);
        $idServicio  = isset($_POST['id_servicio']) && (int)$_POST['id_servicio'] > 0 ? (int)$_POST['id_servicio'] : null;
        $fecha       = $_POST['fecha'] ?? '';
        $horaInicio  = $_POST['hora_inicio'] ?? '';
        $horaFin     = $_POST['hora_fin'] ?? '';
        $totalManual = (float)($_POST['total'] ?? 0.00);
        $observaciones = trim($_POST['observaciones'] ?? '');

        // 1. Validar existencia y estado de cliente y terapeuta
        require_once __DIR__ . '/../models/Cliente.php';
        require_once __DIR__ . '/../models/Empleado.php';

        $cliente = (new Cliente())->obtenerPorId($idCliente);
        if (!$cliente || $cliente['estado'] !== 'activo') {
            $error('Debes seleccionar un cliente activo válido para agendar la cita.');
        }

        $empleado = (new Empleado())->findById($idEmpleado);
        if (!$empleado || $empleado['estado'] !== 'activo') {
            $error('Debes seleccionar un terapeuta activo válido.');
        }

        // 2. Validar formato y coherencia de fecha y horario (sin fechas pasadas)
        $validacion = $this->citaService->validarFechaHorario($fecha, $horaInicio, $horaFin);
        if (!$validacion['valido']) {
            $error($validacion['mensaje']);
        }

        if ($fecha === date('Y-m-d') && $horaInicio < date('H:i')) {
            $error('La hora de inicio ya pasó para la fecha de hoy. Elija un horario futuro.');
        }

        // 3. Sin servicio del catálogo se exige un monto manual mayor a cero
        if ($idServicio === null && $totalManual <= 0) {
            $error('Debes seleccionar un servicio del catálogo o indicar un monto total mayor a C$0.00.');
        }

        // 4. La cita debe caer dentro del turno registrado del terapeuta
        if (!$this->citaService->verificarDisponibilidadEmpleado($idEmpleado, $fecha, $horaInicio, $horaFin)) {
            $error('El terapeuta seleccionado no tiene turno registrado que cubra ese horario en la fecha elegida.');
        }

        // 5. Agendamiento atómico (bloqueo FOR UPDATE + cálculo de total/hora_fin en servidor)
        $resultado = $this->citaModel->agendarCita([
            'id_cliente'    => $idCliente,
            'id_empleado'   => $idEmpleado,
            'fecha'         => $fecha,
            'hora_inicio'   => $horaInicio,
            'hora_fin'      => $horaFin,
            'total'         => $totalManual,
            'observaciones' => $observaciones
        ], $idServicio);

        if ($resultado['success']) {
            $_SESSION['mensaje'] = "Cita #{$resultado['id']} agendada correctamente sin cruces de horario.";
            $_SESSION['mensaje_tipo'] = "success";
        } else {
            $error($resultado['mensaje']);
        }

        header('Location: index.php?c=citas&a=index');
        exit;
    }

    /**
     * Cambiar estado de una cita aplicando la máquina de transiciones permitidas.
     * Terapeuta: solo sus propias citas y solo transiciones hacia adelante
     *            (Confirmada / Completada, sin cancelar ni reabrir).
     */
    public function cambiarEstado(): void {
        header('Content-Type: application/json');
        AuthHelper::requireCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $nuevoEstado = trim($_POST['estado'] ?? '');

        if ($id <= 0 || empty($nuevoEstado)) {
            echo json_encode(['success' => false, 'mensaje' => 'Parámetros inválidos.']);
            return;
        }

        $cita = $this->citaModel->findById($id);
        if (!$cita) {
            echo json_encode(['success' => false, 'mensaje' => 'La cita no existe.']);
            return;
        }

        $rolActual = $_SESSION['rol_nombre'] ?? '';
        $idEmpleadoSesion = (int)($_SESSION['id_empleado'] ?? 0);

        if ($rolActual === 'Terapeuta') {
            if ((int)$cita['id_empleado'] !== $idEmpleadoSesion) {
                echo json_encode(['success' => false, 'mensaje' => 'No tienes permiso para modificar citas de otros terapeutas.']);
                return;
            }

            if (!in_array($nuevoEstado, ['Completada', 'Confirmada'], true)) {
                echo json_encode(['success' => false, 'mensaje' => 'Como terapeuta solo puedes marcar la cita como Confirmada o Completada. La cancelación debe gestionarse en Recepción.']);
                return;
            }
        } else {
            if (!in_array($nuevoEstado, ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'], true)) {
                echo json_encode(['success' => false, 'mensaje' => 'Estado no válido.']);
                return;
            }

            // No se puede cancelar ni reabrir una cita que ya fue cobrada
            if (in_array($nuevoEstado, ['Cancelada', 'Pendiente'], true) && $this->citaModel->totalPagado($id) > 0) {
                echo json_encode(['success' => false, 'mensaje' => 'Esta cita ya tiene un pago registrado; no puede cancelarse ni reabrirse.']);
                return;
            }
        }

        // Máquina de transiciones + bloqueo optimista contra el estado leído
        $res = $this->citaModel->updateStatus($id, $nuevoEstado, $cita['estado']);
        if ($res) {
            echo json_encode(['success' => true, 'mensaje' => "Estado de la cita actualizado a '{$nuevoEstado}'."]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => "Transición no permitida: la cita está en estado '{$cita['estado']}' y no puede pasar directamente a '{$nuevoEstado}'."]);
        }
    }
}
