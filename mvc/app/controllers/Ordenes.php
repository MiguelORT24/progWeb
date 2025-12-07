<?php
/**
 * Controlador Ordenes
 * Gestiona las órdenes de instalación (RF-17, RF-18, RF-19)
 */

class Ordenes extends Controller {
    private $ordenModel;
    private $clienteModel;
    private $loteModel;

    public function __construct() {
        $this->ordenModel = $this->model('OrdenInstalacion');
        $this->clienteModel = $this->model('Cliente');
        $this->loteModel = $this->model('InventarioLote');
    }

    /**
     * Vista principal - Listar órdenes
     */
    public function index() {
        $ordenes = $this->ordenModel->all();
        
        $data = [
            'titulo' => 'Órdenes de Instalación',
            'ordenes' => $ordenes
        ];
        
        $this->view('ordenes/index', $data);
    }

    /**
     * Crear nueva orden (RF-17)
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id_cliente' => $_POST['id_cliente'],
                'fecha_programada' => $_POST['fecha_programada'],
                'estado' => 'PENDIENTE',
                'id_usuario' => $_SESSION['usuario_id'] ?? null
            ];
            
            $orden_id = $this->ordenModel->create($data);
            
            if ($orden_id) {
                $_SESSION['mensaje'] = 'Orden creada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('ordenes/reservar/' . $orden_id);
            } else {
                $_SESSION['mensaje'] = 'Error al crear la orden';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $data = [
            'clientes' => $this->clienteModel->all()
        ];
        
        $this->view('ordenes/crear', $data);
    }

    /**
     * Reservar materiales para la orden (RF-18)
     */
    public function reservar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_lote = $_POST['id_lote'];
            $cantidad = $_POST['cantidad'];
            
            if ($this->ordenModel->agregarMaterial($id, $id_lote, $cantidad)) {
                $_SESSION['mensaje'] = 'Material reservado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al reservar material. Verifique disponibilidad';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $orden = $this->ordenModel->find($id);
        $materiales = $this->ordenModel->obtenerMateriales($id);
        $lotesDisponibles = $this->loteModel->lotesDisponibles();
        
        $data = [
            'orden' => $orden,
            'materiales' => $materiales,
            'lotes_disponibles' => $lotesDisponibles
        ];
        
        $this->view('ordenes/reservar', $data);
    }

    /**
     * Confirmar instalación (RF-19)
     */
    public function confirmar($id) {
        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        
        if ($this->ordenModel->confirmarInstalacion($id, $usuario_id)) {
            $_SESSION['mensaje'] = 'Instalación confirmada. Stock actualizado';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al confirmar la instalación';
            $_SESSION['tipo_mensaje'] = 'danger';
        }
        
        redirect('ordenes');
    }

    /**
     * Ver detalle de orden
     */
    public function ver($id) {
        $orden = $this->ordenModel->find($id);
        $materiales = $this->ordenModel->obtenerMateriales($id);
        
        $data = [
            'orden' => $orden,
            'materiales' => $materiales
        ];
        
        $this->view('ordenes/ver', $data);
    }

    /**
     * Cambiar estado de orden
     */
    public function cambiarEstado($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $estado = $_POST['estado'];
            
            if ($this->ordenModel->cambiarEstado($id, $estado)) {
                $_SESSION['mensaje'] = 'Estado actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al cambiar el estado';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        redirect('ordenes');
    }

    /**
     * Órdenes pendientes
     */
    public function pendientes() {
        $ordenes = $this->ordenModel->pendientes();
        
        $data = [
            'titulo' => 'Órdenes Pendientes',
            'ordenes' => $ordenes
        ];
        
        $this->view('ordenes/pendientes', $data);
    }

    /**
     * Eliminar orden (solo si está pendiente)
     */
    public function eliminar($id) {
        if ($this->ordenModel->delete($id)) {
            $_SESSION['mensaje'] = 'Orden eliminada exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'No se puede eliminar una orden en proceso o completada';
            $_SESSION['tipo_mensaje'] = 'warning';
        }
        
        redirect('ordenes');
    }
}
