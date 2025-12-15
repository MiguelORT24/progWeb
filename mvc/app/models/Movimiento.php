<?php
/**
 * Modelo Movimiento
 * Gestiona los movimientos de inventario (entradas y salidas) sobre lotes existentes
 */

class Movimiento {
    private $db;
    private $table = 'movimiento_inventario';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Historial de movimientos (últimos 100)
     */
    public function all() {
        $sql = "SELECT 
                    mi.id_mov,
                    mi.fecha_hora,
                    mi.tipo,
                    mi.cantidad,
                    mi.motivo,
                    il.id_lote,
                    il.id_equipo,
                    il.id_ubicacion,
                    e.sku,
                    e.descripcion AS equipo_descripcion,
                    u.nombre AS ubicacion_nombre,
                    usr.nombre AS usuario_nombre
                FROM movimiento_inventario mi
                LEFT JOIN inventario_lote il ON mi.id_lote = il.id_lote
                LEFT JOIN equipo e ON il.id_equipo = e.id_equipo
                LEFT JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
                LEFT JOIN usuario usr ON mi.id_usuario = usr.id_usuario
                ORDER BY mi.fecha_hora DESC
                LIMIT 100";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Todas las salidas (sin límite)
     */
    public function salidas() {
        $sql = "SELECT 
                    mi.id_mov,
                    mi.fecha_hora,
                    mi.tipo,
                    mi.cantidad,
                    mi.motivo,
                    il.id_lote,
                    il.id_equipo,
                    il.id_ubicacion,
                    e.sku,
                    e.descripcion AS equipo_descripcion,
                    u.nombre AS ubicacion_nombre,
                    usr.nombre AS usuario_nombre
                FROM movimiento_inventario mi
                LEFT JOIN inventario_lote il ON mi.id_lote = il.id_lote
                LEFT JOIN equipo e ON il.id_equipo = e.id_equipo
                LEFT JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
                LEFT JOIN usuario usr ON mi.id_usuario = usr.id_usuario
                WHERE mi.tipo = 'salida'
                ORDER BY mi.fecha_hora DESC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Movimientos de hoy
     */
    public function hoy() {
        $sql = "SELECT 
                    mi.id_mov,
                    mi.fecha_hora,
                    mi.tipo,
                    mi.cantidad,
                    mi.motivo,
                    e.sku,
                    e.descripcion AS equipo_descripcion,
                    usr.nombre AS usuario_nombre
                FROM movimiento_inventario mi
                LEFT JOIN inventario_lote il ON mi.id_lote = il.id_lote
                LEFT JOIN equipo e ON il.id_equipo = e.id_equipo
                LEFT JOIN usuario usr ON mi.id_usuario = usr.id_usuario
                WHERE DATE(mi.fecha_hora) = CURDATE()
                ORDER BY mi.fecha_hora ASC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Movimientos por fecha (YYYY-MM-DD)
     */
    public function porFecha($fecha) {
        $sql = "SELECT 
                    mi.id_mov,
                    mi.fecha_hora,
                    mi.tipo,
                    mi.cantidad,
                    mi.motivo,
                    e.sku,
                    e.descripcion AS equipo_descripcion,
                    usr.nombre AS usuario_nombre,
                    il.cantidad AS lote_cantidad
                FROM movimiento_inventario mi
                LEFT JOIN inventario_lote il ON mi.id_lote = il.id_lote
                LEFT JOIN equipo e ON il.id_equipo = e.id_equipo
                LEFT JOIN usuario usr ON mi.id_usuario = usr.id_usuario
                WHERE DATE(mi.fecha_hora) = :fecha
                ORDER BY mi.fecha_hora ASC";

        $this->db->query($sql);
        $this->db->bind(':fecha', $fecha);
        return $this->db->resultSet();
    }

    /**
     * Registrar movimiento y actualizar cantidad del lote.
     */
    public function create(array $data) {
        $this->db->beginTransaction();

        try {
            // Leer lote actual
            $sqlLote = "SELECT cantidad FROM inventario_lote WHERE id_lote = :id_lote FOR UPDATE";
            $this->db->query($sqlLote);
            $this->db->bind(':id_lote', $data['id_lote']);
            $lote = $this->db->single();

            if (!$lote) {
                throw new RuntimeException('El lote no existe.');
            }

            $cantidadActual = (int)$lote['cantidad'];
            $cantidad = (int)$data['cantidad'];

            if ($data['tipo'] === 'salida' && $cantidad > $cantidadActual) {
                throw new RuntimeException('Cantidad insuficiente en el lote.');
            }

            $nuevaCantidad = $data['tipo'] === 'entrada'
                ? $cantidadActual + $cantidad
                : $cantidadActual - $cantidad;

            // Actualizar cantidad del lote
            $sqlUpdate = "UPDATE inventario_lote SET cantidad = :cantidad WHERE id_lote = :id_lote";
            $this->db->query($sqlUpdate);
            $this->db->bind(':cantidad', $nuevaCantidad);
            $this->db->bind(':id_lote', $data['id_lote']);
            $this->db->execute();

            // Insertar movimiento
            $sqlMov = "INSERT INTO movimiento_inventario (fecha_hora, tipo, cantidad, motivo, id_lote, id_usuario)
                       VALUES (:fecha_hora, :tipo, :cantidad, :motivo, :id_lote, :id_usuario)";
            $this->db->query($sqlMov);
            $this->db->bind(':fecha_hora', date('Y-m-d H:i:s'));
            $this->db->bind(':tipo', $data['tipo']);
            $this->db->bind(':cantidad', $cantidad);
            $this->db->bind(':motivo', $data['motivo'] ?? null);
            $this->db->bind(':id_lote', $data['id_lote']);
            $this->db->bind(':id_usuario', $data['id_usuario'] ?? null);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Movimientos por lote
     */
    public function porLote($id_lote) {
        $sql = "SELECT 
                    mi.id_mov,
                    mi.fecha_hora,
                    mi.tipo,
                    mi.cantidad,
                    mi.motivo,
                    usr.nombre AS usuario_nombre
                FROM movimiento_inventario mi
                LEFT JOIN usuario usr ON mi.id_usuario = usr.id_usuario
                WHERE mi.id_lote = :id_lote
                ORDER BY mi.fecha_hora DESC";

        $this->db->query($sql);
        $this->db->bind(':id_lote', $id_lote);
        return $this->db->resultSet();
    }
}
