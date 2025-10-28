<?php
// pages/admin/usuarios.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../../config/conexion.php';

$msg = null;

// Crear nuevo usuario (solo barbero o admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_usuario'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $rol = $_POST['rol'] ?? 'barbero';
    $password = $_POST['password'] ?? '';

    if ($nombre && $correo && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO usuarios (nombre, correo, telefono, password, rol) VALUES (:n, :c, :t, :p, :r)");
        $ins->execute([':n'=>$nombre, ':c'=>$correo, ':t'=>$telefono, ':p'=>$hash, ':r'=>$rol]);
        $msg = "✅ Usuario creado correctamente.";
    } else {
        $msg = "⚠️ Todos los campos son obligatorios.";
    }
}

// Obtener usuarios
$usuarios = $pdo->query("SELECT id, nombre, correo, telefono, rol, creado_en FROM usuarios ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">👥 Gestión de Usuarios</h2>

  <?php if ($msg): ?>
    <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Crear usuario -->
  <div class="card shadow-lg border-0 mb-4">
    <div class="card-header bg-dark text-warning fw-bold">
      Agregar nuevo usuario
    </div>
    <div class="card-body bg-light">
      <form method="post">
        <input type="hidden" name="create_usuario" value="1" />
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">Nombre completo</label>
            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Carlos Pérez">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Correo electrónico</label>
            <input type="email" name="correo" class="form-control" required placeholder="correo@ejemplo.com">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">Teléfono</label>
            <input type="text" name="telefono" class="form-control" placeholder="Ej: 3001234567">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Contraseña</label>
            <input type="password" name="password" class="form-control" required placeholder="********">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Rol</label>
            <select name="rol" class="form-select">
              <option value="barbero">Barbero</option>
              <option value="admin">Administrador</option>
            </select>
          </div>
        </div>
        <div class="text-end mt-3">
          <button class="btn btn-warning px-4 fw-bold">➕ Crear usuario</button>
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
            <th>Teléfono</th>
            <th>Rol</th>
            <th>Fecha de registro</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($usuarios)): ?>
            <tr><td colspan="6" class="text-muted">No hay usuarios registrados aún.</td></tr>
          <?php else: ?>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><?= htmlspecialchars($u['telefono']) ?></td>
                <td><span class="badge bg-<?= $u['rol'] === 'admin' ? 'danger' : ($u['rol'] === 'barbero' ? 'primary' : 'secondary') ?>">
                  <?= htmlspecialchars($u['rol']) ?>
                </span></td>
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
