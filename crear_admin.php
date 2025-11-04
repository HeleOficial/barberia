<?php
require_once __DIR__ . '/config/conexion.php';

$nombre = 'Administrador';
$correo = 'admin@barber.com';
$password = 'admin123';
$rol = 'admin';

// Encriptar contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar usuario
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, telefono, password, rol) VALUES (:n, :c, '', :p, :r)");
$stmt->execute([
  ':n' => $nombre,
  ':c' => $correo,
  ':p' => $hash,
  ':r' => $rol
]);

echo "✅ Usuario administrador creado correctamente.<br>";
echo "Correo: admin@barber.com<br>";
echo "Contraseña: admin123";
