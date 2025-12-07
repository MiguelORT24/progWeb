<?php
/**
 * Modelo Movimiento
 * Gestiona los movimientos de inventario (entradas y salidas)
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Movimiento {
    private $db;
    private $table = 'movimientos';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todos los movimientos con información completa
     */
    public function all() {
        $sql = "SELECT 
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
                ORDER BY m.created_at DESC
                LIMIT 100";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Obtener movimientos del día actual
     */
    public function hoy() {
        $sql = "SELECT 
                    mi.id_mov,
                    mi.tipo,
                    mi.cantidad,
                    mi.motivo,
                    e.sku,
                    e.descripcion,
                    u.nombre AS usuario_nombre,
                    mi.fecha_hora AS created_at
                FROM movimiento_inventario mi
                INNER JOIN inventario_lote il ON mi.id_lote = il.id_lote
                INNER JOIN equipo e ON il.id_equipo = e.id_equipo
                LEFT JOIN usuario u ON mi.id_usuario = u.id_usuario
                WHERE DATE(mi.fecha_hora) = CURDATE()
                ORDER BY mi.fecha_hora ASC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Crear un nuevo movimiento y actualizar stock
     */
    public function create($data) {
        // Iniciar transacción
        $this->db->beginTransaction();
        
        try {
            // Crear el movimiento
            $sql = "INSERT INTO movimientos 
                    (producto_id, movimiento_tipo, movimiento_cantidad, movimiento_motivo, 
                     movimiento_precio_unitario, usuario_id) 
                    VALUES 
                    (:producto_id, :movimiento_tipo, :movimiento_cantidad, :movimiento_motivo, 
                     :movimiento_precio_unitario, :usuario_id)";
            
            $this->db->query($sql);
            $this->db->bind(':producto_id', $data['producto_id']);
            $this->db->bind(':movimiento_tipo', $data['movimiento_tipo']);
            $this->db->bind(':movimiento_cantidad', $data['movimiento_cantidad']);
            $this->db->bind(':movimiento_motivo', $data['movimiento_motivo'] ?? null);
            $this->db->bind(':movimiento_precio_unitario', $data['movimiento_precio_unitario'] ?? null);
            $this->db->bind(':usuario_id', $data['usuario_id'] ?? null);
            
            $this->db->execute();
            
            // Actualizar stock del producto
            $tipo = $data['movimiento_tipo'];
            $cantidad = $data['movimiento_cantidad'];
            
            if ($tipo === 'entrada') {
                $sqlStock = "UPDATE productos SET producto_stock = producto_stock + :cantidad WHERE id = :id";
            } else {
                $sqlStock = "UPDATE productos SET producto_stock = producto_stock - :cantidad WHERE id = :id";
            }
            
            $this->db->query($sqlStock);
            $this->db->bind(':cantidad', $cantidad);
            $this->db->bind(':id', $data['producto_id']);
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
     * Obtener movimientos por producto
     */
    public function porProducto($producto_id) {
        $sql = "SELECT 
                    m.id,
                    m.movimiento_tipo,
                    m.movimiento_cantidad,
                    m.movimiento_motivo,
                    m.movimiento_precio_unitario,
                    u.usuario_nombre,
                    m.created_at
                FROM movimientos m
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.producto_id = :producto_id
                ORDER BY m.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':producto_id', $producto_id);
        return $this->db->resultSet();
    }

    /**
     * Obtener movimientos por rango de fechas
     */
    public function porFecha($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    m.id,
                    m.producto_id,
                    p.producto_codigo,
                    p.producto_nombre,
                    m.movimiento_tipo,
                    m.movimiento_cantidad,
                    m.movimiento_motivo,
                    m.movimiento_precio_unitario,
                    u.usuario_nombre,
                    m.created_at
                FROM movimientos m
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                WHERE DATE(m.created_at) BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY m.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':fecha_inicio', $fecha_inicio);
        $this->db->bind(':fecha_fin', $fecha_fin);
        return $this->db->resultSet();
    }

    /**
     * Obtener resumen de movimientos
     */
    public function resumen() {
        $sql = "SELECT 
                    COUNT(*) as total_movimientos,
                    SUM(CASE WHEN movimiento_tipo = 'entrada' THEN movimiento_cantidad ELSE 0 END) as total_entradas,
                    SUM(CASE WHEN movimiento_tipo = 'salida' THEN movimiento_cantidad ELSE 0 END) as total_salidas,
                    SUM(CASE WHEN movimiento_tipo = 'entrada' THEN movimiento_cantidad * movimiento_precio_unitario ELSE 0 END) as valor_entradas,
                    SUM(CASE WHEN movimiento_tipo = 'salida' THEN movimiento_cantidad * movimiento_precio_unitario ELSE 0 END) as valor_salidas
                FROM movimientos
                WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        
        $this->db->query($sql);
        return $this->db->single();
    }

    /**
     * Obtener últimos movimientos
     */
    public function ultimos($limite = 10) {
        $sql = "SELECT 
                    m.id,
                    p.producto_codigo,
                    p.producto_nombre,
                    m.movimiento_tipo,
                    m.movimiento_cantidad,
                    m.movimiento_motivo,
                    u.usuario_nombre,
                    m.created_at
                FROM movimientos m
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                ORDER BY m.created_at DESC
                LIMIT :limite";
        
        $this->db->query($sql);
        $this->db->bind(':limite', $limite);
        return $this->db->resultSet();
    }
}
