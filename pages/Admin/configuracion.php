<?php 
// pages/admin/configuracion.php 
require_once __DIR__ . '/../../includes/auth.php'; 
require_login(); 
require_role('admin'); 
include __DIR__ . '/../../includes/header.php'; 
?> 

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">⚙️ Configuración del Sistema</h2>

  <div class="row">
    <!-- Configuración General -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Parámetros Generales
        </div>
        <div class="card-body bg-light">
          <p>Aquí podrás ajustar información general del sistema, como el nombre de la barbería, logo, colores o mensajes de bienvenida.</p>
          <button class="btn btn-warning btn-sm">Editar Configuración</button>
        </div>
      </div>
    </div>

    <!-- Integraciones -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Integraciones
        </div>
        <div class="card-body bg-light">
          <p>Configura integraciones externas como WhatsApp, correos (SMTP) o recordatorios automáticos.</p>
          <button class="btn btn-warning btn-sm">Ver Integraciones</button>
        </div>
      </div>
    </div>

    <!-- Seguridad -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Seguridad
        </div>
        <div class="card-body bg-light">
          <p>Administra roles de usuarios, contraseñas y opciones de seguridad del sistema.</p>
          <button class="btn btn-warning btn-sm">Gestionar Seguridad</button>
        </div>
      </div>
    </div>

    <!-- Copias de seguridad -->
    <div class="col-md-6 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Copias de Seguridad
        </div>
        <div class="card-body bg-light">
          <p>Genera y descarga copias de seguridad de la base de datos y configuración del sistema.</p>
          <button class="btn btn-warning btn-sm">Crear Backup</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
