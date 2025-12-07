<?php
/**
 * Modelo Usuario
 * Gestiona usuarios del sistema con roles (ADMIN, ALMACEN, LECTOR)
 * @author Sistema de Inventarios
 * @fecha 22/11/2025
 */

class Usuario {
    private $db;
    private $table = 'usuario';

    public function __construct() {
        $this->db = new Base($this->table);
    }

    /**
     * Obtener todos los usuarios
     */
    public function all() {
        $sql = "SELECT id_usuario, nombre, email, rol FROM usuario ORDER BY nombre";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar un usuario por ID
     */
    public function find($id) {
        $sql = "SELECT id_usuario, nombre, email, rol FROM usuario WHERE id_usuario = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        $sql = "INSERT INTO usuario (nombre, email, password, rol) 
                VALUES (:nombre, :email, :password, :rol)";
        
        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':rol', $data['rol'] ?? 'LECTOR');
        
        return $this->db->execute();
    }

    /**
     * Actualizar un usuario
     */
    public function update($id, $data) {
        // Si se proporciona nueva contraseña
        if (!empty($data['password'])) {
            $sql = "UPDATE usuario 
                    SET nombre = :nombre, 
                        email = :email, 
                        password = :password, 
                        rol = :rol 
                    WHERE id_usuario = :id";
            
            $this->db->query($sql);
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $sql = "UPDATE usuario 
                    SET nombre = :nombre, 
                        email = :email, 
                        rol = :rol 
                    WHERE id_usuario = :id";
            
            $this->db->query($sql);
        }
        
        $this->db->bind(':id', $id);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':rol', $data['rol']);
        
        return $this->db->execute();
    }

    /**
     * Eliminar un usuario
     */
    public function delete($id) {
        $sql = "DELETE FROM usuario WHERE id_usuario = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Login de usuario (RF-21)
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        $usuario = $this->db->single();
        
        if ($usuario) {
            if (password_verify($password, $usuario['password'])) {
                // No devolver la contraseña
                unset($usuario['password']);
                return $usuario;
            }
        }
        return false;
    }

    /**
     * Buscar usuario por email
     */
    public function buscarPorEmail($email) {
        $sql = "SELECT id_usuario, nombre, email, rol FROM usuario WHERE email = :email";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Verificar si el usuario tiene permiso según su rol
     */
    public function tienePermiso($rol_usuario, $rol_requerido) {
        $jerarquia = [
            'LECTOR' => 1,
            'ALMACEN' => 2,
            'ADMIN' => 3
        ];
        
        return ($jerarquia[$rol_usuario] ?? 0) >= ($jerarquia[$rol_requerido] ?? 0);
    }

    /**
     * Obtener usuarios por rol
     */
    public function porRol($rol) {
        $sql = "SELECT id_usuario, nombre, email, rol 
                FROM usuario 
                WHERE rol = :rol 
                ORDER BY nombre";
        $this->db->query($sql);
        $this->db->bind(':rol', $rol);
        return $this->db->resultSet();
    }

    /**
     * Cambiar contraseña de un usuario
     */
    public function cambiarPassword($id, $nueva_password) {
        $sql = "UPDATE usuario SET password = :password WHERE id_usuario = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':password', password_hash($nueva_password, PASSWORD_DEFAULT));
        return $this->db->execute();
    }
}