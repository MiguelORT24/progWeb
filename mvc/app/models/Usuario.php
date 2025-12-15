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
        $sql = "SELECT id_usuario, nombre, email, 'ADMIN' AS rol FROM usuario ORDER BY nombre";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Buscar un usuario por ID
     */
    public function find($id) {
        $sql = "SELECT id_usuario, nombre, email, 'ADMIN' AS rol FROM usuario WHERE id_usuario = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        $sql = "INSERT INTO usuario (nombre, email, contrasena) 
                VALUES (:nombre, :email, :contrasena)";
        
        $this->db->query($sql);
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':contrasena', password_hash($data['password'], PASSWORD_DEFAULT));
        
        return $this->db->execute();
    }

    /**
     * Actualizar un usuario
     */
    public function update($id, $data) {
        $campos = [
            'nombre' => $data['nombre'],
            'email' => $data['email']
        ];

        if (!empty($data['password'])) {
            $campos['contrasena'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sets = [];
        foreach ($campos as $key => $value) {
            $sets[] = "{$key} = :{$key}";
        }

        $sql = "UPDATE usuario SET " . implode(', ', $sets) . " WHERE id_usuario = :id";
        $this->db->query($sql);

        foreach ($campos as $key => $value) {
            $this->db->bind(":{$key}", $value);
        }
        $this->db->bind(':id', $id);

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
        $sql = "SELECT id_usuario, nombre, email, contrasena FROM usuario WHERE email = :email";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        $usuario = $this->db->single();
        
        if ($usuario) {
            if (password_verify($password, $usuario['contrasena'])) {
                unset($usuario['contrasena']);
                $usuario['rol'] = 'ADMIN';
                return $usuario;
            } else {
                $this->logDebug("password_mismatch", $email);
            }
        } else {
            $this->logDebug("email_not_found", $email);
        }
        return false;
    }

    /**
     * Buscar usuario por email
     */
    public function buscarPorEmail($email) {
        $sql = "SELECT id_usuario, nombre, email, 'ADMIN' AS rol FROM usuario WHERE email = :email";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    /**
     * Cambiar contraseña de un usuario
     */
    public function cambiarPassword($id, $nueva_password) {
        $sql = "UPDATE usuario SET contrasena = :contrasena WHERE id_usuario = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':contrasena', password_hash($nueva_password, PASSWORD_DEFAULT));
        return $this->db->execute();
    }

    /**
     * Log sencillo a archivo para depurar problemas de login en servidor web
     */
    private function logDebug(string $tipo, string $email): void {
        $line = sprintf("[%s] %s :: %s\n", date('Y-m-d H:i:s'), $tipo, $email);
        $file = __DIR__ . '/../../logs/login_debug.log';
        // Intentar en el repo
        if (@file_put_contents($file, $line, FILE_APPEND) !== false) {
            return;
        }
        // Fallback a /tmp (permite escribir aunque Apache tenga permisos limitados)
        @file_put_contents('/tmp/inventario_login.log', $line, FILE_APPEND);
        // Último recurso: error_log
        error_log("[LOGIN_DEBUG] " . trim($line));
    }
}
