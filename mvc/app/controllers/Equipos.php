<?php
/**
 * Controlador Equipos
 * Gestiona el catálogo de equipos (cámaras y sensores)
 */

class Equipos extends Controller {
    private $equipoModel;
    private $marcaModel;
    private $categoriaModel;

    public function __construct() {
        $this->equipoModel = $this->model('Equipo');
        $this->marcaModel = $this->model('Marca');
        $this->categoriaModel = $this->model('Categoria');
    }

    /**
     * Vista principal - Listar equipos con búsqueda
     */
    public function index() {
        $termino = $_GET['buscar'] ?? '';
        $filtros = [
            'tipo' => $_GET['tipo'] ?? '',
            'id_marca' => $_GET['marca'] ?? '',
            'id_categoria' => $_GET['categoria'] ?? ''
        ];
        
        if ($termino || array_filter($filtros)) {
            $equipos = $this->equipoModel->buscar($termino, $filtros);
        } else {
            $equipos = $this->equipoModel->all();
        }
        
        $data = [
            'titulo' => 'Catálogo de Equipos',
            'equipos' => $equipos,
            'marcas' => $this->marcaModel->all(),
            'categorias' => $this->categoriaModel->all(),
            'filtros' => $filtros,
            'termino' => $termino
        ];
        
        $this->view('equipos/index', $data);
    }

    /**
     * Crear nuevo equipo
     */
    public function crear() {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden crear equipos', 'equipos');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'sku' => strtoupper(trim($_POST['sku'])),
                'tipo' => $_POST['tipo'],
                'descripcion' => trim($_POST['descripcion']),
                'id_marca' => !empty($_POST['id_marca']) ? $_POST['id_marca'] : null,
                'id_categoria' => !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null
            ];
            
            // Validar SKU único
            if ($this->equipoModel->buscarPorSKU($data['sku'])) {
                $_SESSION['mensaje'] = 'El SKU ya existe';
                $_SESSION['tipo_mensaje'] = 'warning';
            } else {
                if ($this->equipoModel->create($data)) {
                    $_SESSION['mensaje'] = 'Equipo creado exitosamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                    redirect('equipos');
                } else {
                    $_SESSION['mensaje'] = 'Error al crear el equipo';
                    $_SESSION['tipo_mensaje'] = 'danger';
                }
            }
        }
        
        $data = [
            'marcas' => $this->marcaModel->all(),
            'categorias' => $this->categoriaModel->all()
        ];
        
        $this->view('equipos/crear', $data);
    }

    /**
     * Editar equipo
     */
    public function editar($id) {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden editar equipos', 'equipos');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'sku' => strtoupper(trim($_POST['sku'])),
                'tipo' => $_POST['tipo'],
                'descripcion' => trim($_POST['descripcion']),
                'id_marca' => !empty($_POST['id_marca']) ? $_POST['id_marca'] : null,
                'id_categoria' => !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null
            ];
            
            if ($this->equipoModel->update($id, $data)) {
                $_SESSION['mensaje'] = 'Equipo actualizado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('equipos');
            } else {
                $_SESSION['mensaje'] = 'Error al actualizar el equipo';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $equipo = $this->equipoModel->find($id);
        $data = [
            'equipo' => $equipo,
            'marcas' => $this->marcaModel->all(),
            'categorias' => $this->categoriaModel->all()
        ];
        
        $this->view('equipos/editar', $data);
    }

    /**
     * Ver detalle de equipo con stock
     */
    public function ver($id) {
        $equipo = $this->equipoModel->find($id);
        $stockTotal = $this->equipoModel->stockTotal($id);
        
        $data = [
            'equipo' => $equipo,
            'stock_total' => $stockTotal
        ];
        
        $this->view('equipos/ver', $data);
    }

    /**
     * Eliminar equipo
     */
    public function eliminar($id) {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden eliminar equipos', 'equipos');
        
        if ($this->equipoModel->delete($id)) {
            $_SESSION['mensaje'] = 'Equipo eliminado exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al eliminar el equipo';
            $_SESSION['tipo_mensaje'] = 'danger';
        }
        
        redirect('equipos');
    }

    /**
     * API para búsqueda AJAX
     */
    public function buscarAjax() {
        header('Content-Type: application/json');
        
        $termino = $_GET['q'] ?? '';
        $equipos = $this->equipoModel->buscar($termino);
        
        echo json_encode($equipos);
    }
}
