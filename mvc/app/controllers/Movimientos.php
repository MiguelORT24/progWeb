<?php

class Movimientos extends Controller {
    private $movimientoModel;
    private $loteModel;

    public function __construct() {
        requerirAuth();
        $this->movimientoModel = $this->model('Movimiento');
        $this->loteModel = $this->model('InventarioLote');
    }

    /**
     * Historial de salidas
     */
    public function index() {
        $data = [
            'titulo' => 'Salidas de inventario',
            'movimientos' => $this->movimientoModel->salidas()
        ];

        $this->view('movimientos/index', $data);
    }

    /**
     * Registrar entrada
     */
    public function entrada() {
        $this->registrar('entrada');
    }

    /**
     * Registrar salida
     */
    public function salida() {
        $this->registrar('salida');
    }

    private function registrar(string $tipo) {
        $lotes = $this->loteModel->all();
        $data = [
            'titulo' => ucfirst($tipo) . ' de inventario',
            'accion' => ucfirst($tipo),
            'tipo' => $tipo,
            'lotes' => $lotes,
            'error' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['id_lote'] = $_POST['id_lote'] ?? null;
            $data['cantidad'] = (int)($_POST['cantidad'] ?? 0);
            $data['motivo'] = trim($_POST['motivo'] ?? '');

            $data['error'] = $this->validar($data);

            if (empty($data['error'])) {
                try {
                    $this->movimientoModel->create([
                        'id_lote' => $data['id_lote'],
                        'cantidad' => $data['cantidad'],
                        'motivo' => $data['motivo'],
                        'tipo' => $tipo,
                        'id_usuario' => $_SESSION['usuario_id'] ?? null
                    ]);
                    $_SESSION['mensaje'] = 'Movimiento registrado correctamente';
                    $_SESSION['tipo_mensaje'] = 'success';
                    redirect('movimientos');
                } catch (Exception $e) {
                    $data['error'][] = $e->getMessage();
                }
            }
        }

        $this->view('movimientos/registrar', $data);
    }

    private function validar(array $data): array {
        $errores = [];
        if (empty($data['id_lote'])) {
            $errores[] = 'Debe seleccionar un lote';
        }
        if ($data['cantidad'] <= 0) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        }
        return $errores;
    }
}
