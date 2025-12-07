<?php
/**
 * Controlador Marcas
 * Gestiona el ABM de marcas de equipos
 */

class Marcas extends Controller {
    private $marcaModel;

    public function __construct() {
        $this->marcaModel = $this->model('Marca');
    }

    /**
     * Vista principal - Listar marcas
     */
    public function index() {
        $marcas = $this->marcaModel->all();
        
        $data = [
            'titulo' => 'Gestión de Marcas',
            'marcas' => $marcas
        ];
        
        $this->view('marcas/index', $data);
    }

    /**
     * Crear nueva marca
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'])
            ];
            
            // Validar que no exista
            if ($this->marcaModel->buscarPorNombre($data['nombre'])) {
                $_SESSION['mensaje'] = 'La marca ya existe';
                $_SESSION['tipo_mensaje'] = 'warning';
            } else {
                if ($this->marcaModel->create($data)) {
                    $_SESSION['mensaje'] = 'Marca creada exitosamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                    redirect('marcas');
                } else {
                    $_SESSION['mensaje'] = 'Error al crear la marca';
                    $_SESSION['tipo_mensaje'] = 'danger';
                }
            }
        }
        
        $this->view('marcas/crear');
    }

    /**
     * Editar marca
     */
    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre'])
            ];
            
            if ($this->marcaModel->update($id, $data)) {
                $_SESSION['mensaje'] = 'Marca actualizada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('marcas');
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar la marca';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $marca = $this->marcaModel->find($id);
        $data = ['marca' => $marca];
        $this->view('marcas/editar', $data);
    }

    /**
     * Eliminar marca
     */
    public function eliminar($id) {
        if ($this->marcaModel->tieneEquipos($id)) {
            $_SESSION['mensaje'] = 'No se puede eliminar la marca porque tiene equipos asociados';
            $_SESSION['tipo_mensaje'] = 'warning';
        } else {
            if ($this->marcaModel->delete($id)) {
                $_SESSION['mensaje'] = 'Marca eliminada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar la marca';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        redirect('marcas');
    }
}
