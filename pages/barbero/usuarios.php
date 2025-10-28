<?php
// pages/admin/usuarios.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../../config/conexion.php';

$msg = null;

// Crear barbero desde panel admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_barbero'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($nombre && $correo && $pass) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("
            INSERT INTO usuarios (nombre, correo, telefono, password, rol) 
            VALUES (:n, :c, '', :p, 'barbero')
        ");
        $ins->execute([':n' => $nombre, ':c' => $correo, ':p' => $hash]);
        $msg = "✅ Barbero creado correctamente.";
    } else {
        $msg = "⚠️ Por favor completa todos los campos.";
    }
}

// Consultar usuarios registrados
$users = $pdo->query("
    SELECT id, nombre, correo, rol, creado_en 
    FROM usuarios 
    ORDER BY creado_en DESC
")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">👥 Gestión de Usuarios</h2>

  <?php if ($msg): ?>
    <div class="alert alert-info text-center fw-bold"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Crear barbero -->
  <div class="card shadow-lg border-0 mb-4">
    <div class="card-header bg-dark text-warning fw-bold">
      ➕ Crear nuevo barbero
    </div>
    <div class="card-body bg-light">
      <form method="post" class="row g-3">
        <input type="hidden" name="create_barbero" value="1">
        <div class="col-md-4">
          <label class="form-label fw-bold">Nombre completo</label>
          <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold">Correo electrónico</label>
          <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Contraseña</label>
          <input type="password" name="password" class="form-control" placeholder="********" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button class="btn btn-warning w-100 fw-bold">Crear</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de usuarios -->
  <div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-warning fw-bold">
      Usuarios registrados
    </div>
    <div class="card-body bg-light">
      <table class="table table-striped align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Fecha de creación</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="5" class="text-muted">No hay usuarios registrados aún.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td>
                  <span class="badge bg-<?= 
                    $u['rol'] === 'admin' ? 'danger' : 
                    ($u['rol'] === 'barbero' ? 'primary' : 'secondary') 
                  ?>">
                    <?= ucfirst(htmlspecialchars($u['rol'])) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($u['creado_en'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
