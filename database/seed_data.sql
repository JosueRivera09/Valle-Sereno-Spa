-- ============================================================================
-- VALLE SERENO SPA - DATOS DE PRUEBA (SEEDS / DUMMY DATA)
-- ============================================================================
-- Este archivo inserta datos coherentes y completos para todas las tablas:
-- 1. categorias (complementarias)
-- 2. servicios (masajes, faciales, hidroterapia, etc.)
-- 3. empleados (terapeutas y personal adicional)
-- 4. usuarios (cuentas de acceso asociadas)
-- 5. disponibilidad_empleados (horarios de turnos)
-- 6. clientes (frecuentes y nuevos)
-- 7. citas (para hoy, fechas pasadas y futuras)
-- 8. cita_detalles (servicios por cada cita)
-- 9. pagos (liquidaciones en efectivo, tarjeta y transferencia)
-- ============================================================================

USE spa_db;

-- ----------------------------------------------------------------------------
-- 1. CATEGORÍAS ADICIONALES
-- ----------------------------------------------------------------------------
INSERT INTO categorias (id, nombre, descripcion, estado) VALUES
(4, 'Faciales y Estética', 'Tratamientos de hidratación, antienvejecimiento y limpieza facial profunda', 'activo'),
(5, 'Hidroterapia y Circuitos', 'Saunas, jacuzzis termales y baños de vapor revitalizantes', 'activo'),
(6, 'Rituales & Paquetes VIP', 'Paquetes de día completo con combinaciones de tratamientos exclusivos', 'activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), descripcion=VALUES(descripcion), estado=VALUES(estado);

-- ----------------------------------------------------------------------------
-- 2. CATÁLOGO DE SERVICIOS
-- ----------------------------------------------------------------------------
INSERT INTO servicios (id, id_categoria, nombre, descripcion, costo, duracion_minutos, estado) VALUES
-- Relajantes (Cat 1)
(1, 1, 'Masaje Sueco Relajante', 'Alivio de tensiones mediante maniobras suaves con aceites naturales', 45.00, 60, 'activo'),
(2, 1, 'Piedras Volcánicas Calientes', 'Terapia geotermal para calmar dolores y armonizar centros energéticos', 65.00, 75, 'activo'),
(3, 1, 'Aromaterapia & Lavanda Real', 'Sesión relajante con esencias botánicas puras y difusión aromática', 50.00, 50, 'activo'),

-- Terapéuticos (Cat 2)
(4, 2, 'Masaje Descontracturante Profundo', 'Técnica focalizada en contracturas crónicas de espalda, cuello y hombros', 60.00, 60, 'activo'),
(5, 2, 'Drenaje Linfático Manual', 'Estimula el sistema circulatorio y la eliminación natural de toxinas', 55.00, 60, 'activo'),
(6, 2, 'Reflexología Podal Terapéutica', 'Estimulación de puntos reflejos en pies para aliviar dolencias corporales', 40.00, 45, 'activo'),

-- Holísticos (Cat 3)
(7, 3, 'Terapia de Cuencos Tibetanos', 'Sanación vibracional y sonora para equilibrar mente y espíritu', 70.00, 60, 'activo'),
(8, 3, 'Masaje Ayurvédico Abhyanga', 'Masaje hindú con aceites tibios formulados según cada constitución física', 80.00, 90, 'activo'),

-- Faciales y Estética (Cat 4)
(9, 4, 'Limpieza Facial Profunda con Ozono', 'Exfoliación, vapor de ozono, extracción y mascarilla purificante botánica', 55.00, 60, 'activo'),
(10, 4, 'Velo de Colágeno & Oro 24K', 'Regeneración celular, hidratación intensa y luminosidad instantánea', 85.00, 75, 'activo'),

-- Hidroterapia y Circuitos (Cat 5)
(11, 5, 'Circuito Termal Valle Sereno', 'Acceso a piscina de hidromasaje, sauna húmedo y duchas sensoriales', 35.00, 60, 'activo'),
(12, 5, 'Baño Herbal de Tina Serena', 'Inmersión en agua termal enriquecida con eucalipto, lavanda y sales marinas', 45.00, 45, 'activo'),

-- Rituales & Paquetes VIP (Cat 6)
(13, 6, 'Ritual Serenity Oasis (Dúo)', 'Paquete para 2 personas: Masaje aromático, circuito termal y copa de cortesía', 150.00, 120, 'activo'),
(14, 6, 'Día de Spa Transformación Total', 'Exfoliación corporal completa, facial de lujo, masaje de 80 min y almuerzo détox', 180.00, 180, 'activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), costo=VALUES(costo), duracion_minutos=VALUES(duracion_minutos), id_categoria=VALUES(id_categoria);

-- ----------------------------------------------------------------------------
-- 3. EMPLEADOS ADICIONALES (Terapeutas y Asistentes)
-- ----------------------------------------------------------------------------
INSERT INTO empleados (id, nombre_completo, telefono, correo, cargo, especialidades, estado) VALUES
(4, 'Andrea Gómez Salcedo', '3024567890', 'andrea.gomez@vallesereno.com', 'Terapeuta Corporal', 'Masaje Sueco, Descontracturante, Piedras Calientes', 'activo'),
(5, 'David Alejandro Ruiz', '3031238901', 'david.ruiz@vallesereno.com', 'Fisioterapeuta y Quiromasajista', 'Rehabilitación muscular, Drenaje Linfático, Descarga deportiva', 'activo'),
(6, 'Lucía Fernanda Méndez', '3049988776', 'lucia.mendez@vallesereno.com', 'Cosmetóloga y Esteticista', 'Faciales de alta gama, Hidroterapia, Cuencos sonoros', 'activo'),
(7, 'Mariana Restrepo Paz', '3053322114', 'mariana.paz@vallesereno.com', 'Recepcionista Asistente', 'Atención al huésped, Agendamiento, Facturación', 'activo')
ON DUPLICATE KEY UPDATE nombre_completo=VALUES(nombre_completo), telefono=VALUES(telefono), especialidades=VALUES(especialidades);

-- ----------------------------------------------------------------------------
-- 4. USUARIOS DEL SISTEMA
-- Contraseña universal para todos: Admin123*
-- Hash bcrypt: $2y$10$vNm2aJ4rahvc58ClFyY5yOEEJnWEGDyshwm1.6KJ8OO3M8gmEcIre
-- ----------------------------------------------------------------------------
INSERT INTO usuarios (id, id_rol, id_empleado, usuario, password_hash, estado, ultimo_acceso) VALUES
(4, 3, 4, 'andrea.terapeuta', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo', '2026-09-28 16:30:00'),
(5, 3, 5, 'david.terapeuta', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo', '2026-09-29 08:15:00'),
(6, 3, 6, 'lucia.terapeuta', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo', '2026-09-29 09:00:00'),
(7, 2, 7, 'mariana.recepcion', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo', '2026-09-29 11:20:00')
ON DUPLICATE KEY UPDATE usuario=VALUES(usuario), estado=VALUES(estado);

-- ----------------------------------------------------------------------------
-- 5. DISPONIBILIDAD HORARIA DEL PERSONAL
-- Lunes a Sábado (1 = Lunes, 6 = Sábado)
-- ----------------------------------------------------------------------------
INSERT INTO disponibilidad_empleados (id_empleado, dia_semana, hora_inicio, hora_fin, estado) VALUES
-- Mateo Delgado (Terapeuta ID 3)
(3, 1, '08:00:00', '16:00:00', 'activo'),
(3, 2, '08:00:00', '16:00:00', 'activo'),
(3, 3, '08:00:00', '16:00:00', 'activo'),
(3, 4, '08:00:00', '16:00:00', 'activo'),
(3, 5, '08:00:00', '16:00:00', 'activo'),
(3, 6, '09:00:00', '14:00:00', 'activo'),

-- Andrea Gómez (Terapeuta ID 4)
(4, 1, '10:00:00', '18:00:00', 'activo'),
(4, 2, '10:00:00', '18:00:00', 'activo'),
(4, 3, '10:00:00', '18:00:00', 'activo'),
(4, 4, '10:00:00', '18:00:00', 'activo'),
(4, 5, '10:00:00', '18:00:00', 'activo'),
(4, 6, '09:00:00', '15:00:00', 'activo'),

-- David Alejandro Ruiz (Terapeuta ID 5)
(5, 2, '08:00:00', '17:00:00', 'activo'),
(5, 3, '08:00:00', '17:00:00', 'activo'),
(5, 4, '08:00:00', '17:00:00', 'activo'),
(5, 5, '08:00:00', '17:00:00', 'activo'),
(5, 6, '08:00:00', '16:00:00', 'activo'),

-- Lucía Fernanda Méndez (Cosmetóloga ID 6)
(6, 1, '09:00:00', '18:00:00', 'activo'),
(6, 2, '09:00:00', '18:00:00', 'activo'),
(6, 3, '09:00:00', '18:00:00', 'activo'),
(6, 4, '09:00:00', '18:00:00', 'activo'),
(6, 5, '09:00:00', '18:00:00', 'activo');

-- ----------------------------------------------------------------------------
-- 6. CLIENTES
-- ----------------------------------------------------------------------------
INSERT INTO clientes (id, nombre_completo, fecha_nacimiento, telefono, correo, estado) VALUES
(1, 'Elena Rostova', '1992-04-15', '3104561234', 'elena.rostova@gmail.com', 'activo'),
(2, 'Carlos Mendoza Varela', '1985-11-23', '3117894561', 'carlos.mendoza@outlook.com', 'activo'),
(3, 'Sofía Benítez Carvajal', '1996-08-30', '3128905672', 'sofia.benitez@yahoo.com', 'activo'),
(4, 'Mauricio Cárdenas Gil', '1979-02-14', '3159988112', 'mauricio.cardenas@empresa.com', 'activo'),
(5, 'Valentina Henao Ríos', '2000-06-19', '3187766554', 'valentina.henao@gmail.com', 'activo'),
(6, 'Andrés Felipe Morales', '1990-12-05', '3164433221', 'andres.morales@hotmail.com', 'activo'),
(7, 'Camila Ochoa Duque', '1994-09-11', '3171239874', 'camila.ochoa@gmail.com', 'activo'),
(8, 'Guillermo Arango Soto', '1968-03-27', '3193456781', 'guillermo.arango@gmail.com', 'activo'),
(9, 'Patricia Montoya Londoño', '1988-07-22', '3146543210', 'patricia.montoya@gmail.com', 'activo'),
(10, 'Javier Solís Bedoya', '1995-01-18', '3139876541', 'javier.solis@empresa.com', 'activo')
ON DUPLICATE KEY UPDATE nombre_completo=VALUES(nombre_completo), telefono=VALUES(telefono), correo=VALUES(correo);

-- ----------------------------------------------------------------------------
-- 7. CITAS / AGENDA
-- Incluye citas para HOY (CURRENT_DATE), pasadas y programadas para mañana
-- ----------------------------------------------------------------------------
INSERT INTO citas (id, id_cliente, id_empleado, fecha, hora_inicio, hora_fin, total, estado, observaciones) VALUES
-- Citas de Hoy (Representación viva para el Dashboard)
(1, 1, 3, CURRENT_DATE(), '09:00:00', '10:15:00', 65.00, 'Confirmada', 'Prefiere aceite de lavanda tibio. Cabina 1.'),
(2, 2, 3, CURRENT_DATE(), '10:30:00', '11:20:00', 50.00, 'Pendiente', 'Llamar 30 minutos antes para confirmar llegada.'),
(3, 3, 4, CURRENT_DATE(), '11:45:00', '12:45:00', 35.00, 'Confirmada', 'Circuito de hidroterapia relax y toallas tibias.'),
(4, 4, 5, CURRENT_DATE(), '14:00:00', '15:00:00', 60.00, 'Confirmada', 'Tensión cervical severa por jornada laboral prolongada.'),
(5, 5, 6, CURRENT_DATE(), '15:30:00', '16:45:00', 85.00, 'Pendiente', 'Cliente con piel sensible; usar mascarilla hipoalergénica.'),
(6, 6, 4, CURRENT_DATE(), '17:00:00', '18:00:00', 45.00, 'Confirmada', 'Masaje sueco clásico.'),
(7, 7, 3, CURRENT_DATE(), '08:00:00', '09:00:00', 70.00, 'Completada', 'Sesión matutina completada satisfactoriamente.'),
(8, 8, 5, CURRENT_DATE(), '18:30:00', '19:15:00', 40.00, 'Pendiente', 'Reflexología podal vespertina.'),

-- Citas de Ayer y días anteriores (Histórico completado y cancelado)
(9, 9, 3, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), '10:00:00', '11:15:00', 65.00, 'Completada', 'Piedras calientes.'),
(10, 10, 4, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), '11:30:00', '12:30:00', 45.00, 'Completada', 'Atendida a tiempo en cabina 2.'),
(11, 2, 5, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), '15:00:00', '16:00:00', 60.00, 'Cancelada', 'Canceló por inconvenientes de transporte.'),
(12, 1, 6, DATE_SUB(CURRENT_DATE(), INTERVAL 2 DAY), '09:00:00', '10:00:00', 55.00, 'Completada', 'Limpieza profunda con ozono.'),
(13, 3, 3, DATE_SUB(CURRENT_DATE(), INTERVAL 2 DAY), '16:00:00', '17:30:00', 80.00, 'Completada', 'Masaje ayurvédico.'),
(14, 5, 4, DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY), '14:00:00', '16:00:00', 150.00, 'Completada', 'Ritual dúo de fin de semana.'),

-- Citas Futuras (Próximos días en agenda)
(15, 6, 3, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), '09:30:00', '10:30:00', 45.00, 'Confirmada', 'Cita reservada con anticipación.'),
(16, 7, 6, DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY), '11:00:00', '12:15:00', 85.00, 'Pendiente', 'Tratamiento de colágeno.'),
(17, 8, 5, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY), '15:00:00', '16:00:00', 55.00, 'Pendiente', 'Drenaje linfático.')
ON DUPLICATE KEY UPDATE total=VALUES(total), estado=VALUES(estado), observaciones=VALUES(observaciones);

-- ----------------------------------------------------------------------------
-- 8. DETALLE DE CITAS (Servicios asociados)
-- ----------------------------------------------------------------------------
INSERT INTO cita_detalles (id_cita, id_servicio, precio_aplicado, duracion_aplicada) VALUES
-- Cita 1: Piedras volcánicas
(1, 2, 65.00, 75),
-- Cita 2: Aromaterapia
(2, 3, 50.00, 50),
-- Cita 3: Circuito Termal
(3, 11, 35.00, 60),
-- Cita 4: Descontracturante
(4, 4, 60.00, 60),
-- Cita 5: Velo de Colágeno
(5, 10, 85.00, 75),
-- Cita 6: Masaje Sueco
(6, 1, 45.00, 60),
-- Cita 7: Cuencos Tibetanos
(7, 7, 70.00, 60),
-- Cita 8: Reflexología
(8, 6, 40.00, 45),
-- Citas pasadas
(9, 2, 65.00, 75),
(10, 1, 45.00, 60),
(11, 4, 60.00, 60),
(12, 9, 55.00, 60),
(13, 8, 80.00, 90),
(14, 13, 150.00, 120),
-- Citas futuras
(15, 1, 45.00, 60),
(16, 10, 85.00, 75),
(17, 5, 55.00, 60);

-- ----------------------------------------------------------------------------
-- 9. PAGOS REGISTRADOS
-- Transacciones para citas de hoy y días previos
-- ----------------------------------------------------------------------------
INSERT INTO pagos (id_cita, tipo_pago, fecha_pago, monto, estado) VALUES
-- Pagos recibidos el día de hoy (Suman ingresos para el Dashboard)
(7, 'Efectivo', NOW(), 70.00, 'Pagado'),
(1, 'Tarjeta', NOW(), 65.00, 'Pagado'),
(3, 'Transferencia', NOW(), 35.00, 'Pagado'),
(4, 'Tarjeta', NOW(), 60.00, 'Pagado'),

-- Pagos de días anteriores
(9, 'Efectivo', DATE_SUB(NOW(), INTERVAL 1 DAY), 65.00, 'Pagado'),
(10, 'Tarjeta', DATE_SUB(NOW(), INTERVAL 1 DAY), 45.00, 'Pagado'),
(12, 'Transferencia', DATE_SUB(NOW(), INTERVAL 2 DAY), 55.00, 'Pagado'),
(13, 'Tarjeta', DATE_SUB(NOW(), INTERVAL 2 DAY), 80.00, 'Pagado'),
(14, 'Tarjeta', DATE_SUB(NOW(), INTERVAL 3 DAY), 150.00, 'Pagado');
