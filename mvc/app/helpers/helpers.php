<?php
/**
 * Helper de Permisos
 * Funciones para verificar permisos de usuario basado en roles
 */

/**
 * Verificar si el usuario es Administrador
 */
function esAdmin() {
    return ($_SESSION['usuario_rol'] ?? 'ADMIN') === 'ADMIN';
}

/**
 * Verificar si el usuario es de Almacén
 */
function esAlmacen() {
    return ($_SESSION['usuario_rol'] ?? 'ADMIN') === 'ALMACEN';
}

/**
 * Verificar si el usuario es Lector
 */
function esLector() {
    return ($_SESSION['usuario_rol'] ?? 'ADMIN') === 'LECTOR';
}

/**
 * Verificar si puede crear (Admin o Almacén)
 */
function puedeCrear() {
    return esAdmin() || esAlmacen();
}

/**
 * Verificar si puede confirmar (solo Admin)
 */
function puedeConfirmar() {
    return esAdmin();
}

/**
 * Verificar si puede editar (solo Admin)
 */
function puedeEditar() {
    return esAdmin();
}

/**
 * Verificar si puede eliminar (solo Admin)
 */
function puedeEliminar() {
    return esAdmin();
}

/**
 * Verificar si puede gestionar maestros (solo Admin)
 */
function puedeGestionarMaestros() {
    return esAdmin();
}

/**
 * Verificar si está autenticado
 */
function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Requerir autenticación - redirige a login si no está autenticado
 */
function requerirAuth() {
    if (!estaAutenticado()) {
        redirect('login');
        exit;
    }
}

/**
 * Requerir permiso específico - redirige con mensaje si no tiene permiso
 */
function requerirPermiso($permiso, $mensaje = 'No tienes permisos para realizar esta acción', $redirigirA = null) {
    if (!$permiso) {
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = 'warning';
        
        if ($redirigirA) {
            redirect($redirigirA);
        } else {
            redirect('home/dashboard');
        }
        exit;
    }
}
