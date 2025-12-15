<?php
// Archivo de diagnóstico temporal. Elimínalo al terminar.
$root = realpath(__DIR__);
echo "<pre>";
echo "Ruta del script: " . __FILE__ . PHP_EOL;
echo "DocumentRoot según Apache: " . $_SERVER['DOCUMENT_ROOT'] . PHP_EOL;
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? '') . PHP_EOL;

require_once __DIR__ . '/../app/config/config.inc.php';
echo "APPROOT: " . APPROOT . PHP_EOL;
echo "URLROOT: " . URLROOT . PHP_EOL;
echo "DBHOST: " . DBHOST . PHP_EOL;
echo "DBPORT: " . DBPORT . PHP_EOL;
echo "DBNAME: " . DBNAME . PHP_EOL;

try {
    $dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DBUSER, DBPWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $count = $pdo->query("SELECT COUNT(*) AS c FROM usuario")->fetch()['c'] ?? 'n/a';
    $user = $pdo->query("SELECT id_usuario,email FROM usuario ORDER BY id_usuario LIMIT 1")->fetch();
    echo "Conexion BD: OK. Total usuarios: {$count}" . PHP_EOL;
    echo "Primer usuario: " . json_encode($user) . PHP_EOL;
} catch (Exception $e) {
    echo "Conexion BD: ERROR => " . $e->getMessage() . PHP_EOL;
}

echo "</pre>";
