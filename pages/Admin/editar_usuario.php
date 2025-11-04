<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../../config/conexion.php';

// Obtener datos del usuario
if (!isset($_GET['id'])) {
  header("Location: usuarios.php");
  exit;
}
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
  echo "<script>alert('Usuario no encontrado'); window.location.href='usuarios.php';</script>";
  exit;
}

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $correo = $_POST['correo'];
  $telefono = $_POST['telefono'];
  $rol = $_POST['rol'];

  $update = $pdo->prepare("UPDATE usuarios SET nombre=?, correo=?, telefono=?, rol=? WHERE id=?");
  $update->execute([$nombre, $correo, $telefono, $rol, $id]);

  echo "<script>alert('Usuario actualizado correctamente'); window.location.href='usuarios.php';</script>";
  exit;
}
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">✏️ Editar Usuario</h2>

  <form method="POST" class="card p-4 shadow-lg">
    <div class="mb-3">
      <label>Nombre completo</label>
      <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
    </div>

    <div class="mb-3">
      <label>Correo electrónico</label>
      <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
    </div>

    <div class="mb-3">
      <label>Teléfono</label>
      <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
    </div>

    <div class="mb-3">
      <label>Rol</label>
      <select name="rol" class="form-select" required>
        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        <option value="barbero" <?= $usuario['rol'] === 'barbero' ? 'selected' : '' ?>>Barbero</option>
        <option value="cliente" <?= $usuario['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
      </select>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-warning">💾 Guardar cambios</button>
      <a href="usuarios.php" class="btn btn-secondary">⬅️ Volver</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
