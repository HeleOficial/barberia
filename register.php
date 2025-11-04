<?php
require_once __DIR__ . '/config/conexion.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validaciones básicas
    if (!$nombre || !$correo || !$password || !$confirm) {
        $errors[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo electrónico inválido.";
    } elseif ($password !== $confirm) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    // Si todo está bien, intentar registrar
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo");
        $stmt->execute([':correo' => $correo]);
        if ($stmt->fetch()) {
            $errors[] = "El correo ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, correo, telefono, password, rol) 
                VALUES (:n, :c, :t, :p, 'cliente')
            ");
            $stmt->execute([
                ':n' => $nombre,
                ':c' => $correo,
                ':t' => $telefono,
                ':p' => $hash
            ]);
            header("Location: login.php?registro=ok");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro | Barbería</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #1a1a1a, #2b2b2b);
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      position: relative;
    }
    .sparkle {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      pointer-events: none;
      background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
      background-size: 40px 40px;
      animation: sparkleMove 20s linear infinite;
    }
    @keyframes sparkleMove {
      from { background-position: 0 0; }
      to { background-position: 100% 100%; }
    }
    .register-container {
      background: rgba(0, 0, 0, 0.85);
      padding: 2rem;
      border-radius: 15px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 0 25px rgba(255, 215, 0, 0.3);
      z-index: 1;
    }
    .register-container h2 {
      text-align: center;
      color: #f0c14b;
      margin-bottom: 1.5rem;
      font-weight: bold;
    }
    .form-control {
      background: #2a2a2a;
      border: none;
      color: #fff;
    }
    .form-control:focus {
      background: #333;
      border: 1px solid #f0c14b;
      box-shadow: 0 0 10px rgba(240, 193, 75, 0.5);
    }
    .btn-warning {
      background-color: #f0c14b;
      border: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .btn-warning:hover {
      background-color: #ffcf57;
      transform: scale(1.05);
    }
    .link-login {
      display: block;
      text-align: center;
      margin-top: 1rem;
      color: #f0c14b;
      text-decoration: none;
    }
    .link-login:hover {
      text-decoration: underline;
    }
    .alert {
      background-color: rgba(255, 0, 0, 0.1);
      border: 1px solid #f66;
      color: #f88;
    }
  </style>
</head>
<body>
  <div class="sparkle"></div>
  <div class="register-container">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" name="correo" id="correo" class="form-control" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Opcional" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="confirm" class="form-label">Confirmar contraseña</label>
        <input type="password" name="confirm" id="confirm" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-warning w-100">Registrarme</button>
    </form>

    <a href="login.php" class="link-login">¿Ya tienes cuenta? Inicia sesión</a>
  </div>
</body>
</html>
