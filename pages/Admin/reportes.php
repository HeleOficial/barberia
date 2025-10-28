<?php
// pages/admin/reportes.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../../config/conexion.php';

// Consultas
$q = $pdo->query("SELECT estado, COUNT(*) AS total FROM citas GROUP BY estado")->fetchAll();
$top_barberos = $pdo->query("SELECT u.nombre, COUNT(*) AS total FROM citas c JOIN usuarios u ON c.barbero_id=u.id GROUP BY c.barbero_id ORDER BY total DESC LIMIT 5")->fetchAll();
$clientes_freq = $pdo->query("SELECT u.nombre, COUNT(*) AS total FROM citas c JOIN usuarios u ON c.cliente_id=u.id GROUP BY c.cliente_id ORDER BY total DESC LIMIT 5")->fetchAll();
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
  <h2 class="text-center text-warning mb-4">📊 Reportes del Sistema</h2>

  <div class="row">
    <!-- Citas por estado -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Citas por estado
        </div>
        <div class="card-body bg-light">
          <?php if ($q): ?>
            <table class="table table-bordered table-hover mb-0">
              <thead class="table-dark text-center">
                <tr><th>Estado</th><th>Total</th></tr>
              </thead>
              <tbody>
                <?php foreach($q as $r): ?>
                  <tr>
                    <td><?=htmlspecialchars(ucfirst($r['estado']))?></td>
                    <td class="text-center"><?=htmlspecialchars($r['total'])?></td>
                  </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="text-muted">No hay citas registradas.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Barberos más solicitados -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Barberos más solicitados
        </div>
        <div class="card-body bg-light">
          <?php if ($top_barberos): ?>
            <table class="table table-bordered table-hover mb-0">
              <thead class="table-dark text-center">
                <tr><th>Barbero</th><th>Citas</th></tr>
              </thead>
              <tbody>
                <?php foreach($top_barberos as $b): ?>
                  <tr>
                    <td><?=htmlspecialchars($b['nombre'])?></td>
                    <td class="text-center"><?=htmlspecialchars($b['total'])?></td>
                  </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="text-muted">No hay registros de barberos.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Clientes frecuentes -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-dark text-warning fw-bold">
          Clientes más frecuentes
        </div>
        <div class="card-body bg-light">
          <?php if ($clientes_freq): ?>
            <table class="table table-bordered table-hover mb-0">
              <thead class="table-dark text-center">
                <tr><th>Cliente</th><th>Citas</th></tr>
              </thead>
              <tbody>
                <?php foreach($clientes_freq as $c): ?>
                  <tr>
                    <td><?=htmlspecialchars($c['nombre'])?></td>
                    <td class="text-center"><?=htmlspecialchars($c['total'])?></td>
                  </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="text-muted">No hay clientes registrados.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
