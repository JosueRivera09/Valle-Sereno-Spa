-- ============================================================================
-- VALLE SERENO SPA S.A. - RESPALDO OFICIAL DE BASE DE DATOS
-- Base de datos: spa_db
-- Fecha de generación: 2026-10-08 05:59:48
-- SERVIDOR: localhost:3306
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `categorias`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `categorias`
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES ('1', 'Relajantes', 'Masajes y tratamientos para alivio de estres', 'activo');
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES ('2', 'Terapeuticos', 'Servicios enfocados en rehabilitacion y dolor muscular', 'activo');
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES ('3', 'Holísticos', 'Terapias integrales y bienestar energetico', 'activo');
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES ('4', 'Faciales y Estética', 'Tratamientos de hidratación, antienvejecimiento y limpieza facial profunda', 'activo');
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES ('5', 'Hidroterapia y Circuitos', 'Saunas, jacuzzis termales y baños de vapor revitalizantes', 'activo');
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES ('6', 'Rituales & Paquetes VIP', 'Paquetes de día completo con combinaciones de tratamientos exclusivos', 'activo');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `cita_detalles`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cita_detalles`;
CREATE TABLE `cita_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cita` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `precio_aplicado` decimal(10,2) NOT NULL,
  `duracion_aplicada` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_detalles_citas` (`id_cita`),
  KEY `fk_detalles_servicios` (`id_servicio`),
  CONSTRAINT `fk_detalles_citas` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_detalles_servicios` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_detalles_precio` CHECK (`precio_aplicado` >= 0),
  CONSTRAINT `chk_detalles_duracion` CHECK (`duracion_aplicada` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `cita_detalles`
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('1', '1', '2', '65.00', '75');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('2', '2', '3', '50.00', '50');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('3', '3', '11', '35.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('4', '4', '4', '60.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('5', '5', '10', '85.00', '75');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('6', '6', '1', '45.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('7', '7', '7', '70.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('8', '8', '6', '40.00', '45');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('9', '9', '2', '65.00', '75');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('10', '10', '1', '45.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('11', '11', '4', '60.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('12', '12', '9', '55.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('13', '13', '8', '80.00', '90');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('14', '14', '13', '150.00', '120');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('15', '15', '1', '45.00', '60');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('16', '16', '10', '85.00', '75');
INSERT INTO `cita_detalles` (`id`, `id_cita`, `id_servicio`, `precio_aplicado`, `duracion_aplicada`) VALUES ('17', '17', '5', '55.00', '60');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `citas`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL COMMENT 'Terapeuta asignado',
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('Pendiente','Confirmada','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente',
  `observaciones` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_citas_terapeuta_fecha` (`id_empleado`,`fecha`),
  KEY `idx_citas_cliente_fecha` (`id_cliente`,`fecha`),
  CONSTRAINT `fk_citas_clientes` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_citas_empleados` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_citas_horario` CHECK (`hora_fin` > `hora_inicio`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `citas`
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('1', '1', '3', '2026-09-29', '09:00:00', '10:15:00', '65.00', 'Completada', 'Prefiere aceite de lavanda tibio. Cabina 1.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('2', '2', '3', '2026-09-29', '10:30:00', '11:20:00', '50.00', 'Completada', 'Llamar 30 minutos antes para confirmar llegada.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('3', '3', '4', '2026-09-29', '11:45:00', '12:45:00', '35.00', 'Completada', 'Circuito de hidroterapia relax y toallas tibias.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('4', '4', '5', '2026-09-29', '14:00:00', '15:00:00', '60.00', 'Completada', 'Tensión cervical severa por jornada laboral prolongada.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('5', '5', '6', '2026-09-29', '15:30:00', '16:45:00', '85.00', 'Completada', 'Cliente con piel sensible; usar mascarilla hipoalergénica.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('6', '6', '4', '2026-09-29', '17:00:00', '18:00:00', '45.00', 'Completada', 'Masaje sueco clásico.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('7', '7', '3', '2026-09-29', '08:00:00', '09:00:00', '70.00', 'Completada', 'Sesión matutina completada satisfactoriamente.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('8', '8', '5', '2026-09-29', '18:30:00', '19:15:00', '40.00', 'Completada', 'Reflexología podal vespertina.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('9', '9', '3', '2026-09-28', '10:00:00', '11:15:00', '65.00', 'Completada', 'Piedras calientes.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('10', '10', '4', '2026-09-28', '11:30:00', '12:30:00', '45.00', 'Completada', 'Atendida a tiempo en cabina 2.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('11', '2', '5', '2026-09-28', '15:00:00', '16:00:00', '60.00', 'Cancelada', 'Canceló por inconvenientes de transporte.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('12', '1', '6', '2026-09-27', '09:00:00', '10:00:00', '55.00', 'Completada', 'Limpieza profunda con ozono.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('13', '3', '3', '2026-09-27', '16:00:00', '17:30:00', '80.00', 'Completada', 'Masaje ayurvédico.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('14', '5', '4', '2026-09-26', '14:00:00', '16:00:00', '150.00', 'Completada', 'Ritual dúo de fin de semana.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('15', '6', '3', '2026-09-30', '09:30:00', '10:30:00', '45.00', 'Completada', 'Cita reservada con anticipación.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('16', '7', '6', '2026-09-30', '11:00:00', '12:15:00', '85.00', 'Completada', 'Tratamiento de colágeno.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('17', '8', '5', '2026-10-01', '15:00:00', '16:00:00', '55.00', 'Completada', 'Drenaje linfático.', '2026-09-29 17:18:21');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('18', '1', '1', '2026-10-08', '11:00:00', '12:00:00', '950.00', 'Completada', 'Cita agendada mediante flujo automatizado de Administrador', '2026-10-07 20:33:52');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('19', '5', '3', '2026-10-07', '20:48:00', '21:48:00', '1000.00', 'Completada', 'tatat', '2026-10-07 20:44:33');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('20', '11', '5', '2026-10-08', '10:00:00', '10:50:00', '50.28', 'Completada', 'fdsfs', '2026-10-07 20:59:55');
INSERT INTO `citas` (`id`, `id_cliente`, `id_empleado`, `fecha`, `hora_inicio`, `hora_fin`, `total`, `estado`, `observaciones`, `creado_en`) VALUES ('21', '11', '3', '2026-10-08', '10:00:00', '10:50:00', '50.00', 'Completada', '', '2026-10-07 21:48:11');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `clientes`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(120) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_clientes_telefono` (`telefono`),
  KEY `idx_clientes_correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `clientes`
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('1', 'Elena Rostova', '1992-04-15', '3104561234', 'elena.rostova@gmail.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('2', 'Carlos Mendoza Varela', '1985-11-23', '3117894561', 'carlos.mendoza@outlook.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('3', 'Sofía Benítez Carvajal', '1996-08-30', '3128905672', 'sofia.benitez@yahoo.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('4', 'Mauricio Cárdenas Gil', '1979-02-14', '3159988112', 'mauricio.cardenas@empresa.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('5', 'Valentina Henao Ríos', '2000-06-19', '3187766554', 'valentina.henao@gmail.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('6', 'Andrés Felipe Morales', '1990-12-05', '3164433221', 'andres.morales@hotmail.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('7', 'Camila Ochoa Duque', '1994-09-11', '3171239874', 'camila.ochoa@gmail.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('8', 'Guillermo Arango Soto', '1968-03-27', '3193456781', 'guillermo.arango@gmail.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('9', 'Patricia Montoya Londoño', '1988-07-22', '3146543210', 'patricia.montoya@gmail.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('10', 'Javier Solís Bedoya', '1995-01-18', '3139876541', 'javier.solis@empresa.com', 'activo', '2026-09-29 17:18:21');
INSERT INTO `clientes` (`id`, `nombre_completo`, `fecha_nacimiento`, `telefono`, `correo`, `estado`, `creado_en`) VALUES ('11', 'paulina rubio', '2026-10-08', '888888888', '', 'activo', '2026-10-07 20:59:30');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `disponibilidad_empleados`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `disponibilidad_empleados`;
CREATE TABLE `disponibilidad_empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT '1=Lunes, 7=Domingo',
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`),
  KEY `fk_disp_empleados` (`id_empleado`),
  CONSTRAINT `fk_disp_empleados` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_disp_horario` CHECK (`hora_fin` > `hora_inicio`),
  CONSTRAINT `chk_disp_dia` CHECK (`dia_semana` between 1 and 7)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `disponibilidad_empleados`
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('1', '3', '1', '08:00:00', '16:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('2', '3', '2', '08:00:00', '16:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('3', '3', '3', '08:00:00', '16:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('4', '3', '4', '08:00:00', '16:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('5', '3', '5', '08:00:00', '16:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('6', '3', '6', '09:00:00', '14:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('7', '4', '1', '10:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('8', '4', '2', '10:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('9', '4', '3', '10:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('10', '4', '4', '10:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('11', '4', '5', '10:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('12', '4', '6', '09:00:00', '15:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('13', '5', '2', '08:00:00', '17:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('14', '5', '3', '08:00:00', '17:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('15', '5', '4', '08:00:00', '17:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('16', '5', '5', '08:00:00', '17:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('17', '5', '6', '08:00:00', '16:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('18', '6', '1', '09:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('19', '6', '2', '09:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('20', '6', '3', '09:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('21', '6', '4', '09:00:00', '18:00:00', 'activo');
INSERT INTO `disponibilidad_empleados` (`id`, `id_empleado`, `dia_semana`, `hora_inicio`, `hora_fin`, `estado`) VALUES ('22', '6', '5', '09:00:00', '18:00:00', 'activo');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `empleado_servicios`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `empleado_servicios`;
CREATE TABLE `empleado_servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_empleado` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_empleado_servicio` (`id_empleado`,`id_servicio`),
  KEY `fk_emp_serv_servicios` (`id_servicio`),
  CONSTRAINT `fk_emp_serv_empleados` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_emp_serv_servicios` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `empleado_servicios`
INSERT INTO `empleado_servicios` (`id`, `id_empleado`, `id_servicio`, `creado_en`) VALUES ('1', '3', '14', '2026-10-07 21:36:20');
INSERT INTO `empleado_servicios` (`id`, `id_empleado`, `id_servicio`, `creado_en`) VALUES ('2', '3', '13', '2026-10-07 21:36:20');
INSERT INTO `empleado_servicios` (`id`, `id_empleado`, `id_servicio`, `creado_en`) VALUES ('3', '3', '12', '2026-10-07 21:36:22');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `empleados`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(120) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `especialidades` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `empleados`
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('1', 'Valeria Sereno', '3001234567', 'admin@vallesereno.com', 'Directora General', 'Gestion y Direccion', 'activo');
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('2', 'Camila Morales', '3009876543', 'recepcion@vallesereno.com', 'Jefa de Recepcion', 'Atencion al cliente y reservas', 'activo');
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('3', 'Mateo Delgado', '3015554321', 'terapeuta@vallesereno.com', 'Terapeuta Principal', 'Masajes HolÝsticos, Aromaterapia', 'activo');
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('4', 'Andrea Gómez Salcedo', '3024567890', 'andrea.gomez@vallesereno.com', 'Terapeuta Corporal', 'Masaje Sueco, Descontracturante, Piedras Calientes', 'activo');
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('5', 'David Alejandro Ruiz', '3031238901', 'david.ruiz@vallesereno.com', 'Fisioterapeuta y Quiromasajista', 'Rehabilitación muscular, Drenaje Linfático, Descarga deportiva', 'activo');
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('6', 'Lucía Fernanda Méndez', '3049988776', 'lucia.mendez@vallesereno.com', 'Cosmetóloga y Esteticista', 'Faciales de alta gama, Hidroterapia, Cuencos sonoros', 'activo');
INSERT INTO `empleados` (`id`, `nombre_completo`, `telefono`, `correo`, `cargo`, `especialidades`, `estado`) VALUES ('7', 'Mariana Restrepo Paz', '3053322114', 'mariana.paz@vallesereno.com', 'Recepcionista Asistente', 'Atención al huésped, Agendamiento, Facturación', 'activo');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `pagos`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `pagos`;
CREATE TABLE `pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cita` int(11) NOT NULL,
  `tipo_pago` enum('Efectivo','Tarjeta','Transferencia') NOT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('Pagado','Pendiente') NOT NULL DEFAULT 'Pagado',
  PRIMARY KEY (`id`),
  KEY `idx_pagos_cita_fecha` (`id_cita`,`fecha_pago`),
  CONSTRAINT `fk_pagos_citas` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_pagos_monto` CHECK (`monto` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `pagos`
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('1', '7', 'Efectivo', '2026-09-29 17:18:21', '70.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('2', '1', 'Tarjeta', '2026-09-29 17:18:21', '65.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('3', '3', 'Transferencia', '2026-09-29 17:18:21', '35.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('4', '4', 'Tarjeta', '2026-09-29 17:18:21', '60.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('5', '9', 'Efectivo', '2026-09-28 17:18:21', '65.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('6', '10', 'Tarjeta', '2026-09-28 17:18:21', '45.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('7', '12', 'Transferencia', '2026-09-27 17:18:21', '55.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('8', '13', 'Tarjeta', '2026-09-27 17:18:21', '80.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('9', '14', 'Tarjeta', '2026-09-26 17:18:21', '150.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('10', '18', 'Tarjeta', '2026-10-07 20:33:52', '950.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('11', '17', 'Efectivo', '2026-10-07 20:37:44', '55.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('12', '19', 'Efectivo', '2026-10-07 20:45:10', '1000.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('13', '20', 'Efectivo', '2026-10-07 21:46:15', '50.28', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('14', '16', 'Efectivo', '2026-10-07 21:46:20', '85.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('15', '15', 'Efectivo', '2026-10-07 21:46:23', '45.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('16', '8', 'Efectivo', '2026-10-07 21:46:27', '40.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('17', '6', 'Efectivo', '2026-10-07 21:46:31', '45.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('18', '5', 'Efectivo', '2026-10-07 21:46:35', '85.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('19', '2', 'Efectivo', '2026-10-07 21:46:38', '50.00', 'Pagado');
INSERT INTO `pagos` (`id`, `id_cita`, `tipo_pago`, `fecha_pago`, `monto`, `estado`) VALUES ('20', '21', 'Efectivo', '2026-10-07 21:48:44', '50.00', 'Pagado');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `roles`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `roles`
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES ('1', 'Administrador', 'Configuracion y control general');
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES ('2', 'Recepcionista', 'Atencion diaria, clientes, citas y cobros');
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES ('3', 'Terapeuta', 'Consulta de citas asignadas y atencion de servicios');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `servicios`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `duracion_minutos` int(11) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id`),
  KEY `fk_servicios_categorias` (`id_categoria`),
  CONSTRAINT `fk_servicios_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_servicios_costo` CHECK (`costo` >= 0),
  CONSTRAINT `chk_servicios_duracion` CHECK (`duracion_minutos` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `servicios`
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('1', '1', 'Masaje Sueco Relajante', 'Alivio de tensiones mediante maniobras suaves con aceites naturales', '45.00', '60', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('2', '1', 'Piedras Volcánicas Calientes', 'Terapia geotermal para calmar dolores y armonizar centros energéticos', '65.00', '75', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('3', '1', 'Aromaterapia & Lavanda Real', 'Sesión relajante con esencias botánicas puras y difusión aromática', '50.00', '50', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('4', '2', 'Masaje Descontracturante Profundo', 'Técnica focalizada en contracturas crónicas de espalda, cuello y hombros', '60.00', '60', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('5', '2', 'Drenaje Linfático Manual', 'Estimula el sistema circulatorio y la eliminación natural de toxinas', '55.00', '60', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('6', '2', 'Reflexología Podal Terapéutica', 'Estimulación de puntos reflejos en pies para aliviar dolencias corporales', '40.00', '45', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('7', '3', 'Terapia de Cuencos Tibetanos', 'Sanación vibracional y sonora para equilibrar mente y espíritu', '70.00', '60', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('8', '3', 'Masaje Ayurvédico Abhyanga', 'Masaje hindú con aceites tibios formulados según cada constitución física', '80.00', '90', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('9', '4', 'Limpieza Facial Profunda con Ozono', 'Exfoliación, vapor de ozono, extracción y mascarilla purificante botánica', '55.00', '60', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('10', '4', 'Velo de Colágeno & Oro 24K', 'Regeneración celular, hidratación intensa y luminosidad instantánea', '85.00', '75', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('11', '5', 'Circuito Termal Valle Sereno', 'Acceso a piscina de hidromasaje, sauna húmedo y duchas sensoriales', '35.00', '60', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('12', '5', 'Baño Herbal de Tina Serena', 'Inmersión en agua termal enriquecida con eucalipto, lavanda y sales marinas', '45.00', '45', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('13', '6', 'Ritual Serenity Oasis (Dúo)', 'Paquete para 2 personas: Masaje aromático, circuito termal y copa de cortesía', '150.00', '120', 'activo');
INSERT INTO `servicios` (`id`, `id_categoria`, `nombre`, `descripcion`, `costo`, `duracion_minutos`, `estado`) VALUES ('14', '6', 'Día de Spa Transformación Total', 'Exfoliación corporal completa, facial de lujo, masaje de 80 min y almuerzo détox', '180.00', '180', 'activo');

-- ----------------------------------------------------------------------------
-- Estructura de tabla: `usuarios`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `fk_usuarios_roles` (`id_rol`),
  KEY `fk_usuarios_empleados` (`id_empleado`),
  CONSTRAINT `fk_usuarios_empleados` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de tabla: `usuarios`
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('1', '1', '1', 'admin', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-25 20:48:53', '2026-10-07 21:42:22');
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('2', '2', '2', 'recepcion', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-25 20:48:53', '2026-09-25 20:51:40');
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('3', '3', '3', 'terapeuta', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-25 20:48:53', '2026-10-07 21:41:42');
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('4', '3', '4', 'andrea.terapeuta', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-29 17:18:21', '2026-09-28 16:30:00');
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('5', '3', '5', 'david.terapeuta', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-29 17:18:21', '2026-09-29 08:15:00');
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('6', '3', '6', 'lucia.terapeuta', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-29 17:18:21', '2026-09-29 09:00:00');
INSERT INTO `usuarios` (`id`, `id_rol`, `id_empleado`, `usuario`, `password_hash`, `estado`, `intentos_fallidos`, `bloqueado_hasta`, `creado_en`, `ultimo_acceso`) VALUES ('7', '2', '7', 'mariana.recepcion', '$2y$10$IN4jSozzUdOwxWkBzlzX..KrDAK.k9SdM5wETpfmAVJUsoBOK8cqq', 'activo', '0', NULL, '2026-09-29 17:18:21', '2026-09-29 11:20:00');

SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================================
-- FIN DEL RESPALDO SQL - VALLE SERENO SPA
-- ============================================================================
