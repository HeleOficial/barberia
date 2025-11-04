<?php
// pages/barbero/agenda.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

// Obtener el ID del barbero logueado
$barbero_id = $_SESSION['user_id'];

// Consultar las citas asignadas a este barbero
$stmt = $pdo->prepare("
    SELECT c.*, 
           u.nombre AS cliente, 
           s.nombre AS servicio 
    FROM citas c 
    JOIN usuarios u ON c.cliente_id = u.id 
    JOIN servicios s ON c.servicio_id = s.id 
    WHERE c.barbero_id = :barbero 
    ORDER BY c.fecha, c.hora
");
$stmt->execute([':barbero' => $barbero_id]);
$citas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h3 class="text-center text-warning mb-4">
    <i class="bi bi-calendar-check"></i> Mi Agenda
  </h3>

  <?php if (empty($citas)): ?>
    <div class="alert alert-info text-center">
      No tienes citas agendadas.
    </div>
  <?php else: ?>
    <div class="card shadow-lg border-0">
      <div class="card-header bg-dark text-warning fw-bold">
        Próximas citas asignadas
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
              <th>Acciones</th>
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
                    $clase = match($estado) {
                      'confirmada' => 'bg-success',
                      'pendiente' => 'bg-warning',
                      'rechazada', 'cancelada' => 'bg-danger',
                      'reprogramada' => 'bg-secondary',
                      default => 'bg-light text-dark'
                    };
                  ?>
                  <span class="badge <?= $clase ?>">
                    <?= htmlspecialchars(ucfirst($c['estado'])) ?>
                  </span>
                </td>
                <td>
                  <?php if (in_array($estado, ['pendiente', 'reprogramada'])): ?>
                    <form class="accion-cita-form d-inline" method="post" action="accion_cita.php">
                      <input type="hidden" name="cita_id" value="<?= htmlspecialchars($c['id']) ?>">
                      <button type="submit" name="accion" value="aceptar" class="btn btn-success btn-sm">Aceptar</button>
                      <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                      <button type="submit" name="accion" value="posponer" class="btn btn-primary btn-sm">Posponer</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted">Sin acciones</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<script>
// Captura los formularios de acción (Aceptar, Rechazar, Posponer)
document.querySelectorAll('.accion-cita-form').forEach(form => {
  form.addEventListener('submit', async e => {
    e.preventDefault();
    const data = new FormData(form);
    const resp = await fetch(form.action, { method: 'POST', body: data });
    const result = await resp.json();
    alert(result.msg);
    if (result.status === 'ok') location.reload();
  });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
