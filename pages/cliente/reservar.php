<?php
// pages/cliente/reservar.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

// Obtener servicios y barberos
$servicios = $pdo->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll();
$barberos = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'barbero' ORDER BY nombre")->fetchAll();

$success = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barbero_id = $_POST['barbero_id'] ?? '';
    $servicio_id = $_POST['servicio_id'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    // Validaciones básicas
    if (!$barbero_id || !$servicio_id || !$fecha || !$hora) {
        $errors[] = "⚠️ Todos los campos son obligatorios.";
    }

    // Validar formato de fecha y hora
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $errors[] = "⚠️ Fecha inválida.";
    }
    if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
        $errors[] = "⚠️ Hora inválida.";
    }

    if (empty($errors)) {
        // Comprobar si ya existe una cita para ese barbero en esa fecha y hora
        $check = $pdo->prepare("
            SELECT COUNT(*) 
            FROM citas 
            WHERE barbero_id = :barbero 
              AND fecha = :fecha 
              AND hora = :hora 
              AND estado NOT IN ('cancelada', 'rechazada')
        ");
        $check->execute([
            ':barbero' => $barbero_id,
            ':fecha'   => $fecha,
            ':hora'    => $hora
        ]);

        if ($check->fetchColumn() > 0) {
            $errors[] = "❌ El horario seleccionado ya está ocupado.";
        } else {
            // Insertar nueva cita
            $insert = $pdo->prepare("
                INSERT INTO citas (cliente_id, barbero_id, servicio_id, fecha, hora, estado)
                VALUES (:cliente, :barbero, :servicio, :fecha, :hora, 'pendiente')
            ");
            $insert->execute([
                ':cliente'  => $_SESSION['user_id'],
                ':barbero'  => $barbero_id,
                ':servicio' => $servicio_id,
                ':fecha'    => $fecha,
                ':hora'     => $hora
            ]);

            $success = "✅ Cita registrada correctamente. Espera confirmación del barbero.";
        }
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
  <div class="col-md-8">
    <h3 class="text-warning mb-3"><i class="bi bi-calendar-plus"></i> Reservar cita</h3>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" id="formReserva">
      <div class="mb-3">
        <label class="form-label fw-bold">Barbero</label>
        <select name="barbero_id" id="barbero_id" class="form-select" required>
          <option value="">Seleccione un barbero...</option>
          <?php foreach ($barberos as $b): ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Servicio</label>
        <select name="servicio_id" class="form-select" required>
          <option value="">Seleccione un servicio...</option>
          <?php foreach ($servicios as $s): ?>
            <option value="<?= $s['id'] ?>">
              <?= htmlspecialchars($s['nombre']) ?> (<?= $s['duracion_min'] ?> min)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Fecha</label>
        <input type="date" name="fecha" id="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Hora</label>
        <input type="time" name="hora" id="hora" class="form-control" required>
        <small class="text-muted">Las horas ya ocupadas se marcarán al elegir barbero.</small>
      </div>

      <button class="btn btn-warning fw-bold"><i class="bi bi-check2-circle"></i> Reservar</button>
    </form>

    <hr>
    <h5><i class="bi bi-clock-history"></i> Horarios ocupados:</h5>
    <table id="tabla-ocupados" class="table table-bordered text-center mt-2">
      <thead class="table-dark">
        <tr><th>Fecha</th><th>Hora</th></tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
document.getElementById('barbero_id').addEventListener('change', async function() {
  const id = this.value;
  const tabla = document.querySelector('#tabla-ocupados tbody');
  const horaInput = document.getElementById('hora');

  tabla.innerHTML = "";
  horaInput.value = "";
  horaInput.disabled = false;

  if (!id) return;

  const res = await fetch('get_disponibilidad.php?barbero_id=' + id);
  const data = await res.json();

  if (!data || !Array.isArray(data.ocupadas)) {
    tabla.innerHTML = `<tr><td colspan="2" class="text-muted">No hay datos disponibles</td></tr>`;
    return;
  }

  // 🔹 Mostrar citas ocupadas
  if (data.ocupadas.length === 0) {
    tabla.innerHTML = `<tr><td colspan="2" class="text-success">El barbero no tiene citas ocupadas</td></tr>`;
  } else {
    data.ocupadas.forEach(c => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${c.fecha}</td><td class="text-danger fw-bold">${c.hora}</td>`;
      tabla.appendChild(tr);
    });
  }

  // 🔹 Bloquear horas ocupadas del input (solo si coincide fecha)
  document.getElementById('fecha').addEventListener('change', () => {
    const fechaSeleccionada = document.getElementById('fecha').value;
    const ocupadas = data.ocupadas.filter(c => c.fecha === fechaSeleccionada).map(c => c.hora);

    horaInput.addEventListener('input', () => {
      if (ocupadas.includes(horaInput.value)) {
        alert("⚠️ Esa hora ya está ocupada. Por favor, elige otra.");
        horaInput.value = "";
      }
    });
  });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
