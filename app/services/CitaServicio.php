
<?php
require_once __DIR__ . '/../models/Database.php';

class CitaServicio {
    private PDO $db;

    public function __construct() {
        $this->db = database::getConnection();
    }

    /**
     * Verifica si un empleado (terapeuta) ya tiene una cita en el mismo rango de hora y fecha.
     */
    public function verificarCruceHorario(int $idEmpleado, string $fecha, string $horaInicio, string $horaFin): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM citas 
            WHERE id_empleado = :id_empleado 
              AND fecha = :fecha 
              AND estado != 'Cancelada' 
              AND (
                  (:hora_inicio1 BETWEEN hora_inicio AND hora_fin) OR 
                  (:hora_fin1 BETWEEN hora_inicio AND hora_fin) OR 
                  (hora_inicio BETWEEN :hora_inicio2 AND :hora_fin2)
              )
        ");
        $stmt->execute([
            'id_empleado'  => $idEmpleado,
            'fecha'        => $fecha,
            'hora_inicio1' => $horaInicio,
            'hora_fin1'    => $horaFin,
            'hora_inicio2' => $horaInicio,
            'hora_fin2'    => $horaFin
        ]);
        
        $resultado = $stmt->fetch();
        return ($resultado['total'] > 0);
    }
     * Obtiene los terapeutas que NO tienen cruce de horario para una fecha y rango dado.
     */
    public function obtenerTerapeutasDisponibles(string $fecha, string $horaInicio, string $horaFin): array {
        $sql = "
            SELECT e.id, e.nombre_completo, e.cargo, e.especialidades 
            FROM empleados e 
            WHERE e.estado = 'activo' 
              AND e.id NOT IN (
                  SELECT c.id_empleado 
                  FROM citas c 
                  WHERE c.fecha = :fecha 
                    AND c.estado != 'Cancelada' 
                    AND (
                        (:hora_inicio1 BETWEEN c.hora_inicio AND c.hora_fin) OR 
                        (:hora_fin1 BETWEEN c.hora_inicio AND c.hora_fin) OR 
                        (c.hora_inicio BETWEEN :hora_inicio2 AND :hora_fin2)
                    )
              )
            ORDER BY e.nombre_completo ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha'        => $fecha,
            'hora_inicio1' => $horaInicio,
            'hora_fin1'    => $horaFin,
            'hora_inicio2' => $horaInicio,
            'hora_fin2'    => $horaFin
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Registro express de cliente rápido desde el modal de citas.
     */
    public function registrarClienteRapido(string $nombre, string $telefono, string $fechaNac, string $correo = ''): array {
        if (empty(trim($nombre)) || empty(trim($telefono))) {
            return ['success' => false, 'mensaje' => 'Nombre y teléfono son obligatorios.'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO clientes (nombre_completo, fecha_nacimiento, telefono, correo, estado) 
            VALUES (:nombre, :fecha_nac, :telefono, :correo, 'activo')
        ");
        $res = $stmt->execute([
            'nombre'    => trim($nombre),
            'fecha_nac' => !empty($fechaNac) ? $fechaNac : date('Y-m-d'),
            'telefono'  => trim($telefono),
            'correo'    => trim($correo)
        ]);

        if ($res) {
            $newId = (int)$this->db->lastInsertId();
            return [
                'success' => true,
                'cliente' => [
                    'id' => $newId,
                    'nombre_completo' => trim($nombre),
                    'telefono' => trim($telefono)
                ]
            ];
        }
        return ['success' => false, 'mensaje' => 'Error al guardar cliente en BD.'];
    }
}
