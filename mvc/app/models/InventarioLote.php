<?php
/**
 * Modelo InventarioLote
 * Gestiona los lotes de inventario con ubicación y estado
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class InventarioLote {
    private $db;
    private $table = 'inventario_lote';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todos los lotes con información completa
     */
    public function all() {
        $sql = "SELECT * FROM vista_inventario_completo";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar un lote por ID
     */
    public function find($id) {
        $sql = "SELECT * FROM vista_inventario_completo WHERE id_lote = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear un nuevo lote
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO inventario_lote (id_equipo, id_ubicacion, cantidad, estado, fecha_ingreso) 
                    VALUES (:id_equipo, :id_ubicacion, :cantidad, :estado, :fecha_ingreso)";
            
            $this->db->query($sql);
            $this->db->bind(':id_equipo', $data['id_equipo']);
            $this->db->bind(':id_ubicacion', $data['id_ubicacion']);
            $this->db->bind(':cantidad', $data['cantidad']);
            $this->db->bind(':estado', $data['estado'] ?? 'DISPONIBLE');
            $this->db->bind(':fecha_ingreso', $data['fecha_ingreso'] ?? date('Y-m-d'));
            
            if (!$this->db->execute()) {
                $this->db->rollBack();
                return false;
            }
            
            // Obtener el ID del lote recién creado
            $id_lote = $this->db->lastInsertId();
            
            // Crear movimiento de entrada
            $sqlMov = "INSERT INTO movimiento_inventario (tipo, id_lote, cantidad, motivo, id_usuario) 
                       VALUES ('ENTRADA', :id_lote, :cantidad, :motivo, :id_usuario)";
            
            $this->db->query($sqlMov);
            $this->db->bind(':id_lote', $id_lote);
            $this->db->bind(':cantidad', $data['cantidad']);
            $this->db->bind(':motivo', 'Nuevo lote ingresado');
            $this->db->bind(':id_usuario', $_SESSION['usuario_id'] ?? null);
            
            if (!$this->db->execute()) {
                $this->db->rollBack();
                return false;
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Actualizar un lote
     */
    public function update($id, $data) {
        // Si la cantidad es 0 o menor, eliminar el lote
        if (isset($data['cantidad']) && $data['cantidad'] <= 0) {
            return $this->delete($id);
        }
        
        $sql = "UPDATE inventario_lote 
                SET id_equipo = :id_equipo, 
                    id_ubicacion = :id_ubicacion, 
                    cantidad = :cantidad, 
                    estado = :estado, 
                    fecha_ingreso = :fecha_ingreso 
                WHERE id_lote = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':id_equipo', $data['id_equipo']);
        $this->db->bind(':id_ubicacion', $data['id_ubicacion']);
        $this->db->bind(':cantidad', $data['cantidad']);
        $this->db->bind(':estado', $data['estado']);
        $this->db->bind(':fecha_ingreso', $data['fecha_ingreso']);
        
        return $this->db->execute();
    }

    /**
     * Cambiar estado de un lote (RF-07)
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE inventario_lote SET estado = :estado WHERE id_lote = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':estado', $estado);
        return $this->db->execute();
    }

    /**
     * Actualizar cantidad de un lote
     */
    public function actualizarCantidad($id, $cantidad) {
        // Si la cantidad es 0, eliminar el lote
        if ($cantidad <= 0) {
            return $this->delete($id);
        }
        
        $sql = "UPDATE inventario_lote SET cantidad = :cantidad WHERE id_lote = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':cantidad', $cantidad);
        return $this->db->execute();
    }

    /**
     * Eliminar un lote
     */
    public function delete($id) {
        $sql = "DELETE FROM inventario_lote WHERE id_lote = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Búsqueda avanzada con filtros (RF-08)
     */
    public function buscar($filtros = []) {
        $sql = "SELECT * FROM vista_inventario_completo WHERE 1=1";
        
        // Filtro por tipo de equipo
        if (!empty($filtros['tipo'])) {
            $sql .= " AND tipo = :tipo";
        }
        
        // Filtro por ubicación
        if (!empty($filtros['id_ubicacion'])) {
            $sql .= " AND id_ubicacion = :id_ubicacion";
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = :estado";
        }
        
        // Filtro por marca
        if (!empty($filtros['marca'])) {
            $sql .= " AND marca_nombre LIKE :marca";
        }
        
        // Filtro por categoría
        if (!empty($filtros['categoria'])) {
            $sql .= " AND categoria_nombre LIKE :categoria";
        }
        
        // Filtro por rango de fechas
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND fecha_ingreso >= :fecha_desde";
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND fecha_ingreso <= :fecha_hasta";
        }
        
        // Búsqueda por SKU
        if (!empty($filtros['sku'])) {
            $sql .= " AND sku LIKE :sku";
        }
        
        $sql .= " ORDER BY fecha_ingreso DESC";
        
        $this->db->query($sql);
        
        // Bind de parámetros
        if (!empty($filtros['tipo'])) {
            $this->db->bind(':tipo', $filtros['tipo']);
        }
        if (!empty($filtros['id_ubicacion'])) {
            $this->db->bind(':id_ubicacion', $filtros['id_ubicacion']);
        }
        if (!empty($filtros['estado'])) {
            $this->db->bind(':estado', $filtros['estado']);
        }
        if (!empty($filtros['marca'])) {
            $this->db->bind(':marca', "%{$filtros['marca']}%");
        }
        if (!empty($filtros['categoria'])) {
            $this->db->bind(':categoria', "%{$filtros['categoria']}%");
        }
        if (!empty($filtros['fecha_desde'])) {
            $this->db->bind(':fecha_desde', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $this->db->bind(':fecha_hasta', $filtros['fecha_hasta']);
        }
        if (!empty($filtros['sku'])) {
            $this->db->bind(':sku', "%{$filtros['sku']}%");
        }
        
        return $this->db->resultSet();
    }

    /**
     * Calcular stock disponible por equipo
     */
    public function stockDisponible($equipo_id) {
        $sql = "SELECT SUM(cantidad) as total 
                FROM inventario_lote 
                WHERE id_equipo = :id_equipo AND estado = 'DISPONIBLE'";
        $this->db->query($sql);
        $this->db->bind(':id_equipo', $equipo_id);
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }

    /**
     * Listar lotes disponibles
     */
    public function lotesDisponibles() {
        $sql = "SELECT * FROM vista_inventario_completo 
                WHERE estado = 'DISPONIBLE' AND cantidad > 0
                ORDER BY fecha_ingreso";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Lotes por ubicación
     */
    public function lotesPorUbicacion($ubicacion_id) {
        $sql = "SELECT * FROM vista_inventario_completo 
                WHERE id_ubicacion = :id_ubicacion
                ORDER BY sku";
        $this->db->query($sql);
        $this->db->bind(':id_ubicacion', $ubicacion_id);
        return $this->db->resultSet();
    }

    /**
     * Obtener historial de movimientos de un lote (RF-09)
     */
    public function historialMovimientos($id) {
        $sql = "SELECT * FROM vista_movimientos_completo 
                WHERE id_lote = :id_lote
                ORDER BY fecha_hora DESC";
        $this->db->query($sql);
        $this->db->bind(':id_lote', $id);
        return $this->db->resultSet();
    }

    /**
     * Reporte diario de inventario por ubicación
     */
    public function reporteDiario($fecha = null) {
        $fecha = $fecha ?? date('Y-m-d');
        
        $sql = "SELECT 
                    u.nombre AS ubicacion,
                    e.sku,
                    e.tipo,
                    e.descripcion,
                    SUM(il.cantidad) AS cantidad_total,
                    il.estado
                FROM inventario_lote il
                INNER JOIN equipo e ON il.id_equipo = e.id_equipo
                INNER JOIN ubicacion u ON il.id_ubicacion = u.id_ubicacion
                WHERE il.fecha_ingreso <= :fecha
                GROUP BY u.id_ubicacion, e.id_equipo, il.estado
                ORDER BY u.nombre, e.sku";
        
        $this->db->query($sql);
        $this->db->bind(':fecha', $fecha);
        return $this->db->resultSet();
    }

    /**
     * Lotes con stock bajo (menos de 5 unidades disponibles)
     */
    public function stockBajo($limite = 10) {
        $sql = "SELECT 
                    e.id_equipo,
                    e.sku,
                    e.tipo,
                    e.descripcion AS equipo_descripcion,
                    m.nombre AS marca_nombre,
                    c.nombre AS categoria_nombre,
                    SUM(il.cantidad) AS cantidad,
                    MIN(il.fecha_ingreso) AS fecha_ingreso
                FROM inventario_lote il
                INNER JOIN equipo e ON il.id_equipo = e.id_equipo
                LEFT JOIN marca m ON e.id_marca = m.id_marca
                LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
                WHERE il.estado = 'DISPONIBLE'
                GROUP BY e.id_equipo, e.sku, e.tipo, e.descripcion, m.nombre, c.nombre
                HAVING SUM(il.cantidad) <= :limite
                ORDER BY cantidad ASC";
                
        $this->db->query($sql);
        $this->db->bind(':limite', $limite);
        return $this->db->resultSet();
    }

    /**
     * Inventario agrupado por equipo con cantidad total
     */
    public function inventarioAgrupado($filtros = []) {
        $sql = "SELECT 
                    e.id_equipo,
                    e.sku,
                    e.tipo,
                    e.descripcion,
                    m.nombre AS marca_nombre,
                    c.nombre AS categoria_nombre,
                    SUM(CASE WHEN il.estado = 'DISPONIBLE' THEN il.cantidad ELSE 0 END) AS cantidad_disponible,
                    SUM(CASE WHEN il.estado = 'RESERVADO' THEN il.cantidad ELSE 0 END) AS cantidad_reservada,
                    SUM(il.cantidad) AS cantidad_total,
                    COUNT(il.id_lote) AS total_lotes
                FROM equipo e
                LEFT JOIN inventario_lote il ON e.id_equipo = il.id_equipo
                LEFT JOIN marca m ON e.id_marca = m.id_marca
                LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
                WHERE 1=1";
        
        // Aplicar filtros
        if (!empty($filtros['sku'])) {
            $sql .= " AND (e.sku LIKE :sku OR e.descripcion LIKE :sku)";
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND e.tipo = :tipo";
        }
        if (!empty($filtros['marca'])) {
            $sql .= " AND e.id_marca = :marca";
        }
        
        $sql .= " GROUP BY e.id_equipo, e.sku, e.tipo, e.descripcion, m.nombre, c.nombre
                  HAVING cantidad_total > 0
                  ORDER BY e.sku";
        
        $this->db->query($sql);
        
        // Bind filtros
        if (!empty($filtros['sku'])) {
            $this->db->bind(':sku', "%{$filtros['sku']}%");
        }
        if (!empty($filtros['tipo'])) {
            $this->db->bind(':tipo', $filtros['tipo']);
        }
        if (!empty($filtros['marca'])) {
            $this->db->bind(':marca', $filtros['marca']);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Obtener todos los lotes de un equipo específico
     * Ordenados por FIFO (primero los más antiguos)
     */
    public function lotesPorEquipo($id_equipo) {
        $sql = "SELECT * FROM vista_inventario_completo 
                WHERE id_equipo = :id_equipo
                ORDER BY fecha_ingreso ASC, id_lote ASC";
        $this->db->query($sql);
        $this->db->bind(':id_equipo', $id_equipo);
        return $this->db->resultSet();
    }

    /**
     * Procesar una venta - reduce inventario y crea movimientos
     */
    public function procesarVenta($detalle, $venta_id, $usuario_id) {
        try {
            $this->db->beginTransaction();
            
            foreach ($detalle as $item) {
                // Obtener lotes disponibles del equipo
                $lotes = $this->lotesPorEquipo($item['id_equipo']);
                
                $cantidadRestante = $item['cantidad'];
                
                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) break;
                    if ($lote['estado'] != 'DISPONIBLE') continue;
                    
                    $cantidadARestar = min($cantidadRestante, $lote['cantidad']);
                    $nuevaCantidad = $lote['cantidad'] - $cantidadARestar;
                    
                    // Actualizar cantidad del lote
                    $this->actualizarCantidad($lote['id_lote'], $nuevaCantidad);
                    
                    // Crear movimiento de salida
                    $sqlMov = "INSERT INTO movimiento_inventario (tipo, id_lote, cantidad, motivo, id_usuario) 
                               VALUES ('SALIDA', :id_lote, :cantidad, :motivo, :id_usuario)";
                    
                    $this->db->query($sqlMov);
                    $this->db->bind(':id_lote', $lote['id_lote']);
                    $this->db->bind(':cantidad', $cantidadARestar);
                    $this->db->bind(':motivo', "Venta #$venta_id confirmada");
                    $this->db->bind(':id_usuario', $usuario_id);
                    $this->db->execute();
                    
                    $cantidadRestante -= $cantidadARestar;
                }
                
                if ($cantidadRestante > 0) {
                    throw new Exception("Stock insuficiente para {$item['sku']}. Faltan $cantidadRestante unidades.");
                }
            }
            
            // Actualizar estado de la venta
            $sqlUpdate = "UPDATE compra SET estado = 'CONFIRMADA' WHERE id_compra = :id";
            $this->db->query($sqlUpdate);
            $this->db->bind(':id', $venta_id);
            $this->db->execute();
            
            $this->db->commit();
            
            return ['exito' => true];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }
}
