<?php

class Proveedores extends Controller {

    private $modelo;

    public function __construct() {
        $this->modelo = $this->model('Proveedor');
    }

    /**
     * Método index
     */
    public function index() {
        $data = $this->modelo->conConteoProductos();
        $this->view('proveedores/index', $data);
    }

    /**
     * Método create
     */
    public function create() {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden crear proveedores', 'proveedores');
        
        $metodo = $_SERVER['REQUEST_METHOD'];
        $data = [
            'accion' => 'Crear',
            'error' => []
        ];

        if ($metodo == 'POST') {
            $data = $_POST;
            $data['error'] = $this->validarProveedor($data);

            if (empty($data['error'])) {
                unset($data['error']);
                $resultado = $this->modelo->create($data);

                if ($resultado) {
                    refresh('/proveedores');
                } else {
                    $data['error'][] = 'Error al crear el proveedor';
                }
            }
        }

        $this->view('proveedores/create', $data);
    }

    /**
     * Método edit
     */
    public function edit($id) {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden editar proveedores', 'proveedores');
        
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo == 'POST') {
            $data = $_POST;
            unset($data['id']);
            $data['error'] = $this->validarProveedor($data);

            if (empty($data['error'])) {
                unset($data['error']);
                $resultado = $this->modelo->update($id, $data);

                if ($resultado) {
                    refresh('/proveedores');
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

        $this->view('proveedores/create', $data);
    }

    /**
     * Método destroy
     */
    public function destroy($id) {
        requerirAuth();
        requerirPermiso(puedeGestionarMaestros(), 'Solo administradores pueden eliminar proveedores', 'proveedores');
        
        $resultado = $this->modelo->delete($id);

        if ($resultado) {
            refresh('/proveedores');
        } else {
            refresh('/proveedores');
        }
    }

    /**
     * Método validarProveedor
     */
    public function validarProveedor($data) {
        $error = [];

        if (empty($data['proveedor_nombre'])) {
            $error[] = 'El nombre del proveedor está vacío';
        }

        if (!empty($data['proveedor_email']) && !filter_var($data['proveedor_email'], FILTER_VALIDATE_EMAIL)) {
            $error[] = 'El formato del correo no es válido';
        }

        return $error;
    }
}
