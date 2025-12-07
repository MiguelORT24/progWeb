<?php
/**
 * Modelo Marca
 * Gestiona las marcas de equipos
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Marca {
    private $db;
    private $table = 'marca';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todas las marcas
     */
    public function all() {
        $sql = "SELECT * FROM marca ORDER BY nombre";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar una marca por ID
     */
    public function find($id) {
        $sql = "SELECT * FROM marca WHERE id_marca = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear una nueva marca
     */
    public function create($data) {
        $sql = "INSERT INTO marca (nombre) VALUES (:nombre)";
        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        return $this->db->execute();
    }

    /**
     * Actualizar una marca
     */
    public function update($id, $data) {
        $sql = "UPDATE marca SET nombre = :nombre WHERE id_marca = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':nombre', $data['nombre']);
        return $this->db->execute();
    }

    /**
     * Eliminar una marca
     */
    public function delete($id) {
        $sql = "DELETE FROM marca WHERE id_marca = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Buscar marca por nombre (para validar unicidad)
     */
    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM marca WHERE nombre = :nombre";
        $this->db->query($sql);
        $this->db->bind(':nombre', $nombre);
        return $this->db->single();
    }

    /**
     * Verificar si una marca tiene equipos asociados
     */
    public function tieneEquipos($id) {
        $sql = "SELECT COUNT(*) as total FROM equipo WHERE id_marca = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result['total'] > 0;
    }
}
