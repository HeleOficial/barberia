<?php
// pages/admin/servicios.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../../config/conexion.php';

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_servicio'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $desc = trim($_POST['descripcion'] ?? '');
    $dur = (int)($_POST['duracion'] ?? 30);
    $precio = (float)($_POST['precio'] ?? 0);

    if ($nombre) {
        $ins = $pdo->prepare("INSERT INTO servicios (nombre, descripcion, duracion_min, precio) VALUES (:n,:d,:dur,:p)");
        $ins->execute([':n'=>$nombre, ':d'=>$desc, ':dur'=>$dur, ':p'=>$precio]);
        $msg = "✅ Servicio creado correctamente.";
    } else {
        $msg = "⚠️ El nombre del servicio es obligatorio.";
    }
}

$servicios = $pdo->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll();
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">💈 Gestión de Servicios</h2>

  <?php if ($msg): ?>
    <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Formulario creación -->
  <div class="card shadow-lg border-0 mb-4">
    <div class="card-header bg-dark text-warning fw-bold">
      Agregar nuevo servicio
    </div>
    <div class="card-body bg-light">
      <form method="post">
        <input type="hidden" name="create_servicio" value="1" />
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label fw-bold">Nombre</label>
            <input name="nombre" class="form-control" placeholder="Ej: Corte clásico" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Duración (min)</label>
            <input name="duracion" type="number" min="5" class="form-control" placeholder="30" required>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Precio ($)</label>
            <input name="precio" type="number" step="0.01" min="0" class="form-control" placeholder="15000" required>
          </div>
          <div class="col-md-5">
            <label class="form-label fw-bold">Descripción</label>
            <input name="descripcion" class="form-control" placeholder="Detalle del servicio">
          </div>
        </div>
        <div class="text-end mt-3">
          <button class="btn btn-warning px-4 fw-bold">➕ Crear servicio</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de servicios -->
  <div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-warning fw-bold">
      Servicios registrados
    </div>
    <div class="card-body bg-light">
      <table class="table table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Nombre</th>
            <th>Duración</th>
            <th>Precio</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($servicios)): ?>
            <tr><td colspan="4" class="text-muted">No hay servicios registrados aún.</td></tr>
          <?php else: ?>
            <?php foreach ($servicios as $s): ?>
              <tr>
                <td><?= htmlspecialchars($s['nombre']) ?></td>
                <td><?= htmlspecialchars($s['duracion_min']) ?> min</td>
                <td>$<?= number_format($s['precio'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($s['descripcion']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
