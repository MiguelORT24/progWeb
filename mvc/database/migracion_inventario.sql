-- =====================================================
-- Script de Migración: Sistema de Gestión de Inventarios
-- Base de Datos: inventario
-- Fecha: 2025-11-22
-- =====================================================

DROP DATABASE IF EXISTS inventario;
CREATE DATABASE inventario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventario;

-- =====================================================
-- Tabla: marca
-- =====================================================
CREATE TABLE marca (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabla: categoria
-- =====================================================
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabla: equipo
-- =====================================================
CREATE TABLE equipo (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    tipo VARCHAR(100),
    descripcion TEXT,
    id_marca INT,
    id_categoria INT,
    FOREIGN KEY (id_marca) REFERENCES marca(id_marca) ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabla: ubicacion
-- =====================================================
CREATE TABLE ubicacion (
    id_ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabla: inventario_lote
-- =====================================================
CREATE TABLE inventario_lote (
    id_lote INT AUTO_INCREMENT PRIMARY KEY,
    cantidad INT NOT NULL,
    estado VARCHAR(50),
    fecha_ingreso DATE,
    id_equipo INT,
    id_ubicacion INT,
    FOREIGN KEY (id_equipo) REFERENCES equipo(id_equipo) ON DELETE CASCADE,
    FOREIGN KEY (id_ubicacion) REFERENCES ubicacion(id_ubicacion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabla: usuario
-- =====================================================
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabla: movimiento_inventario
-- =====================================================
CREATE TABLE movimiento_inventario (
    id_mov INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL,
    tipo ENUM('entrada', 'salida', 'edicion') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(200),
    id_lote INT,
    id_usuario INT,
    FOREIGN KEY (id_lote) REFERENCES inventario_lote(id_lote) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Datos de Ejemplo: Marca
-- =====================================================
INSERT INTO marca (nombre) VALUES
('Hikvision'),
('Dahua'),
('Bosch'),
('Axis'),
('Honeywell');

-- =====================================================
-- Datos de Ejemplo: Categoría
-- =====================================================
INSERT INTO categoria (nombre) VALUES
('Cámaras de Seguridad'),
('Sensores de Movimiento'),
('Sensores Ambientales'),
('Control de Acceso'),
('Redes y Comunicación');

-- =====================================================
-- Datos de Ejemplo: Equipo
-- =====================================================
INSERT INTO equipo (sku, tipo, descripcion, id_marca, id_categoria) VALUES
('CAM001', 'Cámara IP', 'Cámara IP Hikvision 4MP con visión nocturna', 1, 1),
('CAM002', 'Cámara PTZ', 'Cámara PTZ Dahua con zoom óptico 30x', 2, 1),
('SEN001', 'Sensor PIR', 'Sensor de movimiento Bosch PIR para interiores', 3, 2),
('SEN002', 'Sensor Ambiental', 'Sensor Axis de temperatura y humedad', 4, 3),
('ACC001', 'Control Biométrico', 'Terminal Honeywell de control de acceso biométrico', 5, 4);

-- =====================================================
-- Datos de Ejemplo: Ubicación
-- =====================================================
INSERT INTO ubicacion (nombre) VALUES
('Almacén Central'),
('Sucursal Norte'),
('Sucursal Sur'),
('Sucursal Este'),
('Sucursal Oeste');

-- =====================================================
-- Datos de Ejemplo: Inventario Lote
-- =====================================================
INSERT INTO inventario_lote (cantidad, estado, fecha_ingreso, id_equipo, id_ubicacion) VALUES
(20, 'DISPONIBLE', '2025-11-01', 1, 1),
(10, 'DISPONIBLE', '2025-11-05', 2, 2),
(50, 'DISPONIBLE', '2025-11-10', 3, 3),
(15, 'DISPONIBLE', '2025-11-15', 4, 4),
(5,  'RESERVADO',  '2025-11-20', 5, 5);

-- =====================================================
-- Datos de Ejemplo: Usuario
-- =====================================================
-- Contraseña 'Admin123!' hasheada con password_hash()
INSERT INTO usuario (nombre, email, contrasena) VALUES
('Administrador', 'admin@inventario.com', '$2y$12$i50oEEKxCI0DP4kM9j8zc.qWJB1u5sv5wnO0Q4pNn0UDIa3dbMSTO');

-- =====================================================
-- Datos de Ejemplo: Movimiento Inventario
-- =====================================================
INSERT INTO movimiento_inventario (fecha_hora, tipo, cantidad, motivo, id_lote, id_usuario) VALUES
('2025-11-22 09:00:00', 'entrada', 5, 'Compra inicial de cámaras', 1, 1),
('2025-11-22 10:30:00', 'salida', 2, 'Instalación en sucursal norte', 2, 1),
('2025-11-23 14:15:00', 'entrada', 20, 'Reposición de sensores PIR', 3, 1),
('2025-11-24 11:00:00', 'salida', 1, 'Prueba de sensor ambiental', 4, 1);
