#!/usr/bin/env php
<?php
/**
 * Script de respaldo y recuperación de la base de datos.
 *
 * Usos principales:
 *  - Crear respaldo:          php database/backup.php
 *  - Crear respaldo (15 días) php database/backup.php --keep-days=15
 *  - Listar respaldos:        php database/backup.php --list
 *  - Restaurar respaldo:      php database/backup.php --restore nombre_archivo.sql
 *
 * El script genera archivos .sql en database/backups/ y elimina
 * automáticamente lo que tenga más de N días (N=30 por defecto).
 */

require_once __DIR__ . '/../app/config/config.inc.php';

$options = getopt('', [
    'restore:',     // archivo a restaurar
    'list',         // listar respaldos
    'keep-days::',  // días a conservar (por defecto 30)
    'help'
]);

$keepDays = isset($options['keep-days']) ? (int)$options['keep-days'] : 30;
$backupDir = __DIR__ . '/backups';

if (isset($options['help'])) {
    mostrarAyuda();
    exit(0);
}

if (!is_dir($backupDir) && !mkdir($backupDir, 0775, true)) {
    fwrite(STDERR, "No se pudo crear el directorio de respaldos en: {$backupDir}\n");
    exit(1);
}

try {
    if (isset($options['list'])) {
        listarRespaldos($backupDir);
        exit(0);
    }

    if (isset($options['restore'])) {
        $archivo = $options['restore'];
        restaurarRespaldo($backupDir, $archivo);
        exit(0);
    }

    crearRespaldo($backupDir, $keepDays);
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "[ERROR] " . $e->getMessage() . "\n");
    exit(1);
}

/**
 * Genera un respaldo y aplica política de retención.
 */
function crearRespaldo(string $backupDir, int $keepDays): void
{
    $timestamp = date('Ymd_His');
    $nombreArchivo = sprintf('%s/inventario_%s.sql', rtrim($backupDir, '/'), $timestamp);

    $mysqldump = comandoDisponible('mysqldump');
    if (!$mysqldump) {
        throw new RuntimeException("No se encontró el binario mysqldump. Asegura que MySQL está instalado y en el PATH.");
    }

    $cmd = sprintf(
        '%s --host=%s --user=%s %s --databases %s --single-transaction --add-drop-database --no-tablespaces --result-file=%s',
        escapeshellcmd($mysqldump),
        escapeshellarg(DBHOST),
        escapeshellarg(DBUSER),
        prepararPassword(DBPWD),
        escapeshellarg(DBNAME),
        escapeshellarg($nombreArchivo)
    );

    ejecutarComando($cmd, 'No se pudo generar el respaldo.');

    limpiarRespaldosAntiguos($backupDir, $keepDays);

    fwrite(STDOUT, "[OK] Respaldo creado en: {$nombreArchivo}\n");
}

/**
 * Lista los respaldos disponibles ordenados por fecha.
 */
function listarRespaldos(string $backupDir): void
{
    $archivos = glob($backupDir . '/*.sql');
    sort($archivos);

    if (empty($archivos)) {
        fwrite(STDOUT, "No hay respaldos en {$backupDir}\n");
        return;
    }

    fwrite(STDOUT, "Respaldos en {$backupDir}:\n");
    foreach ($archivos as $archivo) {
        $pesoMb = number_format(filesize($archivo) / 1024 / 1024, 2);
        $fecha = date('Y-m-d H:i:s', filemtime($archivo));
        fwrite(STDOUT, " - " . basename($archivo) . " ({$pesoMb} MB, {$fecha})\n");
    }
}

/**
 * Restaura un respaldo específico.
 */
function restaurarRespaldo(string $backupDir, string $archivo): void
{
    $ruta = realpath("{$backupDir}/{$archivo}");
    if (!$ruta || !is_file($ruta)) {
        throw new RuntimeException("El archivo a restaurar no existe: {$archivo}");
    }

    $mysql = comandoDisponible('mysql');
    if (!$mysql) {
        throw new RuntimeException("No se encontró el binario mysql. Asegura que MySQL está instalado y en el PATH.");
    }

    $cmd = sprintf(
        '%s --host=%s --user=%s %s %s < %s',
        escapeshellcmd($mysql),
        escapeshellarg(DBHOST),
        escapeshellarg(DBUSER),
        prepararPassword(DBPWD),
        '--database=' . escapeshellarg(DBNAME),
        escapeshellarg($ruta)
    );

    ejecutarComando($cmd, 'No se pudo restaurar el respaldo.');

    fwrite(STDOUT, "[OK] Base restaurada desde: {$archivo}\n");
}

/**
 * Elimina respaldos con más días de los permitidos.
 */
function limpiarRespaldosAntiguos(string $backupDir, int $keepDays): void
{
    $limite = time() - ($keepDays * 86400);
    $archivos = glob($backupDir . '/*.sql');

    foreach ($archivos as $archivo) {
        if (filemtime($archivo) < $limite) {
            @unlink($archivo);
        }
    }
}

/**
 * Ejecuta un comando de shell y lanza excepción en error.
 */
function ejecutarComando(string $cmd, string $mensajeError): void
{
    exec($cmd, $output, $codigo);
    if ($codigo !== 0) {
        throw new RuntimeException($mensajeError . " Código: {$codigo}");
    }
}

/**
 * Devuelve la ruta completa de un comando si existe.
 */
function comandoDisponible(string $binario): ?string
{
    $ruta = trim(shell_exec("command -v " . escapeshellarg($binario)));
    return $ruta !== '' ? $ruta : null;
}

/**
 * Prepara el parámetro de contraseña para mysql/mysqldump.
 */
function prepararPassword(string $pwd): string
{
    return $pwd !== '' ? '--password=' . escapeshellarg($pwd) : '';
}

function mostrarAyuda(): void
{
    echo <<<TXT
Uso:
  php database/backup.php [--keep-days=30]
  php database/backup.php --list
  php database/backup.php --restore archivo.sql
  php database/backup.php --help

Opciones:
  --keep-days   Días de retención de respaldos (30 por defecto).
  --list        Muestra los respaldos disponibles.
  --restore     Restaura el respaldo indicado (.sql) sobre la BD configurada.
  --help        Muestra esta ayuda.

TXT;
}
