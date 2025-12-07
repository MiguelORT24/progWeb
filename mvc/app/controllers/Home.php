<?php
/**
 * Controlador Home
 * Dashboard principal del sistema de inventario
 */

class Home extends Controller {
    private $loteModel;
    private $equipoModel;
    private $ordenModel;
    private $compraModel;

    public function __construct() {
        // Verificar sesión (comentado temporalmente para testing)
        // if (!isset($_SESSION['usuario_id'])) {
        //     redirect('login');
        // }
        
        $this->loteModel = $this->model('InventarioLote');
        $this->equipoModel = $this->model('Equipo');
        $this->ordenModel = $this->model('OrdenInstalacion');
        $this->compraModel = $this->model('Compra');
    }

    /**
     * Dashboard principal
     */
    public function index() {
        // Estadísticas para el dashboard
        $data = [
            'titulo' => 'Dashboard - Sistema de Inventario',
            'lotes_disponibles' => count($this->loteModel->lotesDisponibles()),
            'stock_bajo' => count($this->loteModel->stockBajo()),
            'ordenes_pendientes' => count($this->ordenModel->pendientes()),
            'total_equipos' => count($this->equipoModel->all()),
            'usuario' => $_SESSION['usuario_nombre'] ?? 'Invitado',
            'rol' => $_SESSION['usuario_rol'] ?? 'LECTOR'
        ];
        
        $this->view('home/index', $data);
    }
}