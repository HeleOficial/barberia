<?php
require_once __DIR__ . '/../../config/conexion.php';

$barbero_id = $_GET['barbero_id'] ?? null;
if (!$barbero_id) {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT c.fecha, c.hora, c.estado, u.nombre AS cliente
  FROM citas c
  JOIN usuarios u ON c.cliente_id = u.id
  WHERE c.barbero_id = :barbero
  ORDER BY c.fecha, c.hora
");
$stmt->execute([':barbero' => $barbero_id]);
$citas = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($citas);
