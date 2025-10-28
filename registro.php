<?php
require_once __DIR__.'/config/conexion.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$nombre) $errors[] = "Nombre es requerido.";
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";
    if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    if ($password !== $password2) $errors[] = "Las contraseñas no coinciden.";

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo");
    $stmt->execute([':correo' => $correo]);
    if ($stmt->fetch()) $errors[] = "El correo ya está registrado.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $inst = $pdo->prepare("INSERT INTO usuarios (nombre, correo, telefono, password, rol) VALUES (:nombre, :correo, :telefono, :password, 'cliente')");
        $inst->execute([
            ':nombre'=>$nombre, ':correo'=>$correo, ':telefono'=>$telefono, ':password'=>$hash
        ]);
        header('Location: login.php');
        exit;
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <h2>Registro - Cliente</h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach;?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" action="registro.php">
      <div class="mb-3">
        <label class="form-label">Nombre completo</label>
        <input required name="nombre" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label">Correo</label>
        <input required name="correo" type="email" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input name="telefono" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input required name="password" type="password" class="form-control" />
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar contraseña</label>
        <input required name="password2" type="password" class="form-control" />
      </div>
      <button class="btn btn-success">Crear cuenta</button>
      <a class="btn btn-link" href="index.php">Volver al login</a>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
