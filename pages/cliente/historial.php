<?php
// pages/cliente/historial.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$cliente_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.*, s.nombre AS servicio, u.nombre AS barbero 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    JOIN usuarios u ON c.barbero_id = u.id 
    WHERE c.cliente_id = :cliente 
    ORDER BY c.fecha DESC
");
$stmt->execute([':cliente' => $cliente_id]);
$citas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">💈 Historial de Citas</h2>

  <?php if (empty($citas)): ?>
    <div class="alert alert-info text-center shadow-sm">
      No tienes citas registradas todavía.
    </div>
  <?php else: ?>
    <div class="card shadow-lg border-0">
      <div class="card-header bg-dark text-warning fw-bold text-center">
        Historial completo de tus citas
      </div>
      <div class="card-body bg-light">
        <table class="table table-hover text-center align-middle">
          <thead class="table-dark">
            <tr>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Barbero</th>
              <th>Servicio</th>
              <th>Estado</th>
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
                  <?php
                    $estado = strtolower($c['estado']);
                    $badgeClass = match($estado) {
                      'completada' => 'success',
                      'cancelada' => 'danger',
                      'pendiente' => 'warning',
                      default => 'secondary'
                    };
                  ?>
                  <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
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
