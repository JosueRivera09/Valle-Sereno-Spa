<?php
/**
 * Servicio de Lógica de Negocio para Categorías y Catálogo de Servicios
 */
require_once __DIR__ . '/../models/ServicioModel.php';

class ServicioService {
    private ServicioModel $model;

    public function __construct(?ServicioModel $model = null) {
        $this->model = $model ?? new ServicioModel();
    }

    public function listarServicios(): array {
        return $this->model->obtenerServicios();
    }

    public function listarCategorias(): array {
        return $this->model->obtenerCategorias();
    }

    public function crearCategoria(array $data): array {
        if (empty(trim($data['nombre'] ?? ''))) {
            return ['success' => false, 'mensaje' => 'El nombre de la categoría es obligatorio.'];
        }
        $res = $this->model->crearCategoria($data);
        return $res ? ['success' => true, 'mensaje' => 'Categoría registrada correctamente.']
                    : ['success' => false, 'mensaje' => 'Error al guardar la categoría.'];
    }

    public function guardarServicio(array $data): array {
        $val = $this->validarServicio($data);
        if (!$val['valido']) {
            return ['success' => false, 'mensaje' => $val['mensaje']];
        }

        if (!empty($data['id'])) {
            $res = $this->model->actualizarServicio((int)$data['id'], $data);
            $msg = 'Tratamiento/Servicio actualizado.';
        } else {
            $res = $this->model->crearServicio($data);
            $msg = 'Tratamiento/Servicio registrado en catálogo.';
        }

        return $res ? ['success' => true, 'mensaje' => $msg]
                    : ['success' => false, 'mensaje' => 'Error al procesar el servicio en base de datos.'];
    }

    public function cambiarEstadoServicio(int $id, string $estado): array {
        if (!in_array($estado, ['activo', 'inactivo'])) {
            return ['success' => false, 'mensaje' => 'Estado no permitido.'];
        }
        $res = $this->model->cambiarEstadoServicio($id, $estado);
        return $res ? ['success' => true, 'mensaje' => 'Estado del servicio actualizado.']
                    : ['success' => false, 'mensaje' => 'Error al cambiar estado.'];
    }

    public function validarServicio(array $data): array {
        if (empty((int)($data['id_categoria'] ?? 0))) {
            return ['valido' => false, 'mensaje' => 'Debes seleccionar una categoría válida.'];
        }
        if (empty(trim($data['nombre'] ?? ''))) {
            return ['valido' => false, 'mensaje' => 'El nombre del tratamiento/servicio es obligatorio.'];
        }
        if (!isset($data['costo']) || (float)$data['costo'] < 0) {
            return ['valido' => false, 'mensaje' => 'El costo del servicio debe ser mayor o igual a 0.'];
        }
        if (empty((int)($data['duracion_minutos'] ?? 0)) || (int)$data['duracion_minutos'] <= 0) {
            return ['valido' => false, 'mensaje' => 'La duración en minutos debe ser mayor a 0.'];
        }
        return ['valido' => true, 'mensaje' => 'OK'];
    }
}
