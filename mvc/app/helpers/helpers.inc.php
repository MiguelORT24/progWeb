<?php
/*
* Scripts de ayuda genérica
*/

session_start();

/**
 * Redireccionar a una URL
 */
function redirect($page) {
    header('Location: ' . URLROOT . '/' . $page);
    exit();
}

/**
 * Refresh / redirigir
 */
function refresh($Location){
    header("Location: $Location");
    exit();
}

/**
 * Verificar si está logueado
 */
function estaLogueado(){
    return (!empty ($_SESSION["usuario_nombre"]));
}

/**
 * Debug y detener ejecución
 */
function dd(){
    $args = func_get_args();
    call_user_func_array('dump', $args);
    die();
}

/**
 * Debug sin detener
 */
function d(){
    $args = func_get_args();
    call_user_func_array('dump', $args);
}

/**
 * Función dump
 */
function dump($datos){
    if (is_array($datos)) {
        echo '<pre>';
            var_dump($datos);
        echo '</pre>';
        return;
    }else if (is_object($datos)) {
        echo '<pre>';
            print_r($datos);
        echo '</pre>';
        return; 
    } else {
        echo "=====> ";
        var_dump($datos);
        echo " <=====";
        return; 
    }
}

/**
 * Mostrar mensajes flash
 */
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

/**
 * Sanitizar string
 */
function sanitize($string) {
    return filter_var($string, FILTER_SANITIZE_STRING);
}