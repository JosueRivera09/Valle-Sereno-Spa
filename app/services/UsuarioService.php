<?php
/**
 * Capa de Servicios: Lógica de negocio y validación para Usuarios
 * Spa Valle Sereno - Arquitectura N-Capas
 */

require_once __DIR__ . '/../models/Usuario.php';

class UsuarioService {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Listar todos los usuarios
     */
    public function listar(): array {
        return $this->usuarioModel->getAll();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId(int $id): ?array {
        return $this->usuarioModel->getById($id);
    }

    /**
     * Datos para formularios (roles y empleados disponibles)
     */
    public function obtenerCatalogos(): array {
        return [
            'roles'     => $this->usuarioModel->getRoles(),
            'empleados' => $this->usuarioModel->getEmpleados()
        ];
    }

    /**
     * Crear un nuevo usuario
     */
    public function crear(array $data): array {
        $usuario = trim($data['usuario'] ?? '');
        $password = $data['password'] ?? '';
        $idRol = (int)($data['id_rol'] ?? 0);
        $idEmpleado = !empty($data['id_empleado']) ? (int)$data['id_empleado'] : null;
        $estado = ($data['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

        if (empty($usuario) || empty($password) || $idRol <= 0) {
            return ['success' => false, 'message' => 'Por favor complete todos los campos obligatorios.'];
        }

        if (strlen($usuario) < 3 || strlen($usuario) > 50) {
            return ['success' => false, 'message' => 'El nombre de usuario debe tener entre 3 y 50 caracteres.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.'];
        }

        if ($this->usuarioModel->existsUsuario($usuario)) {
            return ['success' => false, 'message' => "El usuario '{$usuario}' ya se encuentra registrado."];
        }

        // Hashing seguro con BCRYPT
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $newId = $this->usuarioModel->create([
            'id_rol'        => $idRol,
            'id_empleado'   => $idEmpleado,
            'usuario'       => $usuario,
            'password_hash' => $passwordHash,
            'estado'        => $estado
        ]);

        return [
            'success' => true,
            'message' => 'Usuario creado exitosamente.',
            'id'      => $newId
        ];
    }

    /**
     * Actualizar datos del usuario (sin cambiar contraseña)
     */
    public function actualizar(int $id, array $data): array {
        $user = $this->usuarioModel->getById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'El usuario no existe.'];
        }

        $usuario = trim($data['usuario'] ?? '');
        $idRol = (int)($data['id_rol'] ?? 0);
        $idEmpleado = !empty($data['id_empleado']) ? (int)$data['id_empleado'] : null;
        $estado = ($data['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

        if (empty($usuario) || $idRol <= 0) {
            return ['success' => false, 'message' => 'El nombre de usuario y rol son obligatorios.'];
        }

        if ($this->usuarioModel->existsUsuario($usuario, $id)) {
            return ['success' => false, 'message' => "El nombre de usuario '{$usuario}' ya pertenece a otra cuenta."];
        }

        // Evitar que el administrador principal se inactive a sí mismo
        if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $id && $estado === 'inactivo') {
            return ['success' => false, 'message' => 'No puedes desactivar tu propia cuenta en sesión.'];
        }

        $this->usuarioModel->update($id, [
            'id_rol'      => $idRol,
            'id_empleado' => $idEmpleado,
            'usuario'     => $usuario,
            'estado'      => $estado
        ]);

        return ['success' => true, 'message' => 'Usuario actualizado correctamente.'];
    }

    /**
     * Restablecer / cambiar contraseña
     */
    public function cambiarPassword(int $id, string $nuevaPassword): array {
        $user = $this->usuarioModel->getById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'El usuario no existe.'];
        }

        if (strlen($nuevaPassword) < 6) {
            return ['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres.'];
        }

        $passwordHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        $this->usuarioModel->updatePassword($id, $passwordHash);

        return ['success' => true, 'message' => "Contraseña actualizada exitosamente para {$user['usuario']}."];
    }

    /**
     * Alternar estado activo / inactivo
     */
    public function toggleEstado(int $id): array {
        if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $id) {
            return ['success' => false, 'message' => 'No puedes desactivar tu propia cuenta.'];
        }

        $user = $this->usuarioModel->getById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }

        $this->usuarioModel->toggleEstado($id);
        $nuevoEstado = $user['estado'] === 'activo' ? 'inactivo' : 'activo';

        return [
            'success'      => true,
            'nuevo_estado' => $nuevoEstado,
            'message'      => "Estado del usuario {$user['usuario']} cambiado a '{$nuevoEstado}'."
        ];
    }

    /**
     * Desbloquear usuario y reiniciar sus intentos fallidos a 0
     */
    public function desbloquear(int $id): array {
        $user = $this->usuarioModel->getById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }

        $this->usuarioModel->desbloquear($id);

        return [
            'success' => true,
            'message' => "La cuenta de {$user['usuario']} ha sido desbloqueada satisfactoriamente con 0 intentos fallidos."
        ];
    }
}
