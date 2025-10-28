<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('cliente');
require_once __DIR__ . '/../../config/conexion.php';

$cliente_id = $_SESSION['user_id'];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$msg = null;

// Cuando el cliente acepta o rechaza la reprogramación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cita_id = $_POST['cita_id'] ?? null;
    $accion = $_POST['accion'] ?? null;

    $stmt = $pdo->prepare("SELECT * FROM citas WHERE id = :id AND cliente_id = :cliente AND estado = 'reprogramada'");
    $stmt->execute([':id'=>$cita_id, ':cliente'=>$cliente_id]);
    $cita = $stmt->fetch();

    if (!$cita) {
        $msg = "Cita no válida o ya confirmada.";
    } else {
        if ($accion === 'aceptar') {
            // Aplicamos los nuevos valores y confirmamos
            $upd = $pdo->prepare("UPDATE citas 
                SET fecha = fecha_reprogramada, hora = hora_reprogramada, 
                    fecha_reprogramada = NULL, hora_reprogramada = NULL, 
                    estado = 'confirmada' 
                WHERE id = :id");
            $upd->execute([':id'=>$cita_id]);
            $msg = "Has confirmado la nueva fecha de tu cita.";
        } elseif ($accion === 'rechazar') {
            // Se rechaza la propuesta y vuelve a pendiente
            $upd = $pdo->prepare("UPDATE citas 
                SET estado = 'pendiente', fecha_reprogramada = NULL, hora_reprogramada = NULL 
                WHERE id = :id");
            $upd->execute([':id'=>$cita_id]);
            $msg = "Has rechazado la nueva fecha. El barbero deberá ofrecer otra.";
        }
    }
}

// Listar citas reprogramadas pendientes de respuesta
$stmt = $pdo->prepare("
    SELECT c.*, u.nombre AS barbero, s.nombre AS servicio
    FROM citas c
    JOIN usuarios u ON c.barbero_id = u.id
    JOIN servicios s ON c.servicio_id = s.id
    WHERE c.cliente_id = :cliente AND c.estado = 'reprogramada'
    ORDER BY c.fecha DESC
");
$stmt->execute([':cliente'=>$cliente_id]);
$citas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
  <h3 class="text-center text-primary mb-3">📅 Confirmar Reprogramación</h3>

  <?php if ($msg): ?>
    <div class="alert alert-info text-center"><?=htmlspecialchars($msg)?></div>
  <?php endif; ?>

  <?php if (empty($citas)): ?>
    <div class="alert alert-success text-center">No tienes reprogramaciones pendientes.</div>
  <?php else: ?>
    <table class="table table-bordered text-center">
      <thead class="table-light">
        <tr>
          <th>Barbero</th>
          <th>Servicio</th>
          <th>Fecha original</th>
          <th>Nueva fecha propuesta</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($citas as $c): ?>
          <tr>
            <td><?=htmlspecialchars($c['barbero'])?></td>
            <td><?=htmlspecialchars($c['servicio'])?></td>
            <td><?=htmlspecialchars($c['fecha'].' '.$c['hora'])?></td>
            <td class="fw-bold text-primary"><?=htmlspecialchars($c['fecha_reprogramada'].' '.$c['hora_reprogramada'])?></td>
            <td>
              <form method="post" style="display:inline">
                <input type="hidden" name="cita_id" value="<?=$c['id']?>">
                <input type="hidden" name="accion" value="aceptar">
                <button class="btn btn-sm btn-success">Aceptar</button>
              </form>
              <form method="post" style="display:inline">
                <input type="hidden" name="cita_id" value="<?=$c['id']?>">
                <input type="hidden" name="accion" value="rechazar">
                <button class="btn btn-sm btn-danger">Rechazar</button>
              </form>
            </td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
