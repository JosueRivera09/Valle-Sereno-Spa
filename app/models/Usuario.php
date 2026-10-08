<?php
/**
 * Modelo de Usuario (Capa de Acceso y Modelo de Datos)
 * Alineado con la estructura oficial de spa_db:
 * - Tabla usuarios: (id, id_rol, id_empleado, usuario, password_hash, estado, ultimo_acceso)
 * - Tabla roles: (id, nombre, descripcion)
 * - Tabla empleados: (id, nombre_completo, correo, telefono, cargo)
 */

require_once __DIR__ . '/Database.php';

class Usuario {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Buscar un usuario por identificador de login (nombre de usuario o correo corporativo)
     */
    public function findByLogin(string $loginIdentifier): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, 
                u.id_rol, 
                u.id_empleado, 
                u.usuario, 
                u.password_hash, 
                u.estado,
                u.intentos_fallidos,
                u.bloqueado_hasta,
                r.nombre AS rol_nombre,
                COALESCE(e.nombre_completo, u.usuario) AS nombre_completo,
                COALESCE(e.correo, u.usuario) AS email,
                e.cargo
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id
            LEFT JOIN empleados e ON u.id_empleado = e.id
            WHERE u.usuario = :user_param OR e.correo = :email_param
            LIMIT 1
        ");
        $stmt->execute([
            'user_param'  => $loginIdentifier,
            'email_param' => $loginIdentifier
        ]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Incrementar intentos fallidos y bloquear si supera el límite
     */
    public function registerFailedAttempt(int $id, int $maxAttempts = 3): int {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET intentos_fallidos = intentos_fallidos + 1,
                estado = CASE WHEN intentos_fallidos + 1 >= :max THEN 'bloqueado' ELSE estado END
            WHERE id = :id
        ");
        $stmt->execute([
            'id'  => $id,
            'max' => $maxAttempts
        ]);

        $stmtCount = $this->db->prepare("SELECT intentos_fallidos, estado FROM usuarios WHERE id = :id");
        $stmtCount->execute(['id' => $id]);
        $row = $stmtCount->fetch();
        return (int)($row['intentos_fallidos'] ?? 0);
    }

    /**
     * Reiniciar contador de intentos fallidos tras login exitoso o desbloqueo
     */
    public function resetFailedAttempts(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET intentos_fallidos = 0 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Desbloquear usuario y restaurar a estado 'activo' con intentos en 0
     */
    public function desbloquear(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET estado = 'activo', 
                intentos_fallidos = 0,
                bloqueado_hasta = NULL 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Actualizar la fecha y hora de último acceso
     */
    public function updateLastAccess(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET ultimo_acceso = NOW(),
                intentos_fallidos = 0
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Listar todos los usuarios con información de rol y empleado asociado
     */
    public function getAll(): array {
        $sql = "
            SELECT 
                u.id,
                u.id_rol,
                u.id_empleado,
                u.usuario,
                u.estado,
                u.intentos_fallidos,
                u.bloqueado_hasta,
                u.creado_en,
                u.ultimo_acceso,
                r.nombre AS rol_nombre,
                COALESCE(e.nombre_completo, 'Sin vincular') AS nombre_completo,
                COALESCE(e.correo, 'Sin correo') AS email,
                COALESCE(e.cargo, 'Usuario del Sistema') AS cargo
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id
            LEFT JOIN empleados e ON u.id_empleado = e.id
            ORDER BY u.id DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener un usuario por su ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.id_rol,
                u.id_empleado,
                u.usuario,
                u.estado,
                u.intentos_fallidos,
                u.bloqueado_hasta,
                u.creado_en,
                u.ultimo_acceso,
                r.nombre AS rol_nombre,
                COALESCE(e.nombre_completo, '') AS nombre_completo,
                COALESCE(e.correo, '') AS email,
                e.cargo
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id
            LEFT JOIN empleados e ON u.id_empleado = e.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Verificar si un nombre de usuario ya está registrado
     */
    public function existsUsuario(string $usuario, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM usuarios WHERE usuario = :usuario";
        $params = ['usuario' => $usuario];
        if ($excludeId !== null) {
            $sql .= " AND id != :excludeId";
            $params['excludeId'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    /**
     * Crear un nuevo usuario en la base de datos
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (id_rol, id_empleado, usuario, password_hash, estado)
            VALUES (:id_rol, :id_empleado, :usuario, :password_hash, :estado)
        ");
        $stmt->execute([
            'id_rol'        => $data['id_rol'],
            'id_empleado'   => !empty($data['id_empleado']) ? (int)$data['id_empleado'] : null,
            'usuario'       => $data['usuario'],
            'password_hash' => $data['password_hash'],
            'estado'        => $data['estado'] ?? 'activo'
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualizar datos básicos del usuario (rol, empleado, nombre de usuario y estado)
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET id_rol = :id_rol,
                id_empleado = :id_empleado,
                usuario = :usuario,
                estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => $id,
            'id_rol'      => $data['id_rol'],
            'id_empleado' => !empty($data['id_empleado']) ? (int)$data['id_empleado'] : null,
            'usuario'     => $data['usuario'],
            'estado'      => $data['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualizar contraseña de un usuario
     */
    public function updatePassword(int $id, string $passwordHash): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET password_hash = :hash
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'   => $id,
            'hash' => $passwordHash
        ]);
    }

    /**
     * Alternar estado (activo / inactivo)
     */
    public function toggleEstado(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET estado = CASE WHEN estado = 'activo' THEN 'inactivo' ELSE 'activo' END
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Obtener el catálogo de roles
     */
    public function getRoles(): array {
        $stmt = $this->db->query("SELECT id, nombre, descripcion FROM roles ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener empleados activos para vincular con cuentas
     */
    public function getEmpleados(): array {
        $stmt = $this->db->query("
            SELECT id, nombre_completo, correo, cargo 
            FROM empleados 
            WHERE estado = 'activo' 
            ORDER BY nombre_completo ASC
        ");
        return $stmt->fetchAll();
    }
}
