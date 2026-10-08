<?php
/**
 * Modelo de Categoria & Servicio - Spa Valle Sereno
 */
require_once __DIR__ . '/../../config/config.php';

class ServicioModel {
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

    // --- Categorías ---
    public function obtenerCategorias(): array {
        $stmt = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function crearCategoria(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre, descripcion, estado) VALUES (:nombre, :descripcion, :estado)");
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':estado' => $data['estado'] ?? 'activo'
        ]);
    }

    public function actualizarCategoria(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE categorias SET nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':estado' => $data['estado'] ?? 'activo'
        ]);
    }

    // --- Servicios ---
    public function obtenerServicios(): array {
        $sql = "SELECT s.*, c.nombre as categoria_nombre 
                FROM servicios s 
                LEFT JOIN categorias c ON s.id_categoria = c.id 
                ORDER BY s.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function crearServicio(array $data): bool {
        $sql = "INSERT INTO servicios (id_categoria, nombre, descripcion, costo, duracion_minutos, estado) 
                VALUES (:id_categoria, :nombre, :descripcion, :costo, :duracion_minutos, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_categoria' => $data['id_categoria'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':costo' => $data['costo'],
            ':duracion_minutos' => $data['duracion_minutos'],
            ':estado' => $data['estado'] ?? 'activo'
        ]);
    }

    public function actualizarServicio(int $id, array $data): bool {
        $sql = "UPDATE servicios 
                SET id_categoria = :id_categoria, nombre = :nombre, descripcion = :descripcion, 
                    costo = :costo, duracion_minutos = :duracion_minutos, estado = :estado 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':id_categoria' => $data['id_categoria'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':costo' => $data['costo'],
            ':duracion_minutos' => $data['duracion_minutos'],
            ':estado' => $data['estado'] ?? 'activo'
        ]);
    }

    public function cambiarEstadoServicio(int $id, string $estado): bool {
        $stmt = $this->db->prepare("UPDATE servicios SET estado = :estado WHERE id = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}
