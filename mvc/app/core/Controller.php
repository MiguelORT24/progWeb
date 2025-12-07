<?php
/**
 * 
 * Clase base para controladores, para no escribir tanto codigo
 */

class Controller {
    
    public function view($view, $data = []){
        $path = APPROOT . '/views/' . $view . '.php';
        if (!file_exists($path)){
            // manejar una forma "agradable" de mostrar errores
            die("Vista {$view} no existe. ");
        } 
            require_once $path;
    }

/**
 * @param string $model debe iniciar con mayuscula
 */


    public function model($model){
        $path = APPROOT . '/models/' . ucwords($model) . '.php';
        if (!file_exists($path)){
            // manejar una forma "agradable" de mostrar errores
            die("Modelo {$model} no existe. ");
        } 
            require_once $path;
            return new $model;

    }
}