<?php


class Usuarios extends Controller{

    private $modelo;
    public function __construct(){
        $this->modelo = $this->model('Usuario');
    }
    /**
    * metodo index
    */
    public function index(){
    //    $data = ['titulo' => 'Usuarios'];
        $data = $this->modelo->all();
        $this->view('usuarios/index', $data);
    }
    /**
     * metodo edit
     * //@param var id
     */
    
    public function edit($id){
        /**
         * dos partes
         * llamada por GET
         */
        // d($_SERVER);
            $metodo = $_SERVER ['REQUEST_METHOD'];
            if($metodo == 'POST'){

            /**
             * tomar los datos
             * validar los datos
             * enviar a cambio
             */
            //dd($_POST);
            $data=$_POST;

            //dd($_FILES);
            if (!empty($_FILES ['usuario_foto']['tmp_name'])){
                $data['usuario_foto']=file_get_contents($_FILES['usuario_foto']['tmp_name']);
            }else {
                unset($data['usuario_foto']);
            }
            unset($data['id']);
            // validar el resto
            //dd($data);
            $data['error'] = $this->validarUsuario($data,true,$id);
            // continuamos después, ya es despues
            if(empty($data['error'])){
            // if (!count($data['error'])){}
            // ir al update
            // depurar $data
                unset($data['error']);
                unset($data['conf_password']);
                if(empty($data['usuario_password'])){
                    unset($data['usuario_password']);
                } else {
                    // ???????????????? para hashearlo
                    $data['usuario_password']=password_hash($data['usuario_password'],PASSWORD_DEFAULT);
                }
                $resultado=$this->modelo->update($id,$data);
                //d($resultado);
                if($resultado){
                    // redirigir a la vista index
                    refresh('/usuarios');
                    // header('Location:/');

                } else {
                    // ultima revision de errores
                    $data['error'][]= 'Ops, algo esta mal.... ( dio actualiacion sin cambios)';
                }

            }

        }


        if(empty($data['error'])){
        $data = $this->modelo->find($id);
        }
        $data['accion' ] = 'editar';
        $data['error']??[]; 
         //temporal 
        $this->view('usuarios/create',$data);

    }
    /**
     * metodo validarUsuario
     * //@param datos, true, id
     */
    public function validarUsuario ($data,$edit=false, $id=null){
        $error=[];
        if($edit){
            if (empty($data['usuario_nombre'])){
                $error[]='El nombre esta vacio';
            }
            if (empty($data['usuario_nivel'])){
                $error[]='El nivel esta vacio';
            }
            if (empty($data['usuario_email'])){
                $error[]='El correo esta vacio';
            }
            if (!filter_var($data['usuario_email'],FILTER_VALIDATE_EMAIL)){
                $error[]='El formato de correo no es válido';
            }

            // falta seccion de correo no repetido
            if (!empty($data['usuario_password'])){
                if(($data['conf_password'] ?? '') !== $data['usuario_password']){
                    $error[]='La conf de password no es igual';
                }
            } 
            
            
        } else {
            if (empty($data['usuario_nombre'])){
                $error[]='El nombre esta vacio';
            }
            if (empty($data['usuario_nivel'])){
                $error[]='El nivel esta vacio';
            }
            if (empty($data['usuario_email'])){
                $error[]='El correo esta vacio';
            }
            if (!filter_var($data['usuario_email'],FILTER_VALIDATE_EMAIL)){
                $error[]='El formato de correo no es válido';
            }

            // falta seccion de correo no repetido
            if (empty($data['usuario_password'])){
                $error[]='El Password está vacío';
            }
            if(($data['conf_password'] ?? '') !== $data['usuario_password']){
                $error[]='La conf de password no es igual';
            }
            

        }
        return $error;
    }

    /**
     * metodo login
     */

    public function login(){
        $data['error']=[];

        $metodo = $_SERVER ['REQUEST_METHOD'];
        if($metodo == 'POST'){
            // dd($_POST);
            $data = $_POST;
            $logueado = $this->modelo->login($data['usuario_email'],$data['usuario_password']);
            unset($data);

            //dd($resultado);

            if($logueado){
                $_SESSION['usuario_nombre'] = $logueado['usuario_nombre'];
                $_SESSION['usuario_nivel'] = $logueado['usuario_nivel'];

                refresh('/');
            }else{
                $data['error'][] = 'Credeniciales no validas';
            }

        }//fin del _post
        $this->view('/auth/login', $data);   

    }

    /**
     * 
     * metodo logout
     */
    public function logout(){
        // session_destroy();
        unset($_SESSION['usuario_nombre']);
        unset($_SESSION['usuario_nivel']);
        //refresh('/');
        session_destroy();
        refresh('auth/login');
    
    
    }

    /**
     * Create 
     */
    public function create(){
        
            $metodo = $_SERVER ['REQUEST_METHOD'];
            $data=[
                'accion' => 'Crear',
                'error' =>[]
            ];
            if($metodo == 'POST'){

            $data=$_POST;

            //dd($_FILES);
            if (!empty($_FILES ['usuario_foto']['tmp_name'])){
                $data['usuario_foto']=file_get_contents($_FILES['usuario_foto']['tmp_name']);
            }else {
                unset($data['usuario_foto']);
            }
            
            // validar el resto
            //dd($data);
            $data['error'] = $this->validarUsuario($data);
            // continuamos después, ya es despues
            if(empty($data['error'])){
            // if (!count($data['error'])){}
            // ir al update
            // depurar $data
                unset($data['error']);
                unset($data['conf_password']);
                // ???????????????? para hashearlo
                $data['usuario_password']=password_hash($data['usuario_password'],PASSWORD_DEFAULT);
                
                $resultado=$this->modelo->create($data);
                //d($resultado);
                if($resultado){
                    // redirigir a la vista index
                    refresh('/usuarios');
                    // header('Location:/');
                } else {
                    // ultima revision de errores
                    $data['error'][]= 'Ops, algo esta mal.... ( dio actualiacion sin cambios)';
                }
            }
        }

        $this->view('usuarios/create',$data);

    }

    /**
     * delete
     */

    public function destroy($id){
        $resultado = $this->modelo->delete($id);
        if($resultado){
            refresh('/usuarios');
        }else{
            //solo para uso más adelante
            $data['error'][]= 'Algo esta mal....';
        }
    }

    /**
     * metodo json
     */

    public function json(){
        
        // $sql= "SELECT id,usuario_nombre,usuario_email,usuario_nivel FROM usuarios";
        $data=$this->modelo->all();

        $this->view('/usuarios/json', $data);
        // header('Content-Type:application/json');
        // header('Content-Disposition:attachment;filename=usuarios.json');
        // echo json_encode($data);
    }

    /**
     * metodo imprimir
     */

    public function imprimir(){

        $data=$this->modelo->all();
        $this->view('/usuarios/fpdf', $data);

    }

}//Fin de clase 