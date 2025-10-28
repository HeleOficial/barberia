<?php
// pages/cliente/cancelar.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$cliente_id = $_SESSION['user_id'];
$msg = null;

// Cancelar cita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cita_id = $_POST['cita_id'] ?? null;
    if ($cita_id) {
        $upd = $pdo->prepare("
            UPDATE citas 
            SET estado = 'cancelada' 
            WHERE id = :id AND cliente_id = :cliente
        ");
        $upd->execute([':id' => $cita_id, ':cliente' => $cliente_id]);
        $msg = "✅ Cita cancelada correctamente.";
    }
}

// Consultar citas activas
$stmt = $pdo->prepare("
    SELECT c.*, s.nombre AS servicio, u.nombre AS barbero 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    JOIN usuarios u ON c.barbero_id = u.id 
    WHERE c.cliente_id = :cliente AND c.estado != 'cancelada' 
    ORDER BY c.fecha, c.hora
");
$stmt->execute([':cliente' => $cliente_id]);
$citas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">💈 Mis próximas citas</h2>

  <?php if ($msg): ?>
    <div class="alert alert-success text-center fw-bold"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if (empty($citas)): ?>
    <div class="alert alert-info text-center shadow-sm">
      No tienes citas activas en este momento.
    </div>
  <?php else: ?>
    <div class="card shadow-lg border-0">
      <div class="card-header bg-dark text-warning fw-bold">
        Citas activas
      </div>
      <div class="card-body bg-light">
        <table class="table table-striped align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Barbero</th>
              <th>Servicio</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($citas as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['fecha']) ?></td>
                <td><?= htmlspecialchars($c['hora']) ?></td>
                <td><?= htmlspecialchars($c['barbero']) ?></td>
                <td><?= htmlspecialchars($c['servicio']) ?></td>
                <td>
                  <form method="post" onsubmit="return confirm('¿Deseas cancelar esta cita?')" style="display:inline;">
                    <input type="hidden" name="cita_id" value="<?= htmlspecialchars($c['id']) ?>">
                    <button class="btn btn-sm btn-danger fw-bold">
                      ❌ Cancelar
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
