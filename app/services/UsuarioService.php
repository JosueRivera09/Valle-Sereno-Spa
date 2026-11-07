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
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        $idRol = (int)($data['id_rol'] ?? 0);
        $idEmpleado = !empty($data['id_empleado']) ? (int)$data['id_empleado'] : null;
        $estado = ($data['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

        if (empty($usuario) || empty($email) || empty($password) || $idRol <= 0) {
            return ['success' => false, 'message' => 'Complete usuario, correo, contraseña y rol.'];
        }

        if (strlen($usuario) < 3 || strlen($usuario) > 50) {
            return ['success' => false, 'message' => 'El nombre de usuario debe tener entre 3 y 50 caracteres.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'El correo electrónico no tiene un formato válido.'];
        }

        $errorPassword = $this->validarPassword($password);
        if ($errorPassword !== null) {
            return ['success' => false, 'message' => $errorPassword];
        }

        if ($this->usuarioModel->existsUsuario($usuario)) {
            return ['success' => false, 'message' => "El usuario '{$usuario}' ya se encuentra registrado."];
        }

        if ($this->usuarioModel->existsEmail($email)) {
            return ['success' => false, 'message' => "El correo '{$email}' ya pertenece a otra cuenta."];
        }

        // Hashing seguro con BCRYPT
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $newId = $this->usuarioModel->create([
            'id_rol'        => $idRol,
            'id_empleado'   => $idEmpleado,
            'usuario'       => $usuario,
            'email'         => $email,
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
        $email = strtolower(trim($data['email'] ?? ''));
        $idRol = (int)($data['id_rol'] ?? 0);
        $idEmpleado = !empty($data['id_empleado']) ? (int)$data['id_empleado'] : null;
        $estado = ($data['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

        if (empty($usuario) || empty($email) || $idRol <= 0) {
            return ['success' => false, 'message' => 'El nombre de usuario, el correo y el rol son obligatorios.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'El correo electrónico no tiene un formato válido.'];
        }

        if ($this->usuarioModel->existsUsuario($usuario, $id)) {
            return ['success' => false, 'message' => "El nombre de usuario '{$usuario}' ya pertenece a otra cuenta."];
        }

        if ($this->usuarioModel->existsEmail($email, $id)) {
            return ['success' => false, 'message' => "El correo '{$email}' ya pertenece a otra cuenta."];
        }

        // Evitar que el administrador principal se inactive a sí mismo
        if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $id && $estado === 'inactivo') {
            return ['success' => false, 'message' => 'No puedes desactivar tu propia cuenta en sesión.'];
        }

        $this->usuarioModel->update($id, [
            'id_rol'      => $idRol,
            'id_empleado' => $idEmpleado,
            'usuario'     => $usuario,
            'email'       => $email,
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

        $errorPassword = $this->validarPassword($nuevaPassword);
        if ($errorPassword !== null) {
            return ['success' => false, 'message' => $errorPassword];
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

    /**
     * Política mínima de contraseñas: 8+ caracteres con al menos una letra y un número.
     * @return string|null Mensaje de error, o null si la contraseña es válida.
     */
    private function validarPassword(string $password): ?string {
        if (strlen($password) < 8) {
            return 'La contraseña debe tener al menos 8 caracteres.';
        }
        if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            return 'La contraseña debe incluir al menos una letra y un número.';
        }
        return null;
    }
}
