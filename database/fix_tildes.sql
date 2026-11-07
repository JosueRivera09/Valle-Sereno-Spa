-- ============================================================================
-- REPARACIÓN DE TILDES (corrupción de codificación en la importación)
-- Los datos se habían insertado con un cliente MySQL en cp850, por lo que
-- cada acento se guardó como "??" o mojibake. Este script reescribe SOLO los
-- valores de texto afectados con su forma correcta en UTF-8.
-- Ejecutar SIEMPRE con: mysql --default-character-set=utf8mb4
-- ============================================================================
USE spa_db;

-- 1) CATEGORÍAS
UPDATE categorias SET nombre='Relajantes',  descripcion='Masajes y tratamientos para alivio de estres' WHERE id=1;
UPDATE categorias SET nombre='Terapeuticos', descripcion='Servicios enfocados en rehabilitacion y dolor muscular' WHERE id=2;
UPDATE categorias SET nombre='Holísticos',  descripcion='Terapias integrales y bienestar energetico' WHERE id=3;
UPDATE categorias SET nombre='Faciales y Estética', descripcion='Tratamientos de hidratación, antienvejecimiento y limpieza facial profunda' WHERE id=4;
UPDATE categorias SET nombre='Hidroterapia y Circuitos', descripcion='Saunas, jacuzzis termales y baños de vapor revitalizantes' WHERE id=5;
UPDATE categorias SET nombre='Rituales & Paquetes VIP', descripcion='Paquetes de día completo con combinaciones de tratamientos exclusivos' WHERE id=6;

-- 2) SERVICIOS
UPDATE servicios SET nombre='Masaje Sueco Relajante', descripcion='Alivio de tensiones mediante maniobras suaves con aceites naturales' WHERE id=1;
UPDATE servicios SET nombre='Piedras Volcánicas Calientes', descripcion='Terapia geotermal para calmar dolores y armonizar centros energéticos' WHERE id=2;
UPDATE servicios SET nombre='Aromaterapia & Lavanda Real', descripcion='Sesión relajante con esencias botánicas puras y difusión aromática' WHERE id=3;
UPDATE servicios SET nombre='Masaje Descontracturante Profundo', descripcion='Técnica focalizada en contracturas crónicas de espalda, cuello y hombros' WHERE id=4;
UPDATE servicios SET nombre='Drenaje Linfático Manual', descripcion='Estimula el sistema circulatorio y la eliminación natural de toxinas' WHERE id=5;
UPDATE servicios SET nombre='Reflexología Podal Terapéutica', descripcion='Estimulación de puntos reflejos en pies para aliviar dolencias corporales' WHERE id=6;
UPDATE servicios SET nombre='Terapia de Cuencos Tibetanos', descripcion='Sanación vibracional y sonora para equilibrar mente y espíritu' WHERE id=7;
UPDATE servicios SET nombre='Masaje Ayurvédico Abhyanga', descripcion='Masaje hindú con aceites tibios formulados según cada constitución física' WHERE id=8;
UPDATE servicios SET nombre='Limpieza Facial Profunda con Ozono', descripcion='Exfoliación, vapor de ozono, extracción y mascarilla purificante botánica' WHERE id=9;
UPDATE servicios SET nombre='Velo de Colágeno & Oro 24K', descripcion='Regeneración celular, hidratación intensa y luminosidad instantánea' WHERE id=10;
UPDATE servicios SET nombre='Circuito Termal Valle Sereno', descripcion='Acceso a piscina de hidromasaje, sauna húmedo y duchas sensoriales' WHERE id=11;
UPDATE servicios SET nombre='Baño Herbal de Tina Serena', descripcion='Inmersión en agua termal enriquecida con eucalipto, lavanda y sales marinas' WHERE id=12;
UPDATE servicios SET nombre='Ritual Serenity Oasis (Dúo)', descripcion='Paquete para 2 personas: Masaje aromático, circuito termal y copa de cortesía' WHERE id=13;
UPDATE servicios SET nombre='Día de Spa Transformación Total', descripcion='Exfoliación corporal completa, facial de lujo, masaje de 80 min y almuerzo détox' WHERE id=14;

-- 3) EMPLEADOS
UPDATE empleados SET nombre_completo='Valeria Sereno', cargo='Directora General', especialidades='Gestion y Direccion' WHERE id=1;
UPDATE empleados SET nombre_completo='Camila Morales', cargo='Jefa de Recepcion', especialidades='Atencion al cliente y reservas' WHERE id=2;
UPDATE empleados SET nombre_completo='Mateo Delgado', cargo='Terapeuta Principal', especialidades='Masajes Holísticos, Aromaterapia' WHERE id=3;
UPDATE empleados SET nombre_completo='Andrea Gómez Salcedo', cargo='Terapeuta Corporal', especialidades='Masaje Sueco, Descontracturante, Piedras Calientes' WHERE id=4;
UPDATE empleados SET nombre_completo='David Alejandro Ruiz', cargo='Fisioterapeuta y Quiromasajista', especialidades='Rehabilitación muscular, Drenaje Linfático, Descarga deportiva' WHERE id=5;
UPDATE empleados SET nombre_completo='Lucía Fernanda Méndez', cargo='Cosmetóloga y Esteticista', especialidades='Faciales de alta gama, Hidroterapia, Cuencos sonoros' WHERE id=6;
UPDATE empleados SET nombre_completo='Mariana Restrepo Paz', cargo='Recepcionista Asistente', especialidades='Atención al huésped, Agendamiento, Facturación' WHERE id=7;

-- 4) CLIENTES
UPDATE clientes SET nombre_completo='Elena Rostova' WHERE id=1;
UPDATE clientes SET nombre_completo='Carlos Mendoza Varela' WHERE id=2;
UPDATE clientes SET nombre_completo='Sofía Benítez Carvajal' WHERE id=3;
UPDATE clientes SET nombre_completo='Mauricio Cárdenas Gil' WHERE id=4;
UPDATE clientes SET nombre_completo='Valentina Henao Ríos' WHERE id=5;
UPDATE clientes SET nombre_completo='Andrés Felipe Morales' WHERE id=6;
UPDATE clientes SET nombre_completo='Camila Ochoa Duque' WHERE id=7;
UPDATE clientes SET nombre_completo='Guillermo Arango Soto' WHERE id=8;
UPDATE clientes SET nombre_completo='Patricia Montoya Londoño' WHERE id=9;
UPDATE clientes SET nombre_completo='Javier Solís Bedoya' WHERE id=10;

-- 5) CITAS (observaciones)
UPDATE citas SET observaciones='Prefiere aceite de lavanda tibio. Cabina 1.' WHERE id=1;
UPDATE citas SET observaciones='Llamar 30 minutos antes para confirmar llegada.' WHERE id=2;
UPDATE citas SET observaciones='Circuito de hidroterapia relax y toallas tibias.' WHERE id=3;
UPDATE citas SET observaciones='Tensión cervical severa por jornada laboral prolongada.' WHERE id=4;
UPDATE citas SET observaciones='Cliente con piel sensible; usar mascarilla hipoalergénica.' WHERE id=5;
UPDATE citas SET observaciones='Masaje sueco clásico.' WHERE id=6;
UPDATE citas SET observaciones='Sesión matutina completada satisfactoriamente.' WHERE id=7;
UPDATE citas SET observaciones='Reflexología podal vespertina.' WHERE id=8;
UPDATE citas SET observaciones='Piedras calientes.' WHERE id=9;
UPDATE citas SET observaciones='Atendida a tiempo en cabina 2.' WHERE id=10;
UPDATE citas SET observaciones='Canceló por inconvenientes de transporte.' WHERE id=11;
UPDATE citas SET observaciones='Limpieza profunda con ozono.' WHERE id=12;
UPDATE citas SET observaciones='Masaje ayurvédico.' WHERE id=13;
UPDATE citas SET observaciones='Ritual dúo de fin de semana.' WHERE id=14;
UPDATE citas SET observaciones='Cita reservada con anticipación.' WHERE id=15;
UPDATE citas SET observaciones='Tratamiento de colágeno.' WHERE id=16;
UPDATE citas SET observaciones='Drenaje linfático.' WHERE id=17;
