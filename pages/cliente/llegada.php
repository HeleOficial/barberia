<?php
// pages/cliente/llegada.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cita_id = $_POST['cita_id'] ?? null;
    if ($cita_id) {
        $upd = $pdo->prepare("UPDATE citas SET estado = 'en_espera' WHERE id = :id AND cliente_id = :cliente");
        $upd->execute([':id' => $cita_id, ':cliente' => $_SESSION['user_id']]);
        $msg = "✅ Se ha notificado tu llegada al barbero.";
        // Aquí podrías integrar envío de correo o WhatsApp al barbero
    } else {
        $msg = "⚠️ Debes seleccionar una cita.";
    }
}

$stmt = $pdo->prepare("
    SELECT c.id, c.fecha, c.hora, u.nombre AS barbero 
    FROM citas c 
    JOIN usuarios u ON c.barbero_id = u.id 
    WHERE c.cliente_id = :cliente AND c.estado IN ('confirmada','pendiente') 
    ORDER BY c.fecha, c.hora
");
$stmt->execute([':cliente' => $_SESSION['user_id']]);
$citas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-warning text-center fw-bold">
      📍 Notificar Llegada
    </div>
    <div class="card-body bg-light">
      
      <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <?php if (empty($citas)): ?>
        <div class="alert alert-secondary text-center shadow-sm">
          No tienes citas pendientes o confirmadas.
        </div>
      <?php else: ?>
        <form method="post" class="mx-auto" style="max-width: 500px;">
          <div class="mb-4">
            <label class="form-label fw-semibold">Selecciona tu cita:</label>
            <select name="cita_id" class="form-select border-dark" required>
              <option value="">Seleccione una cita...</option>
              <?php foreach ($citas as $c): ?>
                <option value="<?= $c['id'] ?>">
                  <?= htmlspecialchars("{$c['fecha']} {$c['hora']} - {$c['barbero']}") ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button class="btn btn-warning text-dark fw-bold w-100 shadow-sm">
            🚶‍♂️ Notificar llegada
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
