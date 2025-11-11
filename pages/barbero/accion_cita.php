<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

header('Content-Type: application/json');

$barbero_id = $_SESSION['user_id'];
$cita_id = $_POST['cita_id'] ?? null;
$accion = $_POST['accion'] ?? null;
$nueva_fecha = $_POST['nueva_fecha'] ?? null;
$nueva_hora  = $_POST['nueva_hora'] ?? null;

if (empty($cita_id) || empty($accion)) {
    echo json_encode(['status' => 'error', 'msg' => 'Cita no válida.']);
    exit;
}

// 🔹 Mapeo de acciones a estados
$estado = match ($accion) {
    'aceptar'    => 'confirmada',
    'rechazar'   => 'rechazada',
    'posponer'   => 'reprogramada',
    'atendida'   => 'atendida',
    'no_asistio' => 'no_asistio',
    default      => null
};

if (!$estado) {
    echo json_encode(['status' => 'error', 'msg' => 'Acción inválida.']);
    exit;
}

// 🔹 Verificar que la cita pertenece al barbero logueado
$stmt = $pdo->prepare("SELECT id FROM citas WHERE id = :id AND barbero_id = :barbero LIMIT 1");
$stmt->execute([':id' => $cita_id, ':barbero' => $barbero_id]);
$cita = $stmt->fetch();

if (!$cita) {
    echo json_encode(['status' => 'error', 'msg' => 'Cita no encontrada o sin permisos.']);
    exit;
}

// 🔹 Si es posponer, validar nueva fecha y hora
if ($accion === 'posponer') {
    if (!$nueva_fecha || !$nueva_hora) {
        echo json_encode(['status' => 'error', 'msg' => 'Debe indicar nueva fecha y hora.']);
        exit;
    }

    $check = $pdo->prepare("
        SELECT COUNT(*) FROM citas
        WHERE barbero_id = :barbero AND fecha = :fecha AND hora = :hora
          AND estado NOT IN ('cancelada', 'rechazada')
    ");
    $check->execute([':barbero' => $barbero_id, ':fecha' => $nueva_fecha, ':hora' => $nueva_hora]);

    if ($check->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Ese horario ya está ocupado.']);
        exit;
    }

    $upd = $pdo->prepare("
        UPDATE citas
        SET estado = :estado, fecha = :fecha, hora = :hora
        WHERE id = :id
    ");
    $upd->execute([':estado' => $estado, ':fecha' => $nueva_fecha, ':hora' => $nueva_hora, ':id' => $cita_id]);

    echo json_encode(['status' => 'ok', 'msg' => '✅ Cita reprogramada correctamente.']);
    exit;
}

// 🔹 Para aceptar, rechazar, atender o marcar no asistió
$upd = $pdo->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
$upd->execute([':estado' => $estado, ':id' => $cita_id]);

echo json_encode(['status' => 'ok', 'msg' => '✅ Estado de cita actualizado.']);
exit;
?>
