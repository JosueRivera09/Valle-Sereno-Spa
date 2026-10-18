-- ================================================================================
-- VALLE SERENO SPA - PARCHE DE INTEGRIDAD PARA BD EXISTENTE (spa_db)
-- Ejecutar cuando la base de datos ya fue creada antes de la corrección de hashes.
--   c:\xampp\mysql\bin\mysql.exe -u root spa_db < database\fix_integridad.sql
-- ================================================================================
USE spa_db;

-- 1. Restablecer todas las cuentas a la contraseña documentada Admin123*
--    (hash bcrypt generado con password_hash('Admin123*', PASSWORD_BCRYPT) y
--     verificado con password_verify)
UPDATE usuarios
SET password_hash = '$2y$10$EpT5h7RfH.292Xbws33taOKxGe8/jAN9.OSP.FbvfsikNWhU4PyUG',
    estado = 'activo',
    intentos_fallidos = 0,
    bloqueado_hasta = NULL;

-- 2. Normalizar el estado de las citas al valor canónico del ENUM ('Pendiente')
--    (citas creadas por versiones previas que insertaban 'pendiente' en minúscula)
UPDATE citas SET estado = 'Pendiente' WHERE estado = 'pendiente';
