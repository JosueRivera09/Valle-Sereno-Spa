<?php
/**
 * Modelo de Cliente - Spa Valle Sereno
 */
require_once __DIR__ . '/../../config/config.php';

class Cliente {
    private PDO $db;

    public function __construct(?PDO $pdo = null) {
        if ($pdo !== null) {
            $this->db = $pdo;
        } else {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $this->db = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
    }

    public function obtenerTodos(): array {
        $stmt = $this->db->query("SELECT * FROM clientes ORDER BY creado_en DESC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function crear(array $data): bool {
        $sql = "INSERT INTO clientes (nombre_completo, fecha_nacimiento, telefono, correo, estado) 
                VALUES (:nombre_completo, :fecha_nacimiento, :telefono, :correo, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre_completo' => $data['nombre_completo'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':telefono' => $data['telefono'],
            ':correo' => $data['correo'],
            ':estado' => $data['estado'] ?? 'activo'
        ]);
    }

    public function actualizar(int $id, array $data): bool {
        $sql = "UPDATE clientes 
                SET nombre_completo = :nombre_completo, 
                    fecha_nacimiento = :fecha_nacimiento, 
                    telefono = :telefono, 
                    correo = :correo, 
                    estado = :estado 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombre_completo' => $data['nombre_completo'],
            ':fecha_nacimiento' => $data['fecha_nacimiento'],
            ':telefono' => $data['telefono'],
            ':correo' => $data['correo'],
            ':estado' => $data['estado'] ?? 'activo'
        ]);
    }

    public function cambiarEstado(int $id, string $nuevoEstado): bool {
        $stmt = $this->db->prepare("UPDATE clientes SET estado = :estado WHERE id = :id");
        return $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);
    }

    public function obtenerHistorialCitas(int $idCliente): array {
        $sql = "SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.total, c.estado, c.observaciones,
                       e.nombre_completo as terapeuta_nombre
                FROM citas c
                LEFT JOIN empleados e ON c.id_empleado = e.id
                WHERE c.id_cliente = :id_cliente
                ORDER BY c.fecha DESC, c.hora_inicio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_cliente' => $idCliente]);
        return $stmt->fetchAll();
    }
}
