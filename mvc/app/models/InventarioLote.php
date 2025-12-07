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
        $sql = "INSERT INTO inventario_lote (id_equipo, id_ubicacion, cantidad, estado, fecha_ingreso) 
                VALUES (:id_equipo, :id_ubicacion, :cantidad, :estado, :fecha_ingreso)";
        
        $this->db->query($sql);
        $this->db->bind(':id_equipo', $data['id_equipo']);
        $this->db->bind(':id_ubicacion', $data['id_ubicacion']);
        $this->db->bind(':cantidad', $data['cantidad']);
        $this->db->bind(':estado', $data['estado'] ?? 'DISPONIBLE');
        $this->db->bind(':fecha_ingreso', $data['fecha_ingreso'] ?? date('Y-m-d'));
        
        return $this->db->execute();
    }

    /**
     * Actualizar un lote
     */
    public function update($id, $data) {
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
        $sql = "UPDATE inventario_lote SET cantidad = :cantidad WHERE id_lote = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':cantidad', $cantidad);
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
    public function stockBajo($limite = 5) {
        $sql = "SELECT * FROM vista_inventario_completo 
                WHERE estado = 'DISPONIBLE' AND cantidad <= :limite
                ORDER BY cantidad ASC";
        $this->db->query($sql);
        $this->db->bind(':limite', $limite);
        return $this->db->resultSet();
    }
}
