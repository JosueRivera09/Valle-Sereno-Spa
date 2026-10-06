<?php
/**
 * Capa de Negocio - Validaciones y lógica de empleados + disponibilidad
 */
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../models/DisponibilidadEmpleado.php';

class EmpleadoService {
    private Empleado $empleadoModel;
    private DisponibilidadEmpleado $dispModel;

    public function __construct() {
        $this->empleadoModel = new Empleado();
        $this->dispModel = new DisponibilidadEmpleado();
    }

    public function listarEmpleados(string $estado = null): array {
        return $this->empleadoModel->getAll($estado);
    }

    public function obtenerEmpleado(int $id): ?array {
        return $this->empleadoModel->findById($id);
    }

    public function crearEmpleado(array $data): array {
        $errores = $this->validarEmpleado($data);
        if (!empty($errores)) {
            return ['success' => false, 'message' => implode(' ', $errores)];
        }

        try {
            $id = $this->empleadoModel->create($data);
            return ['success' => true, 'message' => 'Empleado registrado correctamente.', 'id' => $id];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'El correo ya está registrado.'];
            }
            return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }

    public function actualizarEmpleado(int $id, array $data): array {
        $errores = $this->validarEmpleado($data);
        if (!empty($errores)) {
            return ['success' => false, 'message' => implode(' ', $errores)];
        }

        try {
            $ok = $this->empleadoModel->update($id, $data);
            return $ok
                ? ['success' => true, 'message' => 'Empleado actualizado correctamente.']
                : ['success' => false, 'message' => 'No se pudo actualizar el empleado.'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['success' => false, 'message' => 'El correo ya está en uso por otro empleado.'];
            }
            return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    public function obtenerDisponibilidad(int $idEmpleado): array {
        return $this->dispModel->getByEmpleado($idEmpleado);
    }

    /**
     * Valida y guarda un horario
     * - hora_fin > hora_inicio
     * - dia_semana entre 1 y 7
     */
    public function guardarDisponibilidad(array $data): array {
        $errores = $this->validarDisponibilidad($data);
        if (!empty($errores)) {
            return ['success' => false, 'message' => implode(' ', $errores)];
        }

        try {
            if (!empty($data['id'])) {
                $ok = $this->dispModel->update((int)$data['id'], $data);
                $msg = $ok ? 'Horario actualizado.' : 'No se pudo actualizar el horario.';
                return ['success' => $ok, 'message' => $msg];
            } else {
                $id = $this->dispModel->create($data);
                return ['success' => true, 'message' => 'Horario agregado.', 'id' => $id];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al guardar horario: ' . $e->getMessage()];
        }
    }

    public function eliminarDisponibilidad(int $id): array {
        $ok = $this->dispModel->delete($id);
        return $ok
            ? ['success' => true, 'message' => 'Horario eliminado.']
            : ['success' => false, 'message' => 'No se pudo eliminar el horario.'];
    }

    private function validarEmpleado(array $data): array {
        $errores = [];
        if (empty(trim($data['nombre_completo'] ?? ''))) {
            $errores[] = 'El nombre completo es obligatorio.';
        }
        if (empty(trim($data['telefono'] ?? ''))) {
            $errores[] = 'El teléfono es obligatorio.';
        }
        if (empty(trim($data['correo'] ?? '')) || !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo no es válido.';
        }
        if (empty(trim($data['cargo'] ?? ''))) {
            $errores[] = 'El cargo es obligatorio.';
        }
        return $errores;
    }

    private function validarDisponibilidad(array $data): array {
        $errores = [];

        if (empty($data['id_empleado']) || !is_numeric($data['id_empleado'])) {
            $errores[] = 'Empleado no válido.';
        }

        $dia = (int)($data['dia_semana'] ?? 0);
        if ($dia < 1 || $dia > 7) {
            $errores[] = 'El día de la semana debe estar entre 1 (Lunes) y 7 (Domingo).';
        }

        $horaInicio = $data['hora_inicio'] ?? '';
        $horaFin    = $data['hora_fin'] ?? '';

        if (empty($horaInicio) || empty($horaFin)) {
            $errores[] = 'Debe indicar hora de inicio y hora de fin.';
        } elseif ($horaFin <= $horaInicio) {
            $errores[] = 'La hora de fin debe ser posterior a la hora de inicio.';
        }

        return $errores;
    }
}