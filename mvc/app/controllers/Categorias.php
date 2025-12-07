<?php

class Categorias extends Controller {

    private $modelo;

    public function __construct() {
        $this->modelo = $this->model('Categoria');
    }

    /**
     * Método index
     */
    public function index() {
        $data = $this->modelo->conConteoProductos();
        $this->view('categorias/index', $data);
    }

    /**
     * Método create
     */
    public function create() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $data = [
            'accion' => 'Crear',
            'error' => []
        ];

        if ($metodo == 'POST') {
            $data = $_POST;
            $data['error'] = $this->validarCategoria($data);

            if (empty($data['error'])) {
                unset($data['error']);
                $resultado = $this->modelo->create($data);

                if ($resultado) {
                    refresh('/categorias');
                } else {
                    $data['error'][] = 'Error al crear la categoría';
                }
            }
        }

        $this->view('categorias/create', $data);
    }

    /**
     * Método edit
     */
    public function edit($id) {
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo == 'POST') {
            $data = $_POST;
            unset($data['id']);
            $data['error'] = $this->validarCategoria($data);

            if (empty($data['error'])) {
                unset($data['error']);
                $resultado = $this->modelo->update($id, $data);

                if ($resultado) {
                    refresh('/categorias');
                } else {
                    $data['error'][] = 'No se realizaron cambios';
                }
            }
        }

        if (empty($data['error'])) {
            $data = $this->modelo->find($id);
        }

        $data['accion'] = 'Editar';
        $data['error'] ?? [];

        $this->view('categorias/create', $data);
    }

    /**
     * Método destroy
     */
    public function destroy($id) {
        $resultado = $this->modelo->delete($id);

        if ($resultado) {
            refresh('/categorias');
        } else {
            // Redirigir con mensaje de error
            refresh('/categorias');
        }
    }

    /**
     * Método validarCategoria
     */
    public function validarCategoria($data) {
        $error = [];

        if (empty($data['categoria_nombre'])) {
            $error[] = 'El nombre de la categoría está vacío';
        }

        return $error;
    }
}
