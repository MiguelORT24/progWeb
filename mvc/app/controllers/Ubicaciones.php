<?php
/**
 * Controlador Ubicaciones
 * Gestiona el ABM de ubicaciones del almacén
 */

class Ubicaciones extends Controller {
    private $ubicacionModel;

    public function __construct() {
        $this->ubicacionModel = $this->model('Ubicacion');
    }

    /**
     * Vista principal - Listar ubicaciones
     */
    public function index() {
        $ubicaciones = $this->ubicacionModel->all();
        
        $data = [
            'titulo' => 'Gestión de Ubicaciones',
            'ubicaciones' => $ubicaciones
        ];
        
        $this->view('ubicaciones/index', $data);
    }

    /**
     * Crear nueva ubicación
     */
    public function crear() {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden crear ubicaciones', 'ubicaciones');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'])
            ];
            
            if ($this->ubicacionModel->buscarPorNombre($data['nombre'])) {
                $_SESSION['mensaje'] = 'La ubicación ya existe';
                $_SESSION['tipo_mensaje'] = 'warning';
            } else {
                if ($this->ubicacionModel->create($data)) {
                    $_SESSION['mensaje'] = 'Ubicación creada exitosamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                    redirect('ubicaciones');
                } else {
                    $_SESSION['mensaje'] = 'Error al crear la ubicación';
                    $_SESSION['tipo_mensaje'] = 'danger';
                }
            }
        }
        
        $this->view('ubicaciones/crear');
    }

    /**
     * Editar ubicación
     */
    public function editar($id) {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden editar ubicaciones', 'ubicaciones');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'])
            ];
            
            if ($this->ubicacionModel->update($id, $data)) {
                $_SESSION['mensaje'] = 'Ubicación actualizada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('ubicaciones');
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar la ubicación';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $ubicacion = $this->ubicacionModel->find($id);
        $data = ['ubicacion' => $ubicacion];
        $this->view('ubicaciones/editar', $data);
    }

    /**
     * Eliminar ubicación
     */
    public function eliminar($id) {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden eliminar ubicaciones', 'ubicaciones');
        
        if ($this->ubicacionModel->tieneLotes($id)) {
            $_SESSION['mensaje'] = 'No se puede eliminar la ubicación porque tiene lotes asociados';
            $_SESSION['tipo_mensaje'] = 'warning';
        } else {
            if ($this->ubicacionModel->delete($id)) {
                $_SESSION['mensaje'] = 'Ubicación eliminada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar la ubicación';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        redirect('ubicaciones');
    }

    /**
     * Ver lotes en una ubicación
     */
    public function lotes($id) {
        $ubicacion = $this->ubicacionModel->find($id);
        $lotes = $this->ubicacionModel->obtenerLotes($id);
        
        $data = [
            'ubicacion' => $ubicacion,
            'lotes' => $lotes
        ];
        
        $this->view('ubicaciones/lotes', $data);
    }
}
