<?php
/**
 * Servicio de Gestión de Clientes
 */
require_once __DIR__ . '/../models/Cliente.php';

class ClienteService {
    private Cliente $model;

    public function __construct(?Cliente $model = null) {
        $this->model = $model ?? new Cliente();
    }

    public function listarClientes(): array {
        return $this->model->obtenerTodos();
    }

    public function obtenerCliente(int $id): ?array {
        return $this->model->obtenerPorId($id);
    }

    public function crearCliente(array $data): array {
        $val = $this->validarDatos($data);
        if (!$val['valido']) {
            return ['success' => false, 'mensaje' => $val['mensaje']];
        }

        $res = $this->model->crear($data);
        return $res ? ['success' => true, 'mensaje' => 'Cliente registrado correctamente.']
                    : ['success' => false, 'mensaje' => 'No se pudo guardar el cliente en la base de datos.'];
    }

    public function actualizarCliente(int $id, array $data): array {
        if ($id <= 0) {
            return ['success' => false, 'mensaje' => 'ID de cliente inválido.'];
        }

        $val = $this->validarDatos($data);
        if (!$val['valido']) {
            return ['success' => false, 'mensaje' => $val['mensaje']];
        }

        $res = $this->model->actualizar($id, $data);
        return $res ? ['success' => true, 'mensaje' => 'Expediente de cliente actualizado.']
                    : ['success' => false, 'mensaje' => 'No se pudo actualizar el registro del cliente.'];
    }

    public function cambiarEstado(int $id, string $estado): array {
        if (!in_array($estado, ['activo', 'inactivo'])) {
            return ['success' => false, 'mensaje' => 'Estado no permitido.'];
        }
        $res = $this->model->cambiarEstado($id, $estado);
        return $res ? ['success' => true, 'mensaje' => 'Estado de cliente modificado.']
                    : ['success' => false, 'mensaje' => 'Error al cambiar estado del cliente.'];
    }

    public function obtenerHistorial(int $idCliente): array {
        return $this->model->obtenerHistorialCitas($idCliente);
    }

    public function validarDatos(array $data): array {
        if (empty(trim($data['nombre_completo'] ?? ''))) {
            return ['valido' => false, 'mensaje' => 'El nombre completo del cliente es obligatorio.'];
        }
        if (empty($data['fecha_nacimiento'] ?? '')) {
            return ['valido' => false, 'mensaje' => 'La fecha de nacimiento es obligatoria.'];
        }
        if (empty(trim($data['telefono'] ?? ''))) {
            return ['valido' => false, 'mensaje' => 'El teléfono de contacto es obligatorio.'];
        }
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            return ['valido' => false, 'mensaje' => 'El correo electrónico no tiene un formato válido.'];
        }
        return ['valido' => true, 'mensaje' => 'OK'];
    }
}
