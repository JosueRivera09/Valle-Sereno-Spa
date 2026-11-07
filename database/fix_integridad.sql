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

-- 3. Login por CORREO institucional
--    Agrega la columna email a usuarios (idempotente) y la respalda con el correo del empleado.
SET @col_email := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'email'
);
SET @sql := IF(@col_email = 0,
    'ALTER TABLE usuarios ADD COLUMN email VARCHAR(100) NULL AFTER usuario',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_email := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND INDEX_NAME = 'uq_usuarios_email'
);
SET @sql := IF(@idx_email = 0,
    'ALTER TABLE usuarios ADD UNIQUE KEY uq_usuarios_email (email)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Respaldo: si la cuenta aún no tiene correo, tomar el del empleado vinculado
UPDATE usuarios u
LEFT JOIN empleados e ON u.id_empleado = e.id
SET u.email = COALESCE(NULLIF(u.email, ''), e.correo)
WHERE u.email IS NULL OR u.email = '';

-- Correos conocidos de las cuentas base (por si el respaldo no los cubrió)
UPDATE usuarios SET email = 'admin@vallesereno.com'     WHERE usuario = 'admin'     AND (email IS NULL OR email = '');
UPDATE usuarios SET email = 'recepcion@vallesereno.com' WHERE usuario = 'recepcion' AND (email IS NULL OR email = '');
UPDATE usuarios SET email = 'terapeuta@vallesereno.com' WHERE usuario = 'terapeuta' AND (email IS NULL OR email = '');
