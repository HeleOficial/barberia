<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="/barberia/dashboard.php">Barbería</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">

        <?php if (isset($_SESSION['rol'])): ?>
          <?php if ($_SESSION['rol'] === 'cliente'): ?>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/cliente/reservar.php">Reservar</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/cliente/cancelar.php">Cancelar</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/cliente/historial.php">Historial</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/cliente/valorar.php">Valorar</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/cliente/confirmar_reprogramacion.php">Confirmar reprogramación</a></li>

          <?php elseif ($_SESSION['rol'] === 'barbero'): ?>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/barbero/agenda.php">Agenda</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/barbero/disponibilidad.php">Disponibilidad</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/barbero/historial.php">Historial</a></li>

          <?php elseif ($_SESSION['rol'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/admin/usuarios.php">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/admin/servicios.php">Servicios</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/admin/reportes.php">Reportes</a></li>
            <li class="nav-item"><a class="nav-link" href="/Barber%20System/pages/admin/configuracion.php">Configuración</a></li>
          <?php endif; ?>
        <?php endif; ?>

      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['nombre'])): ?>
          <li class="nav-item"><span class="nav-link">Hola, <?= htmlspecialchars($_SESSION['nombre']) ?> (<?= htmlspecialchars($_SESSION['rol']) ?>)</span></li>
          <li class="nav-item"><a class="nav-link" href="/Barber%20System/logout.php">Cerrar sesión</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/Barber%20System/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
