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
            // Debug mínimo para verificar que este controlador se ejecuta (se escribe en error_log de PHP)
            error_log("[LOGIN_CONTROLLER] intento para {$email}");
            // Registrar datos básicos para depurar diferencias entre CLI y web
            $logLine = sprintf("[%s] email=%s len_pass=%d\n", date('Y-m-d H:i:s'), $email, strlen($password));
            $logFile = APPROOT . 'logs/login_post.log';
            if (@file_put_contents($logFile, $logLine, FILE_APPEND) === false) {
                @file_put_contents('/tmp/inventario_login.log', $logLine, FILE_APPEND);
                error_log("[LOGIN_POST] " . trim($logLine));
            }
            
            // Intentar login
            $usuario = $this->usuarioModel->login($email, $password);
            
            if ($usuario) {
                // Crear sesión
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                // En la versión actual solo existe un rol
                $_SESSION['usuario_rol'] = $usuario['rol'] ?? 'ADMIN';
                
                error_log("[LOGIN_CONTROLLER] OK para {$email}");
                redirect('home');
            } else {
                error_log("[LOGIN_CONTROLLER] FAIL para {$email}");
                $data = [
                    'titulo' => 'Iniciar Sesión',
                    'email' => $email,
                    'error' => 'Email o contraseña incorrectos.'
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
