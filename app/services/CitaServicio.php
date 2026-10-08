
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
        return ($resultado['total'] > 0); // Retorna true si hay cruce de horarios
    }
}
