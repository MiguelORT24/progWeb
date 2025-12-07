<?php
/**
 * Modelo Proveedor
 * Gestiona los proveedores de productos
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Proveedor {
    private $db;
    private $table = 'proveedores';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todos los proveedores
     */
    public function all() {
        return $this->db->get();
    }

    /**
     * Buscar un proveedor por ID
     */
    public function find($id) {
        return $this->db->find($id);
    }

    /**
     * Crear un nuevo proveedor
     */
    public function create($data) {
        return $this->db->create($data);
    }

    /**
     * Actualizar un proveedor
     */
    public function update($id, $data) {
        return $this->db->where('id', $id)->update($data);
    }

    /**
     * Eliminar un proveedor
     */
    public function delete($id) {
        // Verificar si hay productos asociados
        $sql = "SELECT COUNT(*) as total FROM productos WHERE proveedor_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $resultado = $this->db->single();
        
        if ($resultado['total'] > 0) {
            return false; // No se puede eliminar si hay productos asociados
        }
        
        return $this->db->where('id', $id)->delete();
    }

    /**
     * Obtener proveedores con conteo de productos
     */
    public function conConteoProductos() {
        $sql = "SELECT 
                    pr.id,
                    pr.proveedor_nombre,
                    pr.proveedor_contacto,
                    pr.proveedor_telefono,
                    pr.proveedor_email,
                    COUNT(p.id) as total_productos
                FROM proveedores pr
                LEFT JOIN productos p ON pr.id = p.proveedor_id
                GROUP BY pr.id, pr.proveedor_nombre, pr.proveedor_contacto, 
                         pr.proveedor_telefono, pr.proveedor_email
                ORDER BY pr.proveedor_nombre";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
