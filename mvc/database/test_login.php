#!/usr/bin/env php
<?php
require_once __DIR__ . '/../app/config/config.inc.php';

$email = $argv[1] ?? 'admin@inventario.com';
$password = $argv[2] ?? 'Admin123!';

// Conexión directa con el mismo DSN que usa el framework
$dsn = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, DBUSER, DBPWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    fwrite(STDERR, "[CONEXION] " . $e->getMessage() . PHP_EOL);
    exit(1);
}

$stmt = $pdo->prepare('SELECT id_usuario, nombre, email, contrasena FROM usuario WHERE email = :email');
$stmt->execute([':email' => $email]);
$row = $stmt->fetch();

if (!$row) {
    echo "[LOGIN] No se encontró el email {$email}\n";
    exit(2);
}

$ok = password_verify($password, $row['contrasena']);
echo "[LOGIN] email={$email} id={$row['id_usuario']} match=" . ($ok ? 'SI' : 'NO') . PHP_EOL;
if (!$ok) {
    echo "[DEBUG] Hash en BD: {$row['contrasena']}\n";
}
