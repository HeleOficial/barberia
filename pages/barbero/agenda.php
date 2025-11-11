<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

// Obtener el ID del barbero logueado
$barbero_id = $_SESSION['user_id'];

// Consultar citas del barbero
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
    <div class="alert alert-info text-center">No tienes citas agendadas.</div>
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
              <?php
                $estado = strtolower($c['estado']);
                $clase = match($estado) {
                  'confirmada' => 'bg-success',
                  'pendiente' => 'bg-warning',
                  'rechazada', 'cancelada' => 'bg-danger',
                  'reprogramada' => 'bg-secondary',
                  'atendida' => 'bg-primary',
                  'no_asistio' => 'bg-dark',
                  default => 'bg-light text-dark'
                };
              ?>
              <tr>
                <td><?= htmlspecialchars($c['fecha']) ?></td>
                <td><?= htmlspecialchars($c['hora']) ?></td>
                <td><?= htmlspecialchars($c['cliente']) ?></td>
                <td><?= htmlspecialchars($c['servicio']) ?></td>
                <td>
                  <span class="badge <?= $clase ?>">
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $c['estado']))) ?>
                  </span>
                </td>
                <td>
                  <?php if (in_array($estado, ['pendiente', 'reprogramada'])): ?>
                    <div class="d-flex justify-content-center gap-2">
                      <button class="btn btn-success btn-sm" onclick="accionCita(<?= $c['id'] ?>, 'aceptar')">Aceptar</button>
                      <button class="btn btn-danger btn-sm" onclick="accionCita(<?= $c['id'] ?>, 'rechazar')">Rechazar</button>
                      <button class="btn btn-primary btn-sm" onclick="abrirModalPosponer(<?= $c['id'] ?>)">Posponer</button>
                    </div>
                  <?php elseif ($estado === 'confirmada'): ?>
                    <div class="d-flex justify-content-center gap-2">
                      <button class="btn btn-primary btn-sm" onclick="accionCita(<?= $c['id'] ?>, 'atendida')">Atendida</button>
                      <button class="btn btn-dark btn-sm" onclick="accionCita(<?= $c['id'] ?>, 'no_asistio')">No asistió</button>
                    </div>
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

<!-- 🔹 MODAL PARA POSPONER CITA -->
<div class="modal fade" id="modalPosponer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="bi bi-calendar-plus"></i> Reprogramar cita</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formPosponer">
          <input type="hidden" name="cita_id" id="cita_id_modal">

          <div class="mb-3">
            <label for="nueva_fecha" class="form-label">Nueva fecha</label>
            <input type="date" class="form-control" id="nueva_fecha" name="nueva_fecha" required>
          </div>

          <div class="mb-3">
            <label for="nueva_hora" class="form-label">Nueva hora</label>
            <input type="time" class="form-control" id="nueva_hora" name="nueva_hora" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-warning" id="btnGuardarPosponer">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

<script>
async function accionCita(id, accion) {
  const data = new FormData();
  data.append('cita_id', id);
  data.append('accion', accion);

  try {
    const resp = await fetch('accion_cita.php', { method: 'POST', body: data });
    const result = await resp.json();
    alert(result.msg);
    if (result.status === 'ok') location.reload();
  } catch (error) {
    console.error(error);
    alert("❌ Error al procesar la acción.");
  }
}

// 🔹 Abrir modal de posponer
function abrirModalPosponer(id) {
  document.getElementById('cita_id_modal').value = id;
  const modal = new bootstrap.Modal(document.getElementById('modalPosponer'));
  modal.show();
}

// 🔹 Guardar posposición
document.getElementById('btnGuardarPosponer').addEventListener('click', async () => {
  const form = document.getElementById('formPosponer');
  const data = new FormData(form);
  data.append('accion', 'posponer');

  try {
    const resp = await fetch('accion_cita.php', { method: 'POST', body: data });
    const result = await resp.json();
    alert(result.msg);
    if (result.status === 'ok') location.reload();
  } catch (error) {
    alert("❌ Error al reprogramar la cita.");
  }
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
