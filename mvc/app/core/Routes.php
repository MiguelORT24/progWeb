<?php
/**
 * Administracion de rutas 
 * en nuestro sistema existirá un dashboard 
 */

/**
//  * @return de controlador 
 */
class Routes{
    protected $controladorActual = 'Home'; 
    protected $metodoActual = 'index';
    protected $parametros = []; //array/();

    public function __construct(){
        //tome la url 
        //$url = $_GET['url']; ///regresa una cadena y no necesitamos cadena 
        
        $url = $this -> getUrl();
        
        //$_GET['url']; // cadena y no necesito una cadena, necesito un arreglo

        //d($url); // para debug o seguimiento

    ##CARGAR EL CONTROLADOR, DETERMINAR EL METODO
        if($url && file_exists(APPROOT . '/controllers/' . ucwords($url[0]) . '.php')){
            $this -> controladorActual = ucwords($url[0]);
            unset($url[0]);
        }

    // cargamos 
        require_once APPROOT . '/controllers/' . $this ->controladorActual . '.php';
    // instanciacion
        $this -> controladorActual = new $this -> controladorActual;

    # DETERMINAR EL METODO 
        if (isset($url[1]) && method_exists($this -> controladorActual, $url[1])) {
                $this -> metodoActual = $url[1];
                unset($url[1]);
        }

    # PARAMETROS ? aun quedan valores en $url?
        $this -> parametros = ($url) ? array_values($url) : [];

    # LLAMAR AL OBJETO/METODO
        call_user_func_array([$this -> controladorActual, $this -> metodoActual], $this -> parametros);


    } //Fin de construct 

    private function getUrl(){
        if (isset($_GET['url'])) {
            //Labores de limpieza
            $url = rtrim($_GET['url'], '/'); //elimina espacios en blanco o '/' (DIRECTORY_SEPARATOR)
            $url = filter_var($url, FILTER_SANITIZE_URL); //limpia la url de caracteres especiales 
            $url = explode('/', $url); //convierte la cadena en un arreglo 
            return $url;
        }//fin de if isset

    }// Fin de getUrl 

}//Fin de class