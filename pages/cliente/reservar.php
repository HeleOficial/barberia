<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll();
$barberos = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'barbero' ORDER BY nombre")->fetchAll();

$success = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barbero_id = $_POST['barbero_id'] ?? null;
    $servicio_id = $_POST['servicio_id'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $hora = $_POST['hora'] ?? null;

    if (!$barbero_id || !$servicio_id || !$fecha || !$hora) $errors[] = "Todos los campos son requeridos.";

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE barbero_id = :barbero AND fecha = :fecha AND hora = :hora AND estado != 'cancelada'");
        $check->execute([':barbero'=>$barbero_id, ':fecha'=>$fecha, ':hora'=>$hora]);
        if ($check->fetchColumn() > 0) {
            $errors[] = "El horario ya está ocupado.";
        } else {
            $ins = $pdo->prepare("INSERT INTO citas (cliente_id, barbero_id, servicio_id, fecha, hora, estado) VALUES (:cliente, :barbero, :serv, :fecha, :hora, 'pendiente')");
            $ins->execute([
                ':cliente'=>$_SESSION['user_id'],
                ':barbero'=>$barbero_id,
                ':serv'=>$servicio_id,
                ':fecha'=>$fecha,
                ':hora'=>$hora
            ]);
            $success = "Cita registrada. Espera confirmación del barbero.";
        }
    }
}
include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
  <div class="col-md-8">
    <h3>Reservar cita</h3>

    <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-danger"><ul><?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul></div><?php endif; ?>

    <form method="post" id="formReserva">
      <div class="mb-3">
        <label>Barbero</label>
        <select name="barbero_id" id="barbero_id" class="form-select" required>
          <option value="">Seleccione...</option>
          <?php foreach($barberos as $b): ?>
            <option value="<?=$b['id']?>"><?=htmlspecialchars($b['nombre'])?></option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="mb-3">
        <label>Servicio</label>
        <select name="servicio_id" class="form-select" required>
          <option value="">Seleccione...</option>
          <?php foreach($servicios as $s): ?>
            <option value="<?=$s['id']?>"><?=htmlspecialchars($s['nombre'])?> (<?=$s['duracion_min']?> min)</option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="mb-3">
        <label>Fecha</label>
        <input type="date" name="fecha" id="fecha" class="form-control" required />
      </div>

      <div class="mb-3">
        <label>Hora</label>
        <input type="time" name="hora" id="hora" class="form-control" required />
        <small class="text-muted">Horas ocupadas se marcarán en rojo al elegir barbero.</small>
      </div>

      <button class="btn btn-primary">Reservar</button>
    </form>

    <hr>
    <div id="calendario"></div>
  </div>
</div>

<script>
document.getElementById('barbero_id').addEventListener('change', async function() {
  const id = this.value;
  const calendario = document.getElementById('calendario');
  calendario.innerHTML = "";

  if (!id) return;

  const res = await fetch('get_disponibilidad.php?barbero_id=' + id);
  const data = await res.json();

  if (data.length === 0) {
    calendario.innerHTML = "<div class='alert alert-success'>El barbero no tiene citas ocupadas.</div>";
    return;
  }

  let html = "<h5>Horas ocupadas:</h5><table class='table table-sm table-bordered'><thead><tr><th>Fecha</th><th>Hora</th></tr></thead><tbody>";
  data.forEach(c => {
    html += `<tr><td>${c.fecha}</td><td style="color:red">${c.hora}</td></tr>`;
  });
  html += "</tbody></table>";
  calendario.innerHTML = html;
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
