<?php

class Movimientos extends Controller {

    private $modelo;
    private $modeloProducto;

    public function __construct() {
        $this->modelo = $this->model('Movimiento');
        $this->modeloProducto = $this->model('Producto');
    }

    /**
     * Método index
     */
    public function index() {
        $data = $this->modelo->all();
        $this->view('movimientos/index', $data);
    }

    /**
     * Método entrada - Registrar entrada de productos
     */
    public function entrada() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $data = [
            'accion' => 'Entrada',
            'error' => [],
            'productos' => $this->modeloProducto->all()
        ];

        if ($metodo == 'POST') {
            $data = $_POST;
            $data['movimiento_tipo'] = 'entrada';
            $data['usuario_id'] = $_SESSION['usuario_id'] ?? null;
            $data['productos'] = $this->modeloProducto->all();

            $data['error'] = $this->validarMovimiento($data);

            if (empty($data['error'])) {
                unset($data['error']);
                unset($data['productos']);

                $resultado = $this->modelo->create($data);

                if ($resultado) {
                    refresh('/movimientos');
                } else {
                    $data['error'][] = 'Error al registrar la entrada';
                }
            }
        }

        $this->view('movimientos/entrada', $data);
    }

    /**
     * Método salida - Registrar salida de productos
     */
    public function salida() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $data = [
            'accion' => 'Salida',
            'error' => [],
            'productos' => $this->modeloProducto->all()
        ];

        if ($metodo == 'POST') {
            $data = $_POST;
            $data['movimiento_tipo'] = 'salida';
            $data['usuario_id'] = $_SESSION['usuario_id'] ?? null;
            $data['productos'] = $this->modeloProducto->all();

            $data['error'] = $this->validarMovimiento($data);

            if (empty($data['error'])) {
                // Verificar que hay stock suficiente
                $producto = $this->modeloProducto->find($data['producto_id']);
                if ($producto['producto_stock'] < $data['movimiento_cantidad']) {
                    $data['error'][] = 'Stock insuficiente. Stock actual: ' . $producto['producto_stock'];
                } else {
                    unset($data['error']);
                    unset($data['productos']);

                    $resultado = $this->modelo->create($data);

                    if ($resultado) {
                        refresh('/movimientos');
                    } else {
                        $data['error'][] = 'Error al registrar la salida';
                    }
                }
            }
        }

        $this->view('movimientos/salida', $data);
    }

    /**
     * Método historial - Ver historial de un producto
     */
    public function historial($producto_id) {
        $data = $this->modelo->porProducto($producto_id);
        $producto = $this->modeloProducto->find($producto_id);
        $this->view('movimientos/historial', ['movimientos' => $data, 'producto' => $producto]);
    }

    /**
     * Método validarMovimiento
     */
    public function validarMovimiento($data) {
        $error = [];

        if (empty($data['producto_id'])) {
            $error[] = 'Debe seleccionar un producto';
        }

        if (empty($data['movimiento_cantidad']) || $data['movimiento_cantidad'] <= 0) {
            $error[] = 'La cantidad debe ser mayor a 0';
        }

        if (empty($data['movimiento_precio_unitario']) || $data['movimiento_precio_unitario'] <= 0) {
            $error[] = 'El precio unitario debe ser mayor a 0';
        }

        return $error;
    }
}
