<?php
// pages/barbero/valoraciones.php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

// ID del barbero logueado
$barbero_id = $_SESSION['user_id'];

// 🔹 Obtener promedio de calificación del barbero
$stmt_avg = $pdo->prepare("
    SELECT ROUND(AVG(puntuacion), 1) AS promedio, COUNT(*) AS total
    FROM valoraciones
    WHERE barbero_id = :barbero
");
$stmt_avg->execute([':barbero' => $barbero_id]);
$stats = $stmt_avg->fetch();
$promedio = $stats['promedio'] ?? 0;
$total_valoraciones = $stats['total'] ?? 0;

// 🔹 Obtener detalle de valoraciones
$stmt = $pdo->prepare("
    SELECT v.*, c.fecha, c.hora, s.nombre AS servicio, u.nombre AS cliente
    FROM valoraciones v
    JOIN citas c ON v.cita_id = c.id
    JOIN servicios s ON c.servicio_id = s.id
    JOIN usuarios u ON v.cliente_id = u.id
    WHERE v.barbero_id = :barbero
    ORDER BY v.creado_en DESC
");
$stmt->execute([':barbero' => $barbero_id]);
$valoraciones = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h3 class="text-center text-warning mb-4">
    <i class="bi bi-star-fill"></i> Valoraciones de mis clientes
  </h3>

  <?php if ($total_valoraciones == 0): ?>
    <div class="alert alert-info text-center">
      Aún no tienes valoraciones de tus clientes.
    </div>
  <?php else: ?>
    <div class="card shadow-lg border-0 mb-4">
      <div class="card-body text-center bg-dark text-white rounded">
        <h5 class="fw-bold">Promedio general</h5>
        <h1 class="display-4 text-warning"><?= htmlspecialchars($promedio) ?> ⭐</h1>
        <p><?= $total_valoraciones ?> valoración(es) recibida(s)</p>
      </div>
    </div>

    <div class="card shadow border-0">
      <div class="card-header bg-dark text-warning fw-bold">
        Detalle de valoraciones
      </div>
      <div class="card-body bg-light">
        <div class="table-responsive">
          <table class="table table-striped text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th>Fecha Cita</th>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Puntuación</th>
                <th>Comentario</th>
                <th>Fecha Valoración</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($valoraciones as $v): ?>
                <tr>
                  <td><?= htmlspecialchars($v['fecha']) ?></td>
                  <td><?= htmlspecialchars($v['hora']) ?></td>
                  <td><?= htmlspecialchars($v['cliente']) ?></td>
                  <td><?= htmlspecialchars($v['servicio']) ?></td>
                  <td>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <i class="bi <?= $i <= $v['puntuacion'] ? 'bi-star-fill text-warning' : 'bi-star text-muted' ?>"></i>
                    <?php endfor; ?>
                  </td>
                  <td><?= !empty($v['comentario']) ? htmlspecialchars($v['comentario']) : '<em class="text-muted">Sin comentario</em>' ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($v['creado_en'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
