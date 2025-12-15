<?php
/**
 * Controlador Home
 * Dashboard principal del sistema de inventario
 */

class Home extends Controller {
    private $loteModel;
    private $equipoModel;

    public function __construct() {
        $this->loteModel = $this->model('InventarioLote');
        $this->equipoModel = $this->model('Equipo');
    }

    /**
     * Página inicial - Redirige según autenticación
     */
    public function index() {
        // Si no está logueado, redirigir a login
        if (!estaAutenticado()) {
            redirect('login');
        }
        
        // Si está logueado, redirigir a dashboard
        redirect('home/dashboard');
    }

    /**
     * Dashboard principal
     */
    public function dashboard() {
        requerirAuth();
        
        // Obtener inventario agrupado
        $inventario = $this->loteModel->inventarioAgrupado([]);
        $total_productos = count($inventario);
        $total_unidades = array_sum(array_column($inventario, 'cantidad_total'));
        
        // Stock bajo
        $stock_bajo = $this->loteModel->stockBajo(10);

        $data = [
            'titulo' => 'Dashboard - Sistema de Inventario',
            'total_productos' => $total_productos,
            'total_unidades' => $total_unidades,
            'stock_bajo' => $stock_bajo,
            'usuario' => $_SESSION['usuario_nombre'] ?? 'Invitado',
            'rol' => $_SESSION['usuario_rol'] ?? 'LECTOR'
        ];
        
        $this->view('home/dashboard', $data);
    }
}
