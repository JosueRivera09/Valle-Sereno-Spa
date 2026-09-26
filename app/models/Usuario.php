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
     * Actualizar la fecha y hora de último acceso
     */
    public function updateLastAccess(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE usuarios 
            SET ultimo_acceso = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }
}
