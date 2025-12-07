<?php
/**
 * Modelo OrdenInstalacion
 * Gestiona las órdenes de servicio de instalación
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class OrdenInstalacion {
    private $db;
    private $table = 'orden_instalacion';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todas las órdenes con información de cliente
     */
    public function all() {
        $sql = "SELECT 
                    o.*,
                    c.nombre AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    u.nombre AS usuario_nombre
                FROM orden_instalacion o
                INNER JOIN cliente c ON o.id_cliente = c.id_cliente
                LEFT JOIN usuario u ON o.id_usuario = u.id_usuario
                ORDER BY o.fecha_programada DESC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar una orden por ID
     */
    public function find($id) {
        $sql = "SELECT 
                    o.*,
                    c.nombre AS cliente_nombre,
                    c.telefono AS cliente_telefono,
                    c.email AS cliente_email,
                    c.direccion AS cliente_direccion,
                    u.nombre AS usuario_nombre
                FROM orden_instalacion o
                INNER JOIN cliente c ON o.id_cliente = c.id_cliente
                LEFT JOIN usuario u ON o.id_usuario = u.id_usuario
                WHERE o.id_orden = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear una nueva orden (RF-17)
     */
    public function create($data) {
        $sql = "INSERT INTO orden_instalacion (id_cliente, fecha_programada, estado, id_usuario) 
                VALUES (:id_cliente, :fecha_programada, :estado, :id_usuario)";
        
        $this->db->query($sql);
        $this->db->bind(':id_cliente', $data['id_cliente']);
        $this->db->bind(':fecha_programada', $data['fecha_programada']);
        $this->db->bind(':estado', $data['estado'] ?? 'PENDIENTE');
        $this->db->bind(':id_usuario', $data['id_usuario'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar una orden
     */
    public function update($id, $data) {
        $sql = "UPDATE orden_instalacion 
                SET id_cliente = :id_cliente, 
                    fecha_programada = :fecha_programada, 
                    estado = :estado, 
                    id_usuario = :id_usuario 
                WHERE id_orden = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':id_cliente', $data['id_cliente']);
        $this->db->bind(':fecha_programada', $data['fecha_programada']);
        $this->db->bind(':estado', $data['estado']);
        $this->db->bind(':id_usuario', $data['id_usuario'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Cambiar estado de una orden
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE orden_instalacion SET estado = :estado WHERE id_orden = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':estado', $estado);
        return $this->db->execute();
    }

    /**
     * Agregar material a una orden (reservar lote) (RF-18)
     */
    public function agregarMaterial($orden_id, $lote_id, $cantidad) {
        try {
            $this->db->beginTransaction();
            
            // Verificar que el lote esté disponible
            $sqlCheck = "SELECT cantidad, estado FROM inventario_lote WHERE id_lote = :lote_id";
            $this->db->query($sqlCheck);
            $this->db->bind(':lote_id', $lote_id);
            $lote = $this->db->single();
            
            if (!$lote || $lote['estado'] != 'DISPONIBLE' || $lote['cantidad'] < $cantidad) {
                throw new Exception("Lote no disponible o cantidad insuficiente");
            }
            
            // Agregar detalle a la orden
            $sqlDetalle = "INSERT INTO orden_instalacion_detalle (id_orden, id_lote, cantidad) 
                           VALUES (:id_orden, :id_lote, :cantidad)";
            $this->db->query($sqlDetalle);
            $this->db->bind(':id_orden', $orden_id);
            $this->db->bind(':id_lote', $lote_id);
            $this->db->bind(':cantidad', $cantidad);
            $this->db->execute();
            
            // Cambiar estado del lote a RESERVADO
            $sqlReservar = "UPDATE inventario_lote SET estado = 'RESERVADO' WHERE id_lote = :lote_id";
            $this->db->query($sqlReservar);
            $this->db->bind(':lote_id', $lote_id);
            $this->db->execute();
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Obtener materiales asignados a una orden
     */
    public function obtenerMateriales($orden_id) {
        $sql = "SELECT 
                    oid.*,
                    il.id_equipo,
                    e.sku,
                    e.tipo,
                    e.descripcion AS equipo_descripcion,
                    u.nombre AS ubicacion_nombre,
                    il.estado AS lote_estado
                FROM orden_instalacion_detalle oid
                INNER JOIN inventario_lote il ON oid.id_lote = il.id_lote
                INNER JOIN equipo e ON il.id_equipo = e.id_equipo
                INNER JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
                WHERE oid.id_orden = :id_orden";
        
        $this->db->query($sql);
        $this->db->bind(':id_orden', $orden_id);
        return $this->db->resultSet();
    }

    /**
     * Confirmar instalación (RF-19)
     * Genera movimientos de salida y actualiza stock
     */
    public function confirmarInstalacion($orden_id, $usuario_id) {
        try {
            $this->db->beginTransaction();
            
            // Obtener materiales de la orden
            $materiales = $this->obtenerMateriales($orden_id);
            
            if (empty($materiales)) {
                throw new Exception("La orden no tiene materiales asignados");
            }
            
            // Para cada material
            foreach ($materiales as $material) {
                // Crear movimiento de salida
                $sqlMov = "INSERT INTO movimiento_inventario (tipo, id_lote, cantidad, motivo, id_usuario) 
                           VALUES ('SALIDA', :id_lote, :cantidad, :motivo, :id_usuario)";
                
                $this->db->query($sqlMov);
                $this->db->bind(':id_lote', $material['id_lote']);
                $this->db->bind(':cantidad', $material['cantidad']);
                $this->db->bind(':motivo', "Instalación - Orden #$orden_id");
                $this->db->bind(':id_usuario', $usuario_id);
                $this->db->execute();
                
                // Actualizar cantidad del lote
                $sqlUpdate = "UPDATE inventario_lote 
                              SET cantidad = cantidad - :cantidad,
                                  estado = CASE 
                                      WHEN (cantidad - :cantidad) = 0 THEN 'INSTALADO'
                                      ELSE 'DISPONIBLE'
                                  END
                              WHERE id_lote = :id_lote";
                
                $this->db->query($sqlUpdate);
                $this->db->bind(':id_lote', $material['id_lote']);
                $this->db->bind(':cantidad', $material['cantidad']);
                $this->db->execute();
            }
            
            // Cambiar estado de la orden a COMPLETADA
            $sqlOrden = "UPDATE orden_instalacion SET estado = 'COMPLETADA' WHERE id_orden = :id";
            $this->db->query($sqlOrden);
            $this->db->bind(':id', $orden_id);
            $this->db->execute();
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Eliminar una orden (solo si está pendiente)
     */
    public function delete($id) {
        $sql = "DELETE FROM orden_instalacion WHERE id_orden = :id AND estado = 'PENDIENTE'";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Obtener órdenes pendientes
     */
    public function pendientes() {
        $sql = "SELECT 
                    o.*,
                    c.nombre AS cliente_nombre
                FROM orden_instalacion o
                INNER JOIN cliente c ON o.id_cliente = c.id_cliente
                WHERE o.estado = 'PENDIENTE'
                ORDER BY o.fecha_programada";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Obtener órdenes por cliente
     */
    public function porCliente($cliente_id) {
        $sql = "SELECT * FROM orden_instalacion 
                WHERE id_cliente = :id_cliente 
                ORDER BY fecha_programada DESC";
        $this->db->query($sql);
        $this->db->bind(':id_cliente', $cliente_id);
        return $this->db->resultSet();
    }
}
