<?php
/**
 * Modelo Ubicacion
 * Gestiona las ubicaciones físicas del almacén
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Ubicacion {
    private $db;
    private $table = 'ubicacion';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todas las ubicaciones
     */
    public function all() {
        $sql = "SELECT * FROM ubicacion ORDER BY nombre";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar una ubicación por ID
     */
    public function find($id) {
        $sql = "SELECT * FROM ubicacion WHERE id_ubicacion = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear una nueva ubicación
     */
    public function create($data) {
        $sql = "INSERT INTO ubicacion (nombre) VALUES (:nombre)";
        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        return $this->db->execute();
    }

    /**
     * Actualizar una ubicación
     */
    public function update($id, $data) {
        $sql = "UPDATE ubicacion SET nombre = :nombre WHERE id_ubicacion = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':nombre', $data['nombre']);
        return $this->db->execute();
    }

    /**
     * Eliminar una ubicación
     */
    public function delete($id) {
        $sql = "DELETE FROM ubicacion WHERE id_ubicacion = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Buscar ubicación por nombre
     */
    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM ubicacion WHERE nombre = :nombre";
        $this->db->query($sql);
        $this->db->bind(':nombre', $nombre);
        return $this->db->single();
    }

    /**
     * Obtener lotes por ubicación
     */
    public function obtenerLotes($id) {
        $sql = "SELECT il.*, e.sku, e.descripcion 
                FROM inventario_lote il
                INNER JOIN equipo e ON il.id_equipo = e.id_equipo
                WHERE il.id_ubicacion = :id
                ORDER BY e.sku";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->resultSet();
    }

    /**
     * Verificar si una ubicación tiene lotes
     */
    public function tieneLotes($id) {
        $sql = "SELECT COUNT(*) as total FROM inventario_lote WHERE id_ubicacion = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result['total'] > 0;
    }
}
