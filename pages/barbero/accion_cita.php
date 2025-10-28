<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

header('Content-Type: application/json');

$barbero_id = $_SESSION['user_id'];
$cita_id = $_POST['cita_id'] ?? null;
$accion = $_POST['accion'] ?? null;

if (!$cita_id || !$accion) {
    echo json_encode(['status' => 'error', 'msg' => 'Cita no válida.']);
    exit;
}

// Determinar nuevo estado según la acción
switch ($accion) {
    case 'aceptar':
        $estado = 'confirmada';
        break;
    case 'rechazar':
        $estado = 'rechazada';
        break;
    case 'posponer':
        $estado = 'reprogramada';
        break;
    default:
        echo json_encode(['status' => 'error', 'msg' => 'Acción inválida.']);
        exit;
}

// Verificar que la cita pertenezca al barbero logueado
$stmt = $pdo->prepare("SELECT id FROM citas WHERE id = :id AND barbero_id = :barbero LIMIT 1");
$stmt->execute([':id' => $cita_id, ':barbero' => $barbero_id]);
$cita = $stmt->fetch();

if (!$cita) {
    echo json_encode(['status' => 'error', 'msg' => 'Cita no encontrada o sin permisos.']);
    exit;
}

// Actualizar estado
$upd = $pdo->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
$upd->execute([':estado' => $estado, ':id' => $cita_id]);

echo json_encode(['status' => 'ok', 'msg' => 'Cita actualizada correctamente.']);
exit;
