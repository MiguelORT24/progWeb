-- =====================================================
-- Script de Corrección: Tabla compra
-- Fecha: 2025-12-11
-- Descripción: 
--   1. Permite NULL en id_proveedor para salidas
--   2. Cambia fecha de DATE a DATETIME para guardar hora
-- =====================================================

USE inventario;

-- Modificar id_proveedor para permitir NULL (para salidas)
ALTER TABLE compra 
MODIFY COLUMN id_proveedor INT NULL;

-- Cambiar tipo de columna fecha de DATE a DATETIME
ALTER TABLE compra 
MODIFY COLUMN fecha DATETIME NOT NULL;

-- Verificar los cambios
DESCRIBE compra;

-- =====================================================
-- Fin del Script
-- =====================================================
