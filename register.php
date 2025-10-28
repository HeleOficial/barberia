<?php
// register.php
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

    // validar correo unico
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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - KEO BARBERY</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: 'Montserrat', sans-serif; }
    body { 
      background-color: #111; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      height: 100vh; color: #fff; 
      overflow: hidden; 
    }
    .register-container { 
      background: #1c1c1c; 
      padding: 60px 80px; 
      border-radius: 12px; 
      box-shadow: 0 8px 40px rgba(0,0,0,0.6); 
      text-align: center; width: 100%; 
      max-width: 900px; position: relative; 
      z-index: 2; 
    }
    .register-container h2 { 
      margin-bottom: 30px; 
      color: #f1c40f; 
      font-size: 2.5rem; 
    }
    form { 
      display: flex; 
      flex-direction: column; 
      align-items: center; 
    }
    form input[type="text"], 
    form input[type="email"], 
    form input[type="password"] {
      width: 80%; 
      max-width: 450px; 
      padding: 12px; 
      margin: 8px 0; 
      border: none;
      border-radius: 8px; 
      background: #333; 
      color: #fff; 
      font-size: 1rem;
    }
    form input[type="submit"] {
      width: 200px; 
      padding: 12px; 
      background: #f1c40f; 
      border: none;
      border-radius: 8px; 
      cursor: pointer; 
      font-size: 1rem; 
      font-weight: bold;
      margin-top: 15px; 
      transition: 0.3s;
    }
    form input[type="submit"]:hover {
      background: #d4a017; 
      box-shadow: 0 0 20px #f1c40f; 
      transform: scale(1.05);
    }
    .extra-links { 
      margin-top: 15px; 
      font-size: 0.9rem; 
    }
    .extra-links a { 
      color: #f1c40f; 
      text-decoration: none; 
      display: block; 
      margin: 5px 0; 
    }
    .extra-links a:hover { 
      text-decoration: underline; 
    }
    .alert {
      background-color: #d9534f; 
      color: #fff; 
      padding: 10px; 
      border-radius: 5px;
      margin-bottom: 20px; 
      text-align: left; 
      width: 80%; 
      max-width: 450px;
    }
    .sparkle { 
      position: absolute; 
      width: 6px; 
      height: 6px; 
      background: #f1c40f; 
      border-radius: 50%; 
      pointer-events: none; 
      animation: fadeOut 0.8s forwards; 
      box-shadow: 0 0 8px #f1c40f, 0 0 15px #f1c40f; 
    }
    @keyframes fadeOut { 
      to { 
        opacity: 0; 
        transform: 
          scale(0); } }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Crear cuenta</h2>

    <?php if ($errors): ?>
      <div class="alert">
        <ul>
          <?php foreach($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach;?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php">
      <input type="text" name="nombre" placeholder="Nombre completo" value="<?=htmlspecialchars($_POST['nombre'] ?? '')?>" required>
      <input type="email" name="correo" placeholder="Correo electrónico" value="<?=htmlspecialchars($_POST['correo'] ?? '')?>" required>
      <input type="text" name="telefono" placeholder="Teléfono" value="<?=htmlspecialchars($_POST['telefono'] ?? '')?>">
      <input type="password" name="password" placeholder="Contraseña" required>
      <input type="password" name="password2" placeholder="Confirmar contraseña" required>
      <input type="submit" value="Registrarse">
    </form>

    <div class="extra-links">
      <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
      <a href="index.php">Volver al inicio</a>
    </div>
  </div>

  <script>
    document.addEventListener("mousemove", function(e) {
      let sparkle = document.createElement("div");
      sparkle.classList.add("sparkle");
      sparkle.style.left = e.pageX + "px";
      sparkle.style.top = e.pageY + "px";
      document.body.appendChild(sparkle);
      setTimeout(() => { sparkle.remove(); }, 800);
    });
  </script>
</body>
</html>
