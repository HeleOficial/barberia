<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$barbero_id = $_GET['barbero_id'] ?? null;

if (!$barbero_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT dia_semana, hora_inicio, hora_fin 
    FROM disponibilidades
    WHERE barbero_id = :barbero
");
$stmt->execute([':barbero' => $barbero_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
