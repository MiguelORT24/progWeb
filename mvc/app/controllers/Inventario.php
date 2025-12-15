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
    private $movimientoModel;

    public function __construct() {
        $this->loteModel = $this->model('InventarioLote');
        $this->equipoModel = $this->model('Equipo');
        $this->ubicacionModel = $this->model('Ubicacion');
        $this->marcaModel = $this->model('Marca');
        $this->categoriaModel = $this->model('Categoria');
        $this->movimientoModel = $this->model('Movimiento');
    }

    /**
     * Vista principal - Inventario Agrupado por Producto
     * Muestra productos con cantidades totales
     */
    public function index() {
        // Capturar filtros
        $filtros = [
            'tipo' => $_GET['tipo'] ?? '',
            'marca' => $_GET['marca'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'sku' => $_GET['sku'] ?? ''
        ];
        
        // Obtener inventario agrupado
        $productos = $this->loteModel->inventarioAgrupado($filtros);
        
        $marcas = $this->marcaModel->all();
        if (!empty($filtros['marca'])) {
            $enLista = array_filter($marcas, fn($m) => (string)$m['id_marca'] === (string)$filtros['marca']);
            if (empty($enLista)) {
                $marcaExtra = $this->marcaModel->find($filtros['marca']);
                if ($marcaExtra) {
                    $marcas[] = $marcaExtra;
                }
            }
        }

        $categorias = $this->categoriaModel->all();
        if (!empty($filtros['categoria'])) {
            $enListaCat = array_filter($categorias, fn($c) => (string)$c['id_categoria'] === (string)$filtros['categoria']);
            if (empty($enListaCat)) {
                $catExtra = $this->categoriaModel->find($filtros['categoria']);
                if ($catExtra) {
                    $categorias[] = $catExtra;
                }
            }
        }

        $data = [
            'titulo' => 'Inventario por Producto',
            'productos' => $productos,
            'marcas' => $marcas,
            'categorias' => $categorias,
            'tipos' => ['CAMARA', 'SENSOR', 'COMPONENTE'],
            'filtros' => $filtros
        ];
        
        $this->view('inventario/index', $data);
    }

    /**
     * Ver lotes de un producto específico
     */
    public function verLotes($id_equipo) {
        $equipo = $this->equipoModel->find($id_equipo);
        $lotes = $this->loteModel->lotesPorEquipo($id_equipo);
        
        $data = [
            'equipo' => $equipo,
            'lotes' => $lotes,
            'ubicaciones' => $this->ubicacionModel->all()
        ];
        
        $this->view('inventario/lotes', $data);
    }

    /**
     * Crear nuevo lote (RF-06)
     */
    public function crear() {
        requerirAuth();
        requerirPermiso(puedeCrear(), 'No tienes permisos para crear lotes', 'inventario');
        
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
        requerirAuth();
        requerirPermiso(puedeEditar(), 'No tienes permisos para editar lotes', 'inventario');
        
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
        requerirAuth();
        requerirPermiso(puedeEditar(), 'No tienes permisos para cambiar estados', 'inventario');
        
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
        $movimientosDia = $this->movimientoModel->porFecha($fecha);
        $entradasDia = array_reduce($movimientosDia, fn($carry, $mov) => $carry + ($mov['tipo'] === 'entrada' ? (int)$mov['cantidad'] : 0), 0);
        $salidasDia = array_reduce($movimientosDia, fn($carry, $mov) => $carry + ($mov['tipo'] === 'salida' ? (int)$mov['cantidad'] : 0), 0);
        $entradasDetalle = array_values(array_filter($movimientosDia, fn($mov) => $mov['tipo'] === 'entrada'));
        $salidasDetalle = array_values(array_filter($movimientosDia, fn($mov) => $mov['tipo'] === 'salida'));
        $productos = $this->loteModel->inventarioAgrupado([]);
        $totalUnidades = array_reduce($productos, fn($carry, $p) => $carry + (int)($p['cantidad_total'] ?? 0), 0);
        
        $data = [
            'titulo' => 'Reporte Diario de Inventario',
            'fecha' => $fecha,
            'reporte' => $reporte,
            'entradas' => $entradasDia,
            'salidas' => $salidasDia,
            'entradas_detalle' => $entradasDetalle,
            'salidas_detalle' => $salidasDetalle,
            'productos' => $productos,
            'total_unidades' => $totalUnidades,
            'usuario' => $_SESSION['usuario_nombre'] ?? 'Sistema'
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
