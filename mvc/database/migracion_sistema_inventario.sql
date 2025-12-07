-- =====================================================
-- Script de Migración: Sistema de Gestión de Inventario
-- Cámaras, Sensores y Componentes Electrónicos
-- Base de Datos: inventario
-- Fecha: 2025-11-22
-- Basado en: contexto.txt
-- =====================================================
-- IMPORTANTE: Este script crea la base de datos desde cero
-- Si la base de datos ya existe, será eliminada y recreada
-- =====================================================

-- Eliminar la base de datos si existe (CUIDADO: esto borra todos los datos)
DROP DATABASE IF EXISTS inventario;

-- Crear la base de datos
CREATE DATABASE inventario CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos
USE inventario;

-- =====================================================
-- Tabla: usuario
-- Descripción: Usuarios del sistema con roles
-- =====================================================
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('ADMIN', 'ALMACEN', 'LECTOR') NOT NULL DEFAULT 'LECTOR',
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: marca
-- Descripción: Marcas de equipos
-- =====================================================
CREATE TABLE IF NOT EXISTS marca (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: categoria
-- Descripción: Categorías de equipos
-- =====================================================
CREATE TABLE IF NOT EXISTS categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: ubicacion
-- Descripción: Ubicaciones físicas en el almacén
-- =====================================================
CREATE TABLE IF NOT EXISTS ubicacion (
    id_ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: equipo
-- Descripción: Catálogo maestro de equipos
-- =====================================================
CREATE TABLE IF NOT EXISTS equipo (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    tipo ENUM('CAMARA', 'SENSOR') NOT NULL,
    descripcion TEXT,
    id_marca INT,
    id_categoria INT,
    INDEX idx_sku (sku),
    INDEX idx_tipo (tipo),
    INDEX idx_marca (id_marca),
    INDEX idx_categoria (id_categoria),
    FOREIGN KEY (id_marca) REFERENCES marca(id_marca) ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: inventario_lote
-- Descripción: Lotes de inventario
-- =====================================================
CREATE TABLE IF NOT EXISTS inventario_lote (
    id_lote INT AUTO_INCREMENT PRIMARY KEY,
    cantidad INT NOT NULL DEFAULT 0,
    estado ENUM('DISPONIBLE', 'RESERVADO', 'INSTALADO', 'DAÑADO') NOT NULL DEFAULT 'DISPONIBLE',
    fecha_ingreso DATE NOT NULL,
    id_equipo INT NOT NULL,
    id_ubicacion INT NOT NULL,
    INDEX idx_equipo (id_equipo),
    INDEX idx_ubicacion (id_ubicacion),
    INDEX idx_estado (estado),
    INDEX idx_fecha_ingreso (fecha_ingreso),
    FOREIGN KEY (id_equipo) REFERENCES equipo(id_equipo) ON DELETE CASCADE,
    FOREIGN KEY (id_ubicacion) REFERENCES ubicacion(id_ubicacion) ON DELETE RESTRICT,
    CHECK (cantidad >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: movimiento_inventario
-- Descripción: Registro de movimientos
-- =====================================================
CREATE TABLE IF NOT EXISTS movimiento_inventario (
    id_mov INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('ENTRADA', 'SALIDA', 'AJUSTE') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(200),
    id_lote INT NOT NULL,
    id_usuario INT,
    INDEX idx_fecha_hora (fecha_hora),
    INDEX idx_tipo (tipo),
    INDEX idx_lote (id_lote),
    INDEX idx_usuario (id_usuario),
    FOREIGN KEY (id_lote) REFERENCES inventario_lote(id_lote) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL,
    CHECK (cantidad > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: proveedor
-- Descripción: Proveedores
-- =====================================================
CREATE TABLE IF NOT EXISTS proveedor (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: compra
-- Descripción: Órdenes de compra
-- =====================================================
CREATE TABLE IF NOT EXISTS compra (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    id_proveedor INT NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado ENUM('PENDIENTE', 'CONFIRMADA', 'CANCELADA') NOT NULL DEFAULT 'PENDIENTE',
    INDEX idx_fecha (fecha),
    INDEX idx_proveedor (id_proveedor),
    INDEX idx_estado (estado),
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: compra_detalle
-- Descripción: Detalle de compras
-- =====================================================
CREATE TABLE IF NOT EXISTS compra_detalle (
    id_compra INT NOT NULL,
    id_equipo INT NOT NULL,
    cantidad INT NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_compra, id_equipo),
    INDEX idx_equipo (id_equipo),
    FOREIGN KEY (id_compra) REFERENCES compra(id_compra) ON DELETE CASCADE,
    FOREIGN KEY (id_equipo) REFERENCES equipo(id_equipo) ON DELETE RESTRICT,
    CHECK (cantidad > 0),
    CHECK (costo_unitario >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: cliente
-- Descripción: Clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: orden_instalacion
-- Descripción: Órdenes de instalación
-- =====================================================
CREATE TABLE IF NOT EXISTS orden_instalacion (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha_programada DATE NOT NULL,
    estado ENUM('PENDIENTE', 'EN_PROCESO', 'COMPLETADA', 'CANCELADA') NOT NULL DEFAULT 'PENDIENTE',
    id_usuario INT,
    INDEX idx_cliente (id_cliente),
    INDEX idx_fecha_programada (fecha_programada),
    INDEX idx_estado (estado),
    INDEX idx_usuario (id_usuario),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE RESTRICT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: orden_instalacion_detalle
-- Descripción: Detalle de órdenes
-- =====================================================
CREATE TABLE IF NOT EXISTS orden_instalacion_detalle (
    id_orden INT NOT NULL,
    id_lote INT NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (id_orden, id_lote),
    INDEX idx_lote (id_lote),
    FOREIGN KEY (id_orden) REFERENCES orden_instalacion(id_orden) ON DELETE CASCADE,
    FOREIGN KEY (id_lote) REFERENCES inventario_lote(id_lote) ON DELETE RESTRICT,
    CHECK (cantidad > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Vista: vista_inventario_completo
-- =====================================================
CREATE OR REPLACE VIEW vista_inventario_completo AS
SELECT 
    il.id_lote,
    e.id_equipo,
    e.sku,
    e.tipo,
    e.descripcion AS equipo_descripcion,
    m.nombre AS marca_nombre,
    c.nombre AS categoria_nombre,
    u.nombre AS ubicacion_nombre,
    il.cantidad,
    il.estado,
    il.fecha_ingreso,
    CASE 
        WHEN il.estado = 'DISPONIBLE' AND il.cantidad > 0 THEN 'DISPONIBLE'
        WHEN il.estado = 'RESERVADO' THEN 'RESERVADO'
        WHEN il.estado = 'INSTALADO' THEN 'INSTALADO'
        WHEN il.estado = 'DAÑADO' THEN 'DAÑADO'
        ELSE 'SIN_STOCK'
    END AS estado_visual
FROM inventario_lote il
INNER JOIN equipo e ON il.id_equipo = e.id_equipo
LEFT JOIN marca m ON e.id_marca = m.id_marca
LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
INNER JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion;

-- =====================================================
-- Vista: vista_movimientos_completo
-- =====================================================
CREATE OR REPLACE VIEW vista_movimientos_completo AS
SELECT 
    mi.id_mov,
    mi.fecha_hora,
    mi.tipo,
    mi.cantidad,
    mi.motivo,
    il.id_lote,
    e.sku,
    e.tipo AS equipo_tipo,
    e.descripcion AS equipo_descripcion,
    u.nombre AS ubicacion_nombre,
    usr.nombre AS usuario_nombre,
    usr.email AS usuario_email
FROM movimiento_inventario mi
INNER JOIN inventario_lote il ON mi.id_lote = il.id_lote
INNER JOIN equipo e ON il.id_equipo = e.id_equipo
INNER JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
LEFT JOIN usuario usr ON mi.id_usuario = usr.id_usuario
ORDER BY mi.fecha_hora DESC;

-- =====================================================
-- Datos de Ejemplo: Usuarios
-- Contraseña: admin123
-- =====================================================
INSERT INTO usuario (nombre, email, password, rol) VALUES
('Administrador del Sistema', 'admin@inventario.com', '$2y$10$DlIGdhz4JPlLqGoGFwyfrOIXdGx1M3eZ6fPTJZRY8sUWQVOnbe9rW', 'ADMIN'),
('Operador de Almacén', 'almacen@inventario.com', '$2y$10$DlIGdhz4JPlLqGoGFwyfrOIXdGx1M3eZ6fPTJZRY8sUWQVOnbe9rW', 'ALMACEN'),
('Usuario Consultor', 'lector@inventario.com', '$2y$10$DlIGdhz4JPlLqGoGFwyfrOIXdGx1M3eZ6fPTJZRY8sUWQVOnbe9rW', 'LECTOR');

-- =====================================================
-- Datos de Ejemplo: Marcas
-- =====================================================
INSERT INTO marca (nombre) VALUES
('Hikvision'),
('Dahua'),
('Axis Communications'),
('Bosch'),
('Honeywell');

-- =====================================================
-- Datos de Ejemplo: Categorías
-- =====================================================
INSERT INTO categoria (nombre) VALUES
('Cámaras IP'),
('Cámaras Análogas'),
('Sensores de Movimiento'),
('Sensores de Apertura'),
('Accesorios');

-- =====================================================
-- Datos de Ejemplo: Ubicaciones
-- =====================================================
INSERT INTO ubicacion (nombre) VALUES
('Estante A-01'),
('Estante A-02'),
('Estante B-01'),
('Estante B-02'),
('Almacén Principal');

-- =====================================================
-- Datos de Ejemplo: Equipos
-- =====================================================
INSERT INTO equipo (sku, tipo, descripcion, id_marca, id_categoria) VALUES
('CAM-HIK-001', 'CAMARA', 'Cámara IP Hikvision 4MP Domo Interior', 1, 1),
('CAM-DAH-002', 'CAMARA', 'Cámara IP Dahua 2MP Bullet Exterior', 2, 1),
('CAM-AXIS-003', 'CAMARA', 'Cámara IP Axis PTZ 5MP', 3, 1),
('SENS-HON-001', 'SENSOR', 'Sensor de Movimiento PIR Honeywell', 5, 3),
('SENS-BOSCH-002', 'SENSOR', 'Sensor Magnético de Apertura Bosch', 4, 4);

-- =====================================================
-- Datos de Ejemplo: Proveedores
-- =====================================================
INSERT INTO proveedor (nombre, contacto, telefono, email, direccion) VALUES
('Distribuidora TechSecurity', 'Juan Pérez', '555-1234', 'ventas@techsecurity.com', 'Av. Tecnología 123'),
('Importadora ElectroSeguridad', 'María González', '555-5678', 'contacto@electroseguridad.com', 'Calle Comercio 456'),
('Proveedor Global CCTV', 'Carlos Ramírez', '555-9012', 'info@globalcctv.com', 'Zona Industrial 789');

-- =====================================================
-- Datos de Ejemplo: Clientes
-- =====================================================
INSERT INTO cliente (nombre, telefono, email, direccion) VALUES
('Empresa ABC S.A.', '555-1111', 'contacto@empresaabc.com', 'Av. Principal 100'),
('Comercial XYZ', '555-2222', 'ventas@comercialxyz.com', 'Calle Secundaria 200'),
('Industrias DEF', '555-3333', 'info@industriasdef.com', 'Zona Industrial 300');

-- =====================================================
-- Datos de Ejemplo: Lotes
-- =====================================================
INSERT INTO inventario_lote (cantidad, estado, fecha_ingreso, id_equipo, id_ubicacion) VALUES
(25, 'DISPONIBLE', '2025-11-01', 1, 1),
(15, 'DISPONIBLE', '2025-11-05', 2, 1),
(8, 'DISPONIBLE', '2025-11-10', 3, 2),
(50, 'DISPONIBLE', '2025-11-12', 4, 3),
(40, 'DISPONIBLE', '2025-11-15', 5, 3);

-- =====================================================
-- Datos de Ejemplo: Movimientos
-- =====================================================
INSERT INTO movimiento_inventario (tipo, cantidad, motivo, id_lote, id_usuario) VALUES
('ENTRADA', 25, 'Compra inicial de inventario', 1, 1),
('ENTRADA', 15, 'Compra inicial de inventario', 2, 1),
('ENTRADA', 8, 'Compra inicial de inventario', 3, 1),
('ENTRADA', 50, 'Compra inicial de inventario', 4, 2),
('ENTRADA', 40, 'Compra inicial de inventario', 5, 2);

-- =====================================================
-- Datos de Ejemplo: Compras
-- =====================================================
INSERT INTO compra (fecha, id_proveedor, total, estado) VALUES
('2025-11-01', 1, 45000.00, 'CONFIRMADA'),
('2025-11-10', 2, 32000.00, 'CONFIRMADA');

-- =====================================================
-- Datos de Ejemplo: Detalle de Compras
-- =====================================================
INSERT INTO compra_detalle (id_compra, id_equipo, cantidad, costo_unitario) VALUES
(1, 1, 25, 1200.00),
(1, 2, 15, 1000.00),
(2, 3, 8, 4000.00);

-- =====================================================
-- Fin del Script
-- =====================================================
