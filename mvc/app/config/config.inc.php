<?php 
/**
 * config.inc.php el inc es sugerido para los archivos que no se utilizan directamente 
 * @author yo 
 * @fecha de creación 14/10/2025
 * leer sober .env y PHPDoc 
 */

#constantes de URL 
define ('URLROOT', 'http://inventario'); //cual es mi dominio
define('APPROOT', __DIR__ . '/../'); //directorio de la app
define('VENDORS', __DIR__ . '/../../vendor');
// ó 
//define('APPROOT', dirname(dirname(__FILE__)));

require_once APPROOT . 'helpers/helpers.inc.php';

//Definir constantes de bd 
define ("DBUSER", 'root');
define ("DBPWD", '');
define ("DBDRIVER", 'mysql'); 
define ("DBHOST", 'localhost');
define ("DBNAME", 'inventario');

//carga de clases core
//que pasa si son 80 clases a cargar?
spl_autoload_register(function($nombreClase){
    require_once APPROOT . 'core/' . $nombreClase . '.php';

});
 //require_once APPROOT . '/core/Routes.php';

