<?php
/**
 * Controlador Compras
 * Gestiona las compras a proveedores (RF-14, RF-15)
 */

class Compras extends Controller {
    private $compraModel;
    private $proveedorModel;
    private $equipoModel;
    private $ubicacionModel;

    public function __construct() {
        $this->compraModel = $this->model('Compra');
        $this->proveedorModel = $this->model('Proveedor');
        $this->equipoModel = $this->model('Equipo');
        $this->ubicacionModel = $this->model('Ubicacion');
    }

    /**
     * Vista principal - Listar compras
     */
    public function index() {
        $compras = $this->compraModel->all();
        
        $data = [
            'titulo' => 'Gestión de Compras',
            'compras' => $compras
        ];
        
        $this->view('compras/index', $data);
    }

    /**
     * Crear nueva compra (RF-14)
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Crear compra
            $dataCompra = [
                'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                'id_proveedor' => $_POST['id_proveedor'],
                'total' => 0,
                'estado' => 'PENDIENTE'
            ];
            
            $compra_id = $this->compraModel->create($dataCompra);
            
            if ($compra_id) {
                // Agregar detalles
                $equipos = $_POST['equipos'] ?? [];
                $cantidades = $_POST['cantidades'] ?? [];
                $costos = $_POST['costos'] ?? [];
                
                $total = 0;
                foreach ($equipos as $index => $id_equipo) {
                    if (!empty($id_equipo) && !empty($cantidades[$index]) && !empty($costos[$index])) {
                        $detalle = [
                            'id_equipo' => $id_equipo,
                            'cantidad' => $cantidades[$index],
                            'costo_unitario' => $costos[$index]
                        ];
                        
                        $this->compraModel->agregarDetalle($compra_id, $detalle);
                        $total += $cantidades[$index] * $costos[$index];
                    }
                }
                
                // Actualizar total
                $this->compraModel->update($compra_id, array_merge($dataCompra, ['total' => $total]));
                
                $_SESSION['mensaje'] = 'Compra creada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('compras');
            } else {
                $_SESSION['mensaje'] = 'Error al crear la compra';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        $data = [
            'proveedores' => $this->proveedorModel->all(),
            'equipos' => $this->equipoModel->all()
        ];
        
        $this->view('compras/crear', $data);
    }

    /**
     * Ver detalle de compra
     */
    public function ver($id) {
        $compra = $this->compraModel->find($id);
        $detalle = $this->compraModel->obtenerDetalle($id);
        
        $data = [
            'compra' => $compra,
            'detalle' => $detalle
        ];
        
        $this->view('compras/ver', $data);
    }

    /**
     * Confirmar compra y generar stock (RF-15)
     */
    public function confirmar($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ubicacion_id = $_POST['id_ubicacion'];
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            
            if ($this->compraModel->confirmarCompra($id, $ubicacion_id, $usuario_id)) {
                $_SESSION['mensaje'] = 'Compra confirmada. Stock generado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error al confirmar la compra';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            
            redirect('compras');
        }
        
        $compra = $this->compraModel->find($id);
        $detalle = $this->compraModel->obtenerDetalle($id);
        $ubicaciones = $this->ubicacionModel->all();
        
        $data = [
            'compra' => $compra,
            'detalle' => $detalle,
            'ubicaciones' => $ubicaciones
        ];
        
        $this->view('compras/confirmar', $data);
    }

    /**
     * Eliminar compra (solo si está pendiente)
     */
    public function eliminar($id) {
        if ($this->compraModel->delete($id)) {
            $_SESSION['mensaje'] = 'Compra eliminada exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'No se puede eliminar una compra confirmada';
            $_SESSION['tipo_mensaje'] = 'warning';
        }
        
        redirect('compras');
    }
}
