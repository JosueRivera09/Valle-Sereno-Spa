<?php
require_once __DIR__ . '/../models/Database.php';

class CitaServicio {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Verifica si un empleado (terapeuta) ya tiene una cita que se SOLAPE con el rango indicado.
     * Usa la fórmula estricta de solapamiento: nuevo_inicio < existente_fin AND nuevo_fin > existente_inicio
     * (permite citas seguidas, ej. 08:00-09:00 y 09:00-10:00, que con BETWEEN se rechazaban como cruce).
     * @param int|null $idCitaExcluir Id de la cita a excluir (para ediciones).
     */
    public function verificarCruceHorario(int $idEmpleado, string $fecha, string $horaInicio, string $horaFin, ?int $idCitaExcluir = null): bool {
        $sql = "
            SELECT COUNT(*) as total 
            FROM citas 
            WHERE id_empleado = :id_empleado 
              AND fecha = :fecha 
              AND estado != 'Cancelada' 
              AND :hora_inicio < hora_fin 
              AND :hora_fin > hora_inicio
        ";
        $params = [
            'id_empleado' => $idEmpleado,
            'fecha'       => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin'    => $horaFin
        ];

        if ($idCitaExcluir !== null && $idCitaExcluir > 0) {
            $sql .= " AND id != :id_excluir";
            $params['id_excluir'] = $idCitaExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $resultado = $stmt->fetch();
        return ((int)$resultado['total'] > 0);
    }

    /**
     * Valida el formato y la coherencia de fecha/horario de una cita.
     * @return array ['valido' => bool, 'mensaje' => string]
     */
    public function validarFechaHorario(string $fecha, string $horaInicio, string $horaFin): array {
        $patronHora = '/^([01]\d|2[0-3]):[0-5]\d$/';

        $objFecha = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$objFecha || $objFecha->format('Y-m-d') !== $fecha) {
            return ['valido' => false, 'mensaje' => 'La fecha de la cita no es válida.'];
        }

        if (!preg_match($patronHora, $horaInicio) || !preg_match($patronHora, $horaFin)) {
            return ['valido' => false, 'mensaje' => 'Los horarios deben tener formato HH:MM válido.'];
        }

        if ($horaFin <= $horaInicio) {
            return ['valido' => false, 'mensaje' => 'La hora de fin debe ser posterior a la hora de inicio.'];
        }

        if ($fecha < date('Y-m-d')) {
            return ['valido' => false, 'mensaje' => 'No se pueden agendar citas en fechas pasadas.'];
        }

        return ['valido' => true, 'mensaje' => 'OK'];
    }

    /**
     * Verifica que la cita caiga dentro del turno registrado del empleado
     * (disponibilidad_empleados: dia_semana 1=Lunes .. 7=Domingo).
     */
    public function verificarDisponibilidadEmpleado(int $idEmpleado, string $fecha, string $horaInicio, string $horaFin): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM disponibilidad_empleados 
            WHERE id_empleado = :id_empleado 
              AND estado = 'activo'
              AND dia_semana = (DAYOFWEEK(:fecha) + 5) MOD 7 + 1
              AND hora_inicio <= :hora_inicio 
              AND hora_fin >= :hora_fin
        ");
        $stmt->execute([
            'id_empleado' => $idEmpleado,
            'fecha'       => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin'    => $horaFin
        ]);
        return ((int)$stmt->fetchColumn() > 0);
    }

    /**
     * Obtiene los terapeutas que NO tienen cruce de horario y que además
     * están dentro de su turno registrado para la fecha/hora solicitada.
     * Si se pasa idServicio, filtra a los terapeutas que ofrecen dicho servicio.
     */
    public function obtenerTerapeutasDisponibles(string $fecha, string $horaInicio, string $horaFin, ?int $idServicio = null): array {
        // Nota: con PDO::ATTR_EMULATE_PREPARES = false cada marcador nombrado
        // debe usarse una sola vez, por eso los nombres difieren por subconsulta.
        $params = [
            'fecha_dis'    => $fecha,
            'hora_ini_dis' => $horaInicio,
            'hora_fin_dis' => $horaFin,
            'fecha_cit'    => $fecha,
            'hora_ini_cit' => $horaInicio,
            'hora_fin_cit' => $horaFin
        ];

        $serviceFilter = '';
        if ($idServicio !== null && $idServicio > 0) {
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM empleado_servicios WHERE id_servicio = :id_serv");
            $stmtCheck->execute(['id_serv' => $idServicio]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                $serviceFilter = " AND e.id IN (SELECT id_empleado FROM empleado_servicios WHERE id_servicio = :id_servicio) ";
                $params['id_servicio'] = $idServicio;
            }
        }

        $sql = "
            SELECT e.id, e.nombre_completo, e.cargo, e.especialidades 
            FROM empleados e 
            WHERE e.estado = 'activo' 
              AND e.cargo LIKE '%Terapeuta%'
              {$serviceFilter}
              AND EXISTS (
                  SELECT 1 FROM disponibilidad_empleados d
                  WHERE d.id_empleado = e.id 
                    AND d.estado = 'activo'
                    AND d.dia_semana = (DAYOFWEEK(:fecha_dis) + 5) MOD 7 + 1
                    AND d.hora_inicio <= :hora_ini_dis 
                    AND d.hora_fin >= :hora_fin_dis
              )
              AND NOT EXISTS (
                  SELECT 1 FROM citas c
                  WHERE c.id_empleado = e.id 
                    AND c.fecha = :fecha_cit 
                    AND c.estado != 'Cancelada' 
                    AND :hora_ini_cit < c.hora_fin 
                    AND :hora_fin_cit > c.hora_inicio
              )
            ORDER BY e.nombre_completo ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Registro express de cliente rápido desde el modal de citas.
     */
    public function registrarClienteRapido(string $nombre, string $telefono, string $fechaNac, string $correo = ''): array {
        if (empty(trim($nombre)) || empty(trim($telefono))) {
            return ['success' => false, 'mensaje' => 'Nombre y teléfono son obligatorios.'];
        }

        if (strlen(trim($nombre)) > 120) {
            return ['success' => false, 'mensaje' => 'El nombre del cliente no puede exceder 120 caracteres.'];
        }

        // La fecha de nacimiento es obligatoria en el esquema (DATE NOT NULL): validar formato o excluir
        if (!empty($fechaNac)) {
            $objFecha = DateTime::createFromFormat('Y-m-d', $fechaNac);
            if (!$objFecha || $objFecha->format('Y-m-d') !== $fechaNac || $fechaNac > date('Y-m-d')) {
                return ['success' => false, 'mensaje' => 'La fecha de nacimiento no es válida.'];
            }
        }

        if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'mensaje' => 'El correo electrónico no tiene un formato válido.'];
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
