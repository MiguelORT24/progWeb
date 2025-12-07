<?php
/**
 * Controlador Clientes
 * Gestiona el ABM de clientes (RF-16)
 */

class Clientes extends Controller {
    private $clienteModel;

    public function __construct() {
        $this->clienteModel = $this->model('Cliente');
    }

    /**
     * Vista principal - Listar clientes
     */
    public function index() {
        $clientes = $this->clienteModel->all();
        
        $data = [
            'titulo' => 'Gestión de Clientes',
            'clientes' => $clientes
        ];
        
        $this->view('clientes/index', $data);
    }

    /**
     * Crear nuevo cliente
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre']),
                'telefono' => trim($_POST['telefono']),
                'email' => trim($_POST['email']),
                'direccion' => trim($_POST['direccion'])
            ];
            
            if ($this->clienteModel->create($data)) {
                $_SESSION['mensaje'] = 'Cliente creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('clientes');
            } else {
                $_SESSION['mensaje'] = 'Error al crear el cliente';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $this->view('clientes/crear');
    }

    /**
     * Editar cliente
     */
    public function editar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'nombre' => trim($_POST['nombre']),
                'telefono' => trim($_POST['telefono']),
                'email' => trim($_POST['email']),
                'direccion' => trim($_POST['direccion'])
            ];
            
            if ($this->clienteModel->update($id, $data)) {
                $_SESSION['mensaje'] = 'Cliente actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('clientes');
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el cliente';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $cliente = $this->clienteModel->find($id);
        $data = ['cliente' => $cliente];
        $this->view('clientes/editar', $data);
    }

    /**
     * Ver detalle del cliente con sus órdenes
     */
    public function ver($id) {
        $cliente = $this->clienteModel->find($id);
        $ordenes = $this->clienteModel->obtenerOrdenes($id);
        
        $data = [
            'cliente' => $cliente,
            'ordenes' => $ordenes
        ];
        
        $this->view('clientes/ver', $data);
    }

    /**
     * Eliminar cliente
     */
    public function eliminar($id) {
        if ($this->clienteModel->tieneOrdenes($id)) {
            $_SESSION['mensaje'] = 'No se puede eliminar el cliente porque tiene órdenes asociadas';
            $_SESSION['tipo_mensaje'] = 'warning';
        } else {
            if ($this->clienteModel->delete($id)) {
                $_SESSION['mensaje'] = 'Cliente eliminado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al eliminar el cliente';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        redirect('clientes');
    }

    /**
     * Buscar clientes (AJAX)
     */
    public function buscarAjax() {
        header('Content-Type: application/json');
        
        $termino = $_GET['q'] ?? '';
        $clientes = $this->clienteModel->buscarPorNombre($termino);
        
        echo json_encode($clientes);
    }
}
