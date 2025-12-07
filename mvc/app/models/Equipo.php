<?php
/**
 * Modelo Equipo
 * Gestiona el catálogo maestro de equipos (cámaras y sensores)
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Equipo {
    private $db;
    private $table = 'equipo';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todos los equipos con información de marca y categoría
     */
    public function all() {
        $sql = "SELECT 
                    e.id_equipo,
                    e.sku,
                    e.tipo,
                    e.descripcion,
                    e.id_marca,
                    e.id_categoria,
                    m.nombre AS marca_nombre,
                    c.nombre AS categoria_nombre
                FROM equipo e
                LEFT JOIN marca m ON e.id_marca = m.id_marca
                LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
                ORDER BY e.sku";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar un equipo por ID
     */
    public function find($id) {
        $sql = "SELECT 
                    e.*,
                    m.nombre AS marca_nombre,
                    c.nombre AS categoria_nombre
                FROM equipo e
                LEFT JOIN marca m ON e.id_marca = m.id_marca
                LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
                WHERE e.id_equipo = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear un nuevo equipo
     */
    public function create($data) {
        $sql = "INSERT INTO equipo (sku, tipo, descripcion, id_marca, id_categoria) 
                VALUES (:sku, :tipo, :descripcion, :id_marca, :id_categoria)";
        
        $this->db->query($sql);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':tipo', $data['tipo']);
        $this->db->bind(':descripcion', $data['descripcion'] ?? null);
        $this->db->bind(':id_marca', $data['id_marca'] ?? null);
        $this->db->bind(':id_categoria', $data['id_categoria'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Actualizar un equipo
     */
    public function update($id, $data) {
        $sql = "UPDATE equipo 
                SET sku = :sku, 
                    tipo = :tipo, 
                    descripcion = :descripcion, 
                    id_marca = :id_marca, 
                    id_categoria = :id_categoria 
                WHERE id_equipo = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':tipo', $data['tipo']);
        $this->db->bind(':descripcion', $data['descripcion'] ?? null);
        $this->db->bind(':id_marca', $data['id_marca'] ?? null);
        $this->db->bind(':id_categoria', $data['id_categoria'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Eliminar un equipo
     */
    public function delete($id) {
        $sql = "DELETE FROM equipo WHERE id_equipo = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Buscar equipo por SKU
     */
    public function buscarPorSKU($sku) {
        $sql = "SELECT * FROM equipo WHERE sku = :sku";
        $this->db->query($sql);
        $this->db->bind(':sku', $sku);
        return $this->db->single();
    }

    /**
     * Búsqueda avanzada con filtros
     */
    public function buscar($termino = '', $filtros = []) {
        $sql = "SELECT 
                    e.id_equipo,
                    e.sku,
                    e.tipo,
                    e.descripcion,
                    m.nombre AS marca_nombre,
                    c.nombre AS categoria_nombre
                FROM equipo e
                LEFT JOIN marca m ON e.id_marca = m.id_marca
                LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
                WHERE 1=1";
        
        // Búsqueda por término (SKU o descripción)
        if (!empty($termino)) {
            $sql .= " AND (e.sku LIKE :termino OR e.descripcion LIKE :termino)";
        }
        
        // Filtro por tipo
        if (!empty($filtros['tipo'])) {
            $sql .= " AND e.tipo = :tipo";
        }
        
        // Filtro por marca
        if (!empty($filtros['id_marca'])) {
            $sql .= " AND e.id_marca = :id_marca";
        }
        
        // Filtro por categoría
        if (!empty($filtros['id_categoria'])) {
            $sql .= " AND e.id_categoria = :id_categoria";
        }
        
        $sql .= " ORDER BY e.sku LIMIT 50";
        
        $this->db->query($sql);
        
        if (!empty($termino)) {
            $this->db->bind(':termino', "%$termino%");
        }
        if (!empty($filtros['tipo'])) {
            $this->db->bind(':tipo', $filtros['tipo']);
        }
        if (!empty($filtros['id_marca'])) {
            $this->db->bind(':id_marca', $filtros['id_marca']);
        }
        if (!empty($filtros['id_categoria'])) {
            $this->db->bind(':id_categoria', $filtros['id_categoria']);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Obtener equipos por categoría
     */
    public function porCategoria($categoria_id) {
        $sql = "SELECT * FROM equipo WHERE id_categoria = :categoria_id ORDER BY sku";
        $this->db->query($sql);
        $this->db->bind(':categoria_id', $categoria_id);
        return $this->db->resultSet();
    }

    /**
     * Obtener equipos por marca
     */
    public function porMarca($marca_id) {
        $sql = "SELECT * FROM equipo WHERE id_marca = :marca_id ORDER BY sku";
        $this->db->query($sql);
        $this->db->bind(':marca_id', $marca_id);
        return $this->db->resultSet();
    }

    /**
     * Obtener stock total de un equipo (suma de todos sus lotes disponibles)
     */
    public function stockTotal($id) {
        $sql = "SELECT SUM(cantidad) as total 
                FROM inventario_lote 
                WHERE id_equipo = :id AND estado = 'DISPONIBLE'";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }
}
