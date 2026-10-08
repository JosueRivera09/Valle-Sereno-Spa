CREATE DATABASE IF NOT EXISTS spa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spa_db;

-- 1. Roles de usuario
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL
) ENGINE=InnoDB;

-- 2. Empleados (Terapeutas, Recepción, etc.)
CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(120) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    cargo VARCHAR(50) NOT NULL,
    especialidades TEXT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB;

-- 3. Usuarios del sistema
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    id_empleado INT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo', 'bloqueado') NOT NULL DEFAULT 'activo',
    intentos_fallidos INT NOT NULL DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME NULL,
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (id_rol) 
        REFERENCES roles(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_usuarios_empleados FOREIGN KEY (id_empleado) 
        REFERENCES empleados(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Disponibilidad horaria del personal
CREATE TABLE IF NOT EXISTS disponibilidad_empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '1=Lunes, 7=Domingo',
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    CONSTRAINT fk_disp_empleados FOREIGN KEY (id_empleado) 
        REFERENCES empleados(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_disp_horario CHECK (hora_fin > hora_inicio),
    CONSTRAINT chk_disp_dia CHECK (dia_semana BETWEEN 1 AND 7)
) ENGINE=InnoDB;

-- 5. Clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(120) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_clientes_telefono (telefono),
    INDEX idx_clientes_correo (correo)
) ENGINE=InnoDB;

-- 6. Categorías de servicios
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB;

-- 7. Catálogo de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT NULL,
    costo DECIMAL(10,2) NOT NULL,
    duracion_minutos INT NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    CONSTRAINT fk_servicios_categorias FOREIGN KEY (id_categoria) 
        REFERENCES categorias(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_servicios_costo CHECK (costo >= 0),
    CONSTRAINT chk_servicios_duracion CHECK (duracion_minutos > 0)
) ENGINE=InnoDB;

-- 8. Citas / Agenda
CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_empleado INT NOT NULL COMMENT 'Terapeuta asignado',
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('Pendiente', 'Confirmada', 'Completada', 'Cancelada') NOT NULL DEFAULT 'Pendiente',
    observaciones TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_clientes FOREIGN KEY (id_cliente) 
        REFERENCES clientes(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_citas_empleados FOREIGN KEY (id_empleado) 
        REFERENCES empleados(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_citas_horario CHECK (hora_fin > hora_inicio),
    INDEX idx_citas_terapeuta_fecha (id_empleado, fecha),
    INDEX idx_citas_cliente_fecha (id_cliente, fecha)
) ENGINE=InnoDB;

-- 9. Detalle de cita (servicios contratados)
CREATE TABLE IF NOT EXISTS cita_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cita INT NOT NULL,
    id_servicio INT NOT NULL,
    precio_aplicado DECIMAL(10,2) NOT NULL,
    duracion_aplicada INT NOT NULL,
    CONSTRAINT fk_detalles_citas FOREIGN KEY (id_cita) 
        REFERENCES citas(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_detalles_servicios FOREIGN KEY (id_servicio) 
        REFERENCES servicios(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_detalles_precio CHECK (precio_aplicado >= 0),
    CONSTRAINT chk_detalles_duracion CHECK (duracion_aplicada > 0)
) ENGINE=InnoDB;

-- 10. Pagos
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cita INT NOT NULL,
    tipo_pago ENUM('Efectivo', 'Tarjeta', 'Transferencia') NOT NULL,
    fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('Pagado', 'Pendiente') NOT NULL DEFAULT 'Pagado',
    CONSTRAINT fk_pagos_citas FOREIGN KEY (id_cita) 
        REFERENCES citas(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_pagos_monto CHECK (monto > 0),
    INDEX idx_pagos_cita_fecha (id_cita, fecha_pago)
) ENGINE=InnoDB;

-- Datos iniciales
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Configuracion y control general'),
('Recepcionista', 'Atencion diaria, clientes, citas y cobros'),
('Terapeuta', 'Consulta de citas asignadas y atencion de servicios');

INSERT INTO categorias (nombre, descripcion) VALUES
('Relajantes', 'Masajes y tratamientos para alivio de estres'),
('Terapeuticos', 'Servicios enfocados en rehabilitacion y dolor muscular'),
('Holísticos', 'Terapias integrales y bienestar energetico');

-- Inserción de Empleados iniciales
INSERT INTO empleados (id, nombre_completo, telefono, correo, cargo, especialidades, estado) VALUES
(1, 'Valeria Sereno', '3001234567', 'admin@vallesereno.com', 'Directora General', 'Gestion y Direccion', 'activo'),
(2, 'Camila Morales', '3009876543', 'recepcion@vallesereno.com', 'Jefa de Recepcion', 'Atencion al cliente y reservas', 'activo'),
(3, 'Mateo Delgado', '3015554321', 'terapeuta@vallesereno.com', 'Terapeuta Principal', 'Masajes Holísticos, Aromaterapia', 'activo');

-- Inserción de Usuarios iniciales (Contraseña universal: Admin123*)
-- Hash generado con password_hash('Admin123*', PASSWORD_BCRYPT)
INSERT INTO usuarios (id, id_rol, id_empleado, usuario, password_hash, estado) VALUES
(1, 1, 1, 'admin', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo'),
(2, 2, 2, 'recepcion', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo'),
(3, 3, 3, 'terapeuta', '$2y$10$jLdRv72L47xAYCBwUcWyG.n9ObppOXYW8EZ/adtL7RWyBdOtgLjTu', 'activo');

/*
================================================================================
CREDENCIALES DE ACCESO AL SISTEMA - SPA VALLE SERENO
Contraseña universal para todas las cuentas de prueba: Admin123*
================================================================================
1. ADMINISTRADOR
   - Usuario: admin
   - Correo: admin@vallesereno.com
   - Contraseña: Admin123*
   - Rol: Administrador (Control total, reportes, usuarios)

2. RECEPCIONISTA
   - Usuario: recepcion
   - Correo: recepcion@vallesereno.com
   - Contraseña: Admin123*
   - Rol: Recepcionista (Citas, clientes, agendamiento, caja)

3. TERAPEUTA
   - Usuario: terapeuta
   - Correo: terapeuta@vallesereno.com
   - Contraseña: Admin123*
   - Rol: Terapeuta (Agenda asignada, terapias, atención)
================================================================================
*/