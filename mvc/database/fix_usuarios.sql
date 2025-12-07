-- =====================================================
-- Script de Actualización Rápida
-- Agrega el campo usuario_nivel y actualiza la contraseña
-- =====================================================

USE inventario;

-- Agregar el campo usuario_nivel si no existe
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS usuario_nivel INT DEFAULT 1 AFTER usuario_password;

-- Actualizar el usuario administrador con la contraseña correcta
-- Contraseña: admin123
UPDATE usuarios 
SET usuario_password = '$2y$10$wBMOmvJAi8yveHLHhHK0/udNRtl1YJHdIDvIEpA/uxkW7XTfKqX.G',
    usuario_nivel = 1
WHERE usuario_email = 'admin@inventario.com';

-- Si no existe el usuario, crearlo
INSERT INTO usuarios (usuario_nombre, usuario_email, usuario_password, usuario_nivel)
SELECT 'Administrador', 'admin@inventario.com', '$2y$10$wBMOmvJAi8yveHLHhHK0/udNRtl1YJHdIDvIEpA/uxkW7XTfKqX.G', 1
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario_email = 'admin@inventario.com');
