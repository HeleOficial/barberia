<?php
// pages/barbero/accion_cita.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

// 🔹 Aseguramos que la respuesta sea JSON
header('Content-Type: application/json');

// 🔹 Datos del formulario
$barbero_id = $_SESSION['user_id'];  // ✅ Este es el ID del barbero logueado
$citas_id = $_POST['cita_id'] ?? null;
$accion = $_POST['accion'] ?? null;

// 🔹 Validar datos
if (empty($citas_id) || empty($accion)) {
    echo json_encode(['status' => 'error', 'msg' => 'Cita no válida.']);
    exit;
}

// 🔹 Determinar nuevo estado
$estado = match ($accion) {
    'aceptar'  => 'confirmada',
    'rechazar' => 'rechazada',
    'posponer' => 'reprogramada',
    default    => null
};

if (!$estado) {
    echo json_encode(['status' => 'error', 'msg' => 'Acción inválida.']);
    exit;
}

// 🔹 Verificar que la cita pertenezca al barbero logueado
$stmt = $pdo->prepare("SELECT id FROM citas WHERE id = :id AND barbero_id = :barbero LIMIT 1");
$stmt->execute([':id' => $cita_id, ':barbero' => $barbero_id]);
$cita = $stmt->fetch();

if (!$cita) {
    echo json_encode(['status' => 'error', 'msg' => 'Cita no encontrada o sin permisos.']);
    exit;
}

// 🔹 Actualizar estado de la cita
$upd = $pdo->prepare("UPDATE citas SET estado = :estado WHERE id = :id");
$upd->execute([':estado' => $estado, ':id' => $cita_id]);

echo json_encode(['status' => 'ok', 'msg' => '✅ Cita actualizada correctamente.']);
exit;
?>
