<?php
/**
 * Modelo Proveedor
 * Gestiona los proveedores de productos
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Proveedor {
    private $db;
    private $table = 'proveedor';

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
        return $this->db->where('id_proveedor', $id)->delete();
    }

    /**
     * Obtener proveedores con conteo de compras
     */
    public function conConteoCompras() {
        $sql = "SELECT 
                    pr.id_proveedor,
                    pr.nombre,
                    pr.contacto,
                    pr.telefono,
                    pr.email,
                    COUNT(c.id_compra) as total_compras
                FROM proveedor pr
                LEFT JOIN compra c ON pr.id_proveedor = c.id_proveedor
                GROUP BY pr.id_proveedor, pr.nombre, pr.contacto, 
                         pr.telefono, pr.email
                ORDER BY pr.nombre";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}
