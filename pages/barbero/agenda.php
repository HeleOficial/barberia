<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

$barbero_id = $_SESSION['user_id'];
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
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<h3 class="text-center mb-4"><i class="bi bi-calendar-check"></i> Mi Agenda</h3>

<div class="alert alert-info text-center">Cita no encontrada o sin permisos.</div>

<?php if (empty($citas)): ?>
  <div class="alert alert-info">No tienes citas agendadas.</div>
<?php else: ?>
  <div class="card">
    <div class="card-header bg-dark text-white fw-bold">Próximas citas asignadas</div>
    <div class="card-body">
      <table class="table table-striped align-middle">
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
                <span class="badge <?= $clase ?>"><?= htmlspecialchars($c['estado']) ?></span>
              </td>
              <td>
                <?php if (in_array($estado, ['pendiente', 'reprogramada'])): ?>
                  <form class="accion-cita-form d-inline" method="post">
                    <input type="hidden" name="cita_id" value="<?= $c['id'] ?>">
                    <button name="accion" value="aceptar" class="btn btn-success btn-sm">Aceptar</button>
                    <button name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                    <button name="accion" value="posponer" class="btn btn-primary btn-sm">Posponer</button>
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

<script>
document.querySelectorAll('.accion-cita-form').forEach(form => {
  form.addEventListener('submit', async e => {
    e.preventDefault();
    const data = new FormData(form);
    const resp = await fetch('accion_cita.php', { method: 'POST', body: data });
    const result = await resp.json();
    alert(result.msg);
    if (result.status === 'ok') location.reload();
  });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
