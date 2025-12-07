-- =====================================================
-- Script de Migración: Sistema de Gestión de Inventarios
-- Base de Datos: inventario
-- Fecha: 2025-11-22
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
-- Tabla: usuarios
-- Descripción: Usuarios del sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_nombre VARCHAR(100) NOT NULL,
    usuario_email VARCHAR(100) NOT NULL UNIQUE,
    usuario_password VARCHAR(255) NOT NULL,
    usuario_nivel INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_email (usuario_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: categorias
-- Descripción: Categorías de productos
-- =====================================================
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_nombre VARCHAR(100) NOT NULL,
    categoria_descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_categoria_nombre (categoria_nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: proveedores
-- Descripción: Proveedores de productos
-- =====================================================
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_nombre VARCHAR(150) NOT NULL,
    proveedor_contacto VARCHAR(100),
    proveedor_telefono VARCHAR(20),
    proveedor_email VARCHAR(100),
    proveedor_direccion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_proveedor_nombre (proveedor_nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: productos
-- Descripción: Productos del inventario
-- =====================================================
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_codigo VARCHAR(50) NOT NULL UNIQUE,
    producto_nombre VARCHAR(150) NOT NULL,
    producto_descripcion TEXT,
    producto_precio_compra DECIMAL(10,2) NOT NULL,
    producto_precio_venta DECIMAL(10,2) NOT NULL,
    producto_stock INT DEFAULT 0,
    producto_stock_minimo INT DEFAULT 0,
    producto_foto LONGBLOB,
    categoria_id INT,
    proveedor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_producto_codigo (producto_codigo),
    INDEX idx_producto_nombre (producto_nombre),
    INDEX idx_categoria (categoria_id),
    INDEX idx_proveedor (proveedor_id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabla: movimientos
-- Descripción: Movimientos de inventario (entradas/salidas)
-- =====================================================
CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    movimiento_tipo ENUM('entrada', 'salida') NOT NULL,
    movimiento_cantidad INT NOT NULL,
    movimiento_motivo VARCHAR(200),
    movimiento_precio_unitario DECIMAL(10,2),
    usuario_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_producto (producto_id),
    INDEX idx_tipo (movimiento_tipo),
    INDEX idx_fecha (created_at),
    INDEX idx_usuario (usuario_id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Datos de Ejemplo: Usuarios
-- =====================================================
-- NOTA: La contraseña 'admin123' debe ser hasheada con password_hash() de PHP
-- Por ahora, ejecuta este UPDATE después de crear el usuario desde la interfaz
-- o usa el siguiente hash que corresponde a 'admin123':
-- Hash generado con: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO usuarios (usuario_nombre, usuario_email, usuario_password, usuario_nivel) VALUES
('Administrador', 'admin@inventario.com', '$2y$10$e0MYzXyjpJS7Pd2ALwlOPeRN.FdwXQXdxQzXgXpFJXi.XqKJQqKqW', 1);

-- =====================================================
-- Datos de Ejemplo: Categorías
-- =====================================================
INSERT INTO categorias (categoria_nombre, categoria_descripcion) VALUES
('Electrónica', 'Productos electrónicos y tecnológicos'),
('Alimentos', 'Productos alimenticios y bebidas'),
('Ropa', 'Prendas de vestir y accesorios'),
('Hogar', 'Artículos para el hogar y decoración'),
('Oficina', 'Material de oficina y papelería');

-- =====================================================
-- Datos de Ejemplo: Proveedores
-- =====================================================
INSERT INTO proveedores (proveedor_nombre, proveedor_contacto, proveedor_telefono, proveedor_email, proveedor_direccion) VALUES
('TechSupply S.A.', 'Juan Pérez', '555-1234', 'contacto@techsupply.com', 'Av. Tecnología 123, Ciudad'),
('AlimCorp', 'María González', '555-5678', 'ventas@alimcorp.com', 'Calle Comercio 456, Ciudad'),
('Textiles del Norte', 'Carlos Ramírez', '555-9012', 'info@textilesnorte.com', 'Zona Industrial 789, Ciudad');

-- =====================================================
-- Datos de Ejemplo: Productos
-- =====================================================
INSERT INTO productos (producto_codigo, producto_nombre, producto_descripcion, producto_precio_compra, producto_precio_venta, producto_stock, producto_stock_minimo, categoria_id, proveedor_id) VALUES
('ELEC001', 'Laptop HP 15"', 'Laptop HP 15 pulgadas, 8GB RAM, 256GB SSD', 8500.00, 12000.00, 15, 5, 1, 1),
('ELEC002', 'Mouse Inalámbrico', 'Mouse inalámbrico ergonómico', 150.00, 250.00, 50, 10, 1, 1),
('ALIM001', 'Café Premium 500g', 'Café molido premium 500 gramos', 80.00, 150.00, 100, 20, 2, 2),
('ROPA001', 'Camisa Formal Blanca', 'Camisa formal de vestir color blanco', 200.00, 400.00, 30, 10, 3, 3),
('OFIC001', 'Resma Papel A4', 'Resma de papel bond A4 500 hojas', 60.00, 100.00, 200, 50, 5, 1);

-- =====================================================
-- Vista: Productos con Información Completa
-- =====================================================
CREATE OR REPLACE VIEW vista_productos_completa AS
SELECT 
    p.id,
    p.producto_codigo,
    p.producto_nombre,
    p.producto_descripcion,
    p.producto_precio_compra,
    p.producto_precio_venta,
    p.producto_stock,
    p.producto_stock_minimo,
    p.producto_foto,
    c.categoria_nombre,
    pr.proveedor_nombre,
    pr.proveedor_contacto,
    p.created_at,
    p.updated_at,
    CASE 
        WHEN p.producto_stock <= p.producto_stock_minimo THEN 'BAJO'
        WHEN p.producto_stock <= (p.producto_stock_minimo * 1.5) THEN 'MEDIO'
        ELSE 'NORMAL'
    END AS nivel_stock
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN proveedores pr ON p.proveedor_id = pr.id;

-- =====================================================
-- Vista: Movimientos con Información Completa
-- =====================================================
CREATE OR REPLACE VIEW vista_movimientos_completa AS
SELECT 
    m.id,
    m.producto_id,
    p.producto_codigo,
    p.producto_nombre,
    m.movimiento_tipo,
    m.movimiento_cantidad,
    m.movimiento_motivo,
    m.movimiento_precio_unitario,
    m.movimiento_cantidad * m.movimiento_precio_unitario AS movimiento_total,
    u.usuario_nombre,
    m.created_at
FROM movimientos m
INNER JOIN productos p ON m.producto_id = p.id
LEFT JOIN usuarios u ON m.usuario_id = u.id
ORDER BY m.created_at DESC;

-- =====================================================
-- Fin del Script de Migración
-- =====================================================
