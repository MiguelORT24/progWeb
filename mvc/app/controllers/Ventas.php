<?php
/**
 * Controlador Salidas
 * Gestiona las salidas de inventario (reduce stock)
 */

class Ventas extends Controller {
    private $ventaModel;
    private $clienteModel;
    private $equipoModel;
    private $loteModel;

    public function __construct() {
        $this->ventaModel = $this->model('Compra'); // Reutilizamos el modelo Compra por ahora
        $this->clienteModel = $this->model('Cliente');
        $this->equipoModel = $this->model('Equipo');
        $this->loteModel = $this->model('InventarioLote');
    }

    /**
     * Vista principal - Listar salidas
     */
    public function index() {
        $ventas = $this->ventaModel->all();
        
        $data = [
            'titulo' => 'Gestión de Salidas',
            'ventas' => $ventas
        ];
        
        $this->view('ventas/index', $data);
    }

    /**
     * Crear nueva salida
     */
    public function crear() {
        requerirAuth();
        requerirPermiso(puedeCrear(), 'No tienes permisos para crear salidas', 'ventas');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar inventario disponible antes de crear la salida
            $equipos = $_POST['equipos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $precios = $_POST['precios'] ?? [];
            
            $errores = [];
            
            foreach ($equipos as $index => $id_equipo) {
                if (!empty($id_equipo) && !empty($cantidades[$index])) {
                    $cantidadSolicitada = (int)$cantidades[$index];
                    
                    // Validar que la cantidad sea mayor a 0
                    if ($cantidadSolicitada <= 0) {
                        $equipo = $this->equipoModel->find($id_equipo);
                        $errores[] = "La cantidad para '{$equipo['descripcion']}' debe ser mayor a 0.";
                        continue;
                    }
                    
                    // Obtener inventario disponible del equipo
                    $inventario = $this->loteModel->inventarioAgrupado(['id_equipo' => $id_equipo]);
                    
                    if (!empty($inventario)) {
                        $disponible = (int)$inventario[0]['cantidad_disponible'];
                        
                        if ($cantidadSolicitada > $disponible) {
                            $equipo = $this->equipoModel->find($id_equipo);
                            $errores[] = "El producto '{$equipo['descripcion']}' solo tiene {$disponible} unidades disponibles. Solicitaste {$cantidadSolicitada}.";
                        }
                    } else {
                        $equipo = $this->equipoModel->find($id_equipo);
                        $errores[] = "El producto '{$equipo['descripcion']}' no tiene inventario disponible.";
                    }
                }
            }
            
            // Si hay errores, mostrarlos y no crear la salida
            if (!empty($errores)) {
                $_SESSION['mensaje'] = implode('<br>', $errores);
                $_SESSION['tipo_mensaje'] = 'warning';
                
                // Redirigir de vuelta al formulario
                $productos = $this->loteModel->inventarioAgrupado([]);
                $data = [
                    'productos' => $productos
                ];
                $this->view('ventas/crear', $data);
                return;
            }
            
            // Crear salida
            $dataVenta = [
                'fecha' => $_POST['fecha'] ?? date('Y-m-d H:i:s'),
                'id_proveedor' => null, // No usamos clientes en salidas
                'total' => 0,
                'estado' => 'PENDIENTE'
            ];
            
            $venta_id = $this->ventaModel->create($dataVenta);
            
            if ($venta_id) {
                // Agregar detalles
                $total = 0;
                foreach ($equipos as $index => $id_equipo) {
                    if (!empty($id_equipo) && !empty($cantidades[$index]) && !empty($precios[$index])) {
                        $detalle = [
                            'id_equipo' => $id_equipo,
                            'cantidad' => $cantidades[$index],
                            'costo_unitario' => $precios[$index] // Usamos el mismo campo
                        ];
                        
                        $this->ventaModel->agregarDetalle($venta_id, $detalle);
                        $total += $cantidades[$index] * $precios[$index];
                    }
                }
                
                // Actualizar total
                $this->ventaModel->update($venta_id, array_merge($dataVenta, ['total' => $total]));
                
                $_SESSION['mensaje'] = 'Salida creada exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                redirect('ventas');
            } else {
                $_SESSION['mensaje'] = 'Error al crear la salida';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        }
        
        // Obtener equipos con stock disponible
        $productos = $this->loteModel->inventarioAgrupado([]);
        
        $data = [
            'productos' => $productos
        ];
        
        $this->view('ventas/crear', $data);
    }

    /**
     * Ver detalle de salida
     */
    public function ver($id) {
        $venta = $this->ventaModel->find($id);
        $detalle = $this->ventaModel->obtenerDetalle($id);
        
        $data = [
            'venta' => $venta,
            'detalle' => $detalle
        ];
        
        $this->view('ventas/ver', $data);
    }

    /**
     * Confirmar salida y reducir stock
     */
    public function confirmar($id) {
        requerirAuth();
        requerirPermiso(puedeConfirmar(), 'Solo administradores pueden confirmar salidas', 'ventas');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            
            // Obtener detalle de la salida
            $venta = $this->ventaModel->find($id);
            $detalle = $this->ventaModel->obtenerDetalle($id);
            
            // Procesar venta usando el modelo de lotes
            $resultado = $this->loteModel->procesarVenta($detalle, $id, $usuario_id);
            
            if ($resultado['exito']) {
                $_SESSION['mensaje'] = 'Salida confirmada. Inventario reducido exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = 'Error: ' . $resultado['error'];
                $_SESSION['tipo_mensaje'] = 'danger';
            }
            
            redirect('ventas');
        }
        
        $venta = $this->ventaModel->find($id);
        $detalle = $this->ventaModel->obtenerDetalle($id);
        
        $data = [
            'venta' => $venta,
            'detalle' => $detalle
        ];
        
        $this->view('ventas/confirmar', $data);
    }

    /**
     * Eliminar salida (solo si está pendiente)
     */
    public function eliminar($id) {
        requerirAuth();
        requerirPermiso(puedeEliminar(), 'No tienes permisos para eliminar salidas', 'ventas');
        
        if ($this->ventaModel->delete($id)) {
            $_SESSION['mensaje'] = 'Salida eliminada exitosamente';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'No se puede eliminar una salida confirmada';
            $_SESSION['tipo_mensaje'] = 'warning';
        }
        
        redirect('ventas');
    }
}
