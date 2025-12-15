#!/usr/bin/env php
<?php
require_once __DIR__ . '/../app/config/config.inc.php';

$email = $argv[1] ?? 'admin@inventario.com';
$password = $argv[2] ?? 'Admin123!';
$nombre = $argv[3] ?? 'Administrador';

try {
    $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
    $pdo = new PDO($dsn, DBUSER, DBPWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    fwrite(STDERR, "No se pudo conectar a la BD: " . $e->getMessage() . PHP_EOL);
    exit(1);
}

// Verificar si el usuario ya existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :email");
$stmt->execute([':email' => $email]);
$existing = $stmt->fetch();

if ($existing) {
    fwrite(STDOUT, "Ya existe un usuario con el email {$email} (id {$existing['id_usuario']}). No se insertó nada.\n");
    exit(0);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO usuario (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)");
$stmt->execute([
    ':nombre' => $nombre,
    ':email' => $email,
    ':contrasena' => $hash,
]);

$id = $pdo->lastInsertId();
fwrite(STDOUT, "Usuario creado con id {$id}. Email: {$email} | Password: {$password}\n");
