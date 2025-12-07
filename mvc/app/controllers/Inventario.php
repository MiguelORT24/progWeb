<?php
/**
 * Controlador Inventario
 * Gestiona los lotes de inventario (Vista principal UI-INV-01)
 */

class Inventario extends Controller {
    private $loteModel;
    private $equipoModel;
    private $ubicacionModel;
    private $marcaModel;
    private $categoriaModel;

    public function __construct() {
        $this->loteModel = $this->model('InventarioLote');
        $this->equipoModel = $this->model('Equipo');
        $this->ubicacionModel = $this->model('Ubicacion');
        $this->marcaModel = $this->model('Marca');
        $this->categoriaModel = $this->model('Categoria');
    }

    /**
     * Vista principal - UI-INV-01 (RF-08)
     * Panel de filtros y grilla de resultados
     */
    public function index() {
        // Capturar filtros
        $filtros = [
            'tipo' => $_GET['tipo'] ?? '',
            'id_ubicacion' => $_GET['ubicacion'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'marca' => $_GET['marca'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'sku' => $_GET['sku'] ?? ''
        ];
        
        // Buscar lotes con filtros
        $lotes = $this->loteModel->buscar($filtros);
        
        $data = [
            'titulo' => 'Inventario por Lotes',
            'lotes' => $lotes,
            'ubicaciones' => $this->ubicacionModel->all(),
            'marcas' => $this->marcaModel->all(),
            'categorias' => $this->categoriaModel->all(),
            'filtros' => $filtros
        ];
        
        $this->view('inventario/index', $data);
    }

    /**
     * Crear nuevo lote (RF-06)
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id_equipo' => $_POST['id_equipo'],
                'id_ubicacion' => $_POST['id_ubicacion'],
                'cantidad' => $_POST['cantidad'],
                'estado' => $_POST['estado'] ?? 'DISPONIBLE',
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d')
            ];
            
            if ($this->loteModel->create($data)) {
                $_SESSION['mensaje'] = 'Lote creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('inventario');
            } else {
                $_SESSION['mensaje'] = 'Error al crear el lote';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $data = [
            'equipos' => $this->equipoModel->all(),
            'ubicaciones' => $this->ubicacionModel->all()
        ];
        
        $this->view('inventario/crear', $data);
    }

    /**
     * Editar lote
     */
    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id_equipo' => $_POST['id_equipo'],
                'id_ubicacion' => $_POST['id_ubicacion'],
                'cantidad' => $_POST['cantidad'],
                'estado' => $_POST['estado'],
                'fecha_ingreso' => $_POST['fecha_ingreso']
            ];
            
            if ($this->loteModel->update($id, $data)) {
                $_SESSION['mensaje'] = 'Lote actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('inventario');
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el lote';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $lote = $this->loteModel->find($id);
        $data = [
            'lote' => $lote,
            'equipos' => $this->equipoModel->all(),
            'ubicaciones' => $this->ubicacionModel->all()
        ];
        
        $this->view('inventario/editar', $data);
    }

    /**
     * Cambiar estado de lote (RF-07)
     */
    public function cambiarEstado($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nuevoEstado = $_POST['estado'];
            
            if ($this->loteModel->cambiarEstado($id, $nuevoEstado)) {
                $_SESSION['mensaje'] = 'Estado actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al cambiar el estado';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        redirect('inventario');
    }

    /**
     * Ver historial de movimientos del lote (RF-09)
     */
    public function historial($id) {
        $lote = $this->loteModel->find($id);
        $movimientos = $this->loteModel->historialMovimientos($id);
        
        $data = [
            'lote' => $lote,
            'movimientos' => $movimientos
        ];
        
        $this->view('inventario/historial', $data);
    }

    /**
     * Reporte diario de inventario por ubicación
     */
    public function reporteDiario() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $reporte = $this->loteModel->reporteDiario($fecha);
        
        $data = [
            'titulo' => 'Reporte Diario de Inventario',
            'fecha' => $fecha,
            'reporte' => $reporte
        ];
        
        $this->view('inventario/reporte_diario', $data);
    }

    /**
     * Lotes con stock bajo
     */
    public function stockBajo() {
        $lotes = $this->loteModel->stockBajo();
        
        $data = [
            'titulo' => 'Lotes con Stock Bajo',
            'lotes' => $lotes
        ];
        
        $this->view('inventario/stock_bajo', $data);
    }

    /**
     * Lotes disponibles
     */
    public function disponibles() {
        $lotes = $this->loteModel->lotesDisponibles();
        
        $data = [
            'titulo' => 'Lotes Disponibles',
            'lotes' => $lotes
        ];
        
        $this->view('inventario/disponibles', $data);
    }
}
