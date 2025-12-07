<?php
/**
 * Modelo Categoria
 * Gestiona las categorías de equipos
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Categoria {
    private $db;
    private $table = 'categoria';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todas las categorías
     */
    public function all() {
        $sql = "SELECT * FROM categoria ORDER BY nombre";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar una categoría por ID
     */
    public function find($id) {
        $sql = "SELECT * FROM categoria WHERE id_categoria = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear una nueva categoría
     */
    public function create($data) {
        $sql = "INSERT INTO categoria (nombre) VALUES (:nombre)";
        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        return $this->db->execute();
    }

    /**
     * Actualizar una categoría
     */
    public function update($id, $data) {
        $sql = "UPDATE categoria SET nombre = :nombre WHERE id_categoria = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':nombre', $data['nombre']);
        return $this->db->execute();
    }

    /**
     * Eliminar una categoría
     */
    public function delete($id) {
        // Verificar si hay equipos asociados
        $sql = "SELECT COUNT(*) as total FROM equipo WHERE id_categoria = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $resultado = $this->db->single();
        
        if ($resultado['total'] > 0) {
            return false; // No se puede eliminar si hay equipos asociados
        }
        
        $sql = "DELETE FROM categoria WHERE id_categoria = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Obtener categorías con conteo de equipos
     */
    public function conConteoEquipos() {
        $sql = "SELECT 
                    c.id_categoria,
                    c.nombre,
                    COUNT(e.id_equipo) as total_equipos
                FROM categoria c
                LEFT JOIN equipo e ON c.id_categoria = e.id_categoria
                GROUP BY c.id_categoria, c.nombre
                ORDER BY c.nombre";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar categoría por nombre
     */
    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM categoria WHERE nombre = :nombre";
        $this->db->query($sql);
        $this->db->bind(':nombre', $nombre);
        return $this->db->single();
    }
}
