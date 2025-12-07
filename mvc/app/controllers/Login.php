<?php
/**
 * Controlador Login
 * Gestiona la autenticación de usuarios
 */

class Login extends Controller {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = $this->model('Usuario');
    }

    /**
     * Mostrar formulario de login
     */
    public function index() {
        // Si ya está logueado, redirigir al home
        if (isset($_SESSION['usuario_id'])) {
            redirect('home');
        }
        
        $data = [
            'titulo' => 'Iniciar Sesión',
            'email' => '',
            'error' => ''
        ];
        
        $this->view('login/index', $data);
    }

    /**
     * Procesar login
     */
    public function entrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            // Intentar login
            $usuario = $this->usuarioModel->login($email, $password);
            
            if ($usuario) {
                // Crear sesión
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                
                redirect('home');
            } else {
                $data = [
                    'titulo' => 'Iniciar Sesión',
                    'email' => $email,
                    'error' => 'Email o contraseña incorrectos'
                ];
                
                $this->view('login/index', $data);
            }
        } else {
            redirect('login');
        }
    }

    /**
     * Cerrar sesión
     */
    public function salir() {
        session_unset();
        session_destroy();
        redirect('login');
    }
}
