<?php
// dashboard.php
require_once __DIR__.'/includes/auth.php';
require_login();

include 'includes/header.php';
?>
<div class="row">
  <div class="col-md-12">
    <h1>Bienvenido, <?=htmlspecialchars($_SESSION['nombre'])?></h1>
    <p>Rol: <strong><?=htmlspecialchars($_SESSION['rol'])?></strong></p>

    <div class="card">
      <div class="card-body">
        <p>Usa el menú superior para navegar por las opciones disponibles según tu rol.</p>
      </div>
    </div>
  </div><?php
// dashboard.php
require_once __DIR__.'/includes/auth.php';
require_login();

// OPCIONAL: redirige automáticamente al panel de cada rol
// (Descomenta si quieres que el dashboard redirija directamente)
 switch ($_SESSION['rol']) {
   case 'admin':
     header('Location: pages/admin/usuarios.php');
     exit;
   case 'barbero':
     header('Location: pages/barbero/agenda.php');
     exit;
   case 'cliente':
     header('Location: pages/cliente/reservar.php');
     exit;
 }

include 'includes/header.php';
?>

<div class="row">
  <div class="col-md-12 text-center">
    <h1>Bienvenido, <?=htmlspecialchars($_SESSION['nombre'])?></h1>
    <p>Rol: <strong><?=htmlspecialchars($_SESSION['rol'])?></strong></p>

    <div class="card mt-4 shadow-sm">
      <div class="card-body">
        <p>Usa el menú superior para navegar por las opciones disponibles según tu rol.</p>
        <p>Recuerda cerrar sesión cuando termines.</p>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

</div>

<?php include 'includes/footer.php'; ?>
