<?php
/**
 * Modelo Cliente
 * Gestiona la base de datos de clientes
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Cliente {
    private $db;
    private $table = 'cliente';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todos los clientes
     */
    public function all() {
        $sql = "SELECT * FROM cliente ORDER BY nombre";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar un cliente por ID
     */
    public function find($id) {
        $sql = "SELECT * FROM cliente WHERE id_cliente = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear un nuevo cliente
     */
    public function create($data) {
        $sql = "INSERT INTO cliente (nombre, telefono, email, direccion) 
                VALUES (:nombre, :telefono, :email, :direccion)";
        
        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':telefono', $data['telefono'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':direccion', $data['direccion'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Actualizar un cliente
     */
    public function update($id, $data) {
        $sql = "UPDATE cliente 
                SET nombre = :nombre, 
                    telefono = :telefono, 
                    email = :email, 
                    direccion = :direccion 
                WHERE id_cliente = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':telefono', $data['telefono'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':direccion', $data['direccion'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Eliminar un cliente
     */
    public function delete($id) {
        $sql = "DELETE FROM cliente WHERE id_cliente = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Buscar clientes por nombre
     */
    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM cliente WHERE nombre LIKE :nombre ORDER BY nombre LIMIT 20";
        $this->db->query($sql);
        $this->db->bind(':nombre', "%$nombre%");
        return $this->db->resultSet();
    }

    /**
     * Obtener órdenes de instalación de un cliente
     */
    public function obtenerOrdenes($id) {
        $sql = "SELECT * FROM orden_instalacion 
                WHERE id_cliente = :id 
                ORDER BY fecha_programada DESC";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->resultSet();
    }

    /**
     * Verificar si un cliente tiene órdenes
     */
    public function tieneOrdenes($id) {
        $sql = "SELECT COUNT(*) as total FROM orden_instalacion WHERE id_cliente = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result['total'] > 0;
    }
}
