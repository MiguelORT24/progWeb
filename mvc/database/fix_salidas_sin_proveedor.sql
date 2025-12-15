-- Script de migración para permitir salidas sin proveedor
-- Fecha: 2025-12-11
-- Propósito: Modificar la tabla compra para permitir id_proveedor NULL
--            ya que las salidas de inventario no requieren proveedor

USE pw20253;

-- Modificar la columna id_proveedor para permitir NULL
ALTER TABLE compra 
MODIFY COLUMN id_proveedor INT NULL;

-- Eliminar la restricción de clave foránea si existe
ALTER TABLE compra 
DROP FOREIGN KEY IF EXISTS compra_ibfk_1;

-- Volver a crear la clave foránea permitiendo NULL
ALTER TABLE compra 
ADD CONSTRAINT compra_ibfk_1 
FOREIGN KEY (id_proveedor) 
REFERENCES proveedor(id_proveedor) 
ON DELETE RESTRICT;

-- Verificar el cambio
DESCRIBE compra;

SELECT 'Migración completada exitosamente. La columna id_proveedor ahora permite NULL.' AS mensaje;
