<?php
// pages/cliente/get_disponibilidad.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$barbero_id = $_GET['barbero_id'] ?? null;

if (!$barbero_id) {
    echo json_encode(['error' => 'Falta barbero_id']);
    exit;
}

// 🔹 Disponibilidad general del barbero
$stmt = $pdo->prepare("
    SELECT dia_semana, hora_inicio, hora_fin
    FROM disponibilidades
    WHERE barbero_id = :barbero
");
$stmt->execute([':barbero' => $barbero_id]);
$disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Citas ya ocupadas (solo activas)
$citasStmt = $pdo->prepare("
    SELECT fecha, hora
    FROM citas
    WHERE barbero_id = :barbero
      AND estado IN ('pendiente', 'confirmada', 'reprogramada')
    ORDER BY fecha, hora
");
$citasStmt->execute([':barbero' => $barbero_id]);
$citas = $citasStmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Enviar respuesta combinada
echo json_encode([
    'disponibilidad' => $disponibilidad,
    'ocupadas' => $citas
]);
exit;
?>
