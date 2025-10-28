<?php
// pages/barbero/historial.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

$barbero_id = $_SESSION['user_id'];

// Consultar citas atendidas por el barbero
$stmt = $pdo->prepare("
  SELECT c.*, u.nombre AS cliente, s.nombre AS servicio 
  FROM citas c 
  JOIN usuarios u ON c.cliente_id = u.id 
  JOIN servicios s ON c.servicio_id = s.id 
  WHERE c.barbero_id = :barbero 
  ORDER BY c.fecha DESC
");
$stmt->execute([':barbero' => $barbero_id]);
$citas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">📖 Historial de Citas Atendidas</h2>

  <?php if (empty($citas)): ?>
    <div class="alert alert-info text-center shadow-sm">
      Aún no has atendido ninguna cita.
    </div>
  <?php else: ?>
    <div class="card shadow-lg border-0">
      <div class="card-header bg-dark text-warning fw-bold">
        Registro de citas anteriores
      </div>
      <div class="card-body bg-light">
        <table class="table table-striped align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Cliente</th>
              <th>Servicio</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($citas as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['fecha']) ?></td>
                <td><?= htmlspecialchars($c['hora']) ?></td>
                <td><?= htmlspecialchars($c['cliente']) ?></td>
                <td><?= htmlspecialchars($c['servicio']) ?></td>
                <td>
                  <?php 
                    $estado = strtolower($c['estado']);
                    $badgeClass = match ($estado) {
                      'pendiente' => 'warning',
                      'confirmada', 'realizada' => 'success',
                      'cancelada' => 'danger',
                      default => 'secondary'
                    };
                  ?>
                  <span class="badge bg-<?= $badgeClass ?>">
                    <?= ucfirst(htmlspecialchars($c['estado'])) ?>
                  </span>
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
