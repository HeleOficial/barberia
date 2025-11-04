<?php
require 'config/conexion.php';
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM usuarios");
$row = $stmt->fetch();
echo "Conexión correcta. Usuarios en BD: " . $row['total'];
?>
