<?php
require_once __DIR__.'/config/conexion.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    // Buscar usuario por correo
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = :correo");
    $stmt->execute([':correo' => $usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Guardar datos de sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre']  = $user['nombre'];
        $_SESSION['rol']     = $user['rol'];

        // Redirigir según rol
        header('Location: dashboard.php');
        exit;
    } else {
        $errors[] = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - KEO BARBERY</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: 'Montserrat', sans-serif; 
    }

    body { 
      background-color: #111; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      height: 100vh; 
      color: #fff; 
      overflow: hidden; 
    }
    .login-container { 
      background: #1c1c1c; 
      padding: 80px; 
      border-radius: 12px; 
      box-shadow: 0 8px 40px rgba(0,0,0,0.6); 
      text-align: center; width: 100%; 
      max-width: 800px; 
      position: relative; 
      z-index: 2; 
    }
    .login-container h2 { 
      margin-bottom: 40px; 
      color: #f1c40f; 
      font-size: 2.5rem; 
    }
    form { 
      display: flex; 
      flex-direction: column; 
      align-items: center; 
    }
    form input[type="text"], 
    form input[type="password"] { 
      width: 80%; max-width: 450px; 
      padding: 12px; margin: 8px 0; 
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
      transition: 0.3s; }
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
    .sparkle { position: absolute; width: 6px; height: 6px; background: #f1c40f; border-radius: 50%; pointer-events: none; animation: fadeOut 0.8s forwards; box-shadow: 0 0 8px #f1c40f, 0 0 15px #f1c40f; }
    @keyframes fadeOut { to { opacity: 0; transform: scale(0); } }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Iniciar Sesión</h2>

    <?php if ($errors): ?>
      <div class="alert">
        <ul><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <input type="text" name="usuario" placeholder="Correo electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <input type="submit" value="Ingresar">
    </form>

    <div class="extra-links">
      <a href="register.php">¿No tienes cuenta? Regístrate</a>
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
