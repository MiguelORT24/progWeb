<?php
/**
 * Modelo Compra
 * Gestiona las compras a proveedores
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Compra {
    private $db;
    private $table = 'compra';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todas las compras con información de proveedor
     */
    public function all() {
        $sql = "SELECT 
                    c.*,
                    p.nombre AS proveedor_nombre
                FROM compra c
                LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
                ORDER BY c.fecha DESC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar una compra por ID
     */
    public function find($id) {
        $sql = "SELECT 
                    c.*,
                    p.nombre AS proveedor_nombre,
                    p.contacto AS proveedor_contacto,
                    p.telefono AS proveedor_telefono
                FROM compra c
                LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
                WHERE c.id_compra = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear una nueva compra (RF-14)
     */
    public function create($data) {
        $sql = "INSERT INTO compra (fecha, id_proveedor, total, estado) 
                VALUES (:fecha, :id_proveedor, :total, :estado)";
        
        $this->db->query($sql);
        $this->db->bind(':fecha', $data['fecha'] ?? date('Y-m-d'));
        $this->db->bind(':id_proveedor', $data['id_proveedor']);
        $this->db->bind(':total', $data['total'] ?? 0);
        $this->db->bind(':estado', $data['estado'] ?? 'PENDIENTE');
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Actualizar una compra
     */
    public function update($id, $data) {
        $sql = "UPDATE compra 
                SET fecha = :fecha, 
                    id_proveedor = :id_proveedor, 
                    total = :total, 
                    estado = :estado 
                WHERE id_compra = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':fecha', $data['fecha']);
        $this->db->bind(':id_proveedor', $data['id_proveedor']);
        $this->db->bind(':total', $data['total']);
        $this->db->bind(':estado', $data['estado']);
        
        return $this->db->execute();
    }

    /**
     * Agregar detalle a una compra
     */
    public function agregarDetalle($compra_id, $detalle) {
        $sql = "INSERT INTO compra_detalle (id_compra, id_equipo, cantidad, costo_unitario) 
                VALUES (:id_compra, :id_equipo, :cantidad, :costo_unitario)";
        
        $this->db->query($sql);
        $this->db->bind(':id_compra', $compra_id);
        $this->db->bind(':id_equipo', $detalle['id_equipo']);
        $this->db->bind(':cantidad', $detalle['cantidad']);
        $this->db->bind(':costo_unitario', $detalle['costo_unitario']);
        
        return $this->db->execute();
    }

    /**
     * Obtener detalle de una compra
     */
    public function obtenerDetalle($compra_id) {
        $sql = "SELECT 
                    cd.*,
                    e.sku,
                    e.tipo,
                    e.descripcion AS equipo_descripcion,
                    (cd.cantidad * cd.costo_unitario) AS subtotal
                FROM compra_detalle cd
                INNER JOIN equipo e ON cd.id_equipo = e.id_equipo
                WHERE cd.id_compra = :id_compra";
        
        $this->db->query($sql);
        $this->db->bind(':id_compra', $compra_id);
        return $this->db->resultSet();
    }

    /**
     * Confirmar compra y generar stock (RF-15)
     * Crea lotes en inventario y movimientos de entrada
     */
    public function confirmarCompra($compra_id, $ubicacion_id, $usuario_id) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();
            
            // Obtener detalle de la compra
            $detalles = $this->obtenerDetalle($compra_id);
            
            if (empty($detalles)) {
                throw new Exception("La compra no tiene detalles");
            }
            
            // Para cada ítem del detalle
            foreach ($detalles as $detalle) {
                // Crear lote en inventario
                $sqlLote = "INSERT INTO inventario_lote (id_equipo, id_ubicacion, cantidad, estado, fecha_ingreso) 
                            VALUES (:id_equipo, :id_ubicacion, :cantidad, 'DISPONIBLE', :fecha_ingreso)";
                
                $this->db->query($sqlLote);
                $this->db->bind(':id_equipo', $detalle['id_equipo']);
                $this->db->bind(':id_ubicacion', $ubicacion_id);
                $this->db->bind(':cantidad', $detalle['cantidad']);
                $this->db->bind(':fecha_ingreso', date('Y-m-d'));
                $this->db->execute();
                
                $lote_id = $this->db->lastInsertId();
                
                // Crear movimiento de entrada
                $sqlMov = "INSERT INTO movimiento_inventario (tipo, id_lote, cantidad, motivo, id_usuario) 
                           VALUES ('ENTRADA', :id_lote, :cantidad, :motivo, :id_usuario)";
                
                $this->db->query($sqlMov);
                $this->db->bind(':id_lote', $lote_id);
                $this->db->bind(':cantidad', $detalle['cantidad']);
                $this->db->bind(':motivo', "Compra #$compra_id confirmada");
                $this->db->bind(':id_usuario', $usuario_id);
                $this->db->execute();
            }
            
            // Actualizar estado de la compra
            $sqlUpdate = "UPDATE compra SET estado = 'CONFIRMADA' WHERE id_compra = :id";
            $this->db->query($sqlUpdate);
            $this->db->bind(':id', $compra_id);
            $this->db->execute();
            
            // Confirmar transacción
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Eliminar una compra (solo si está pendiente)
     */
    public function delete($id) {
        $sql = "DELETE FROM compra WHERE id_compra = :id AND estado = 'PENDIENTE'";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Calcular total de una compra basado en su detalle
     */
    public function calcularTotal($compra_id) {
        $sql = "SELECT SUM(cantidad * costo_unitario) as total 
                FROM compra_detalle 
                WHERE id_compra = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $compra_id);
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }
}
