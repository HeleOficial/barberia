<?php 
// pages/cliente/valorar.php 
require_once __DIR__ . '/../../includes/auth.php'; 
require_login(); 
require_role('cliente'); 
require_once __DIR__ . '/../../config/conexion.php'; 

$cliente_id = $_SESSION['user_id']; 
$errors = $success = null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $cita_id = $_POST['cita_id'] ?? null; 
    $puntuacion = (int)($_POST['puntuacion'] ?? 0); 
    $comentario = trim($_POST['comentario'] ?? ''); 

    if (!$cita_id || $puntuacion < 1 || $puntuacion > 5) {
        $errors = "⚠️ Selecciona una cita válida y una puntuación entre 1 y 5."; 
    }

    if (!$errors) { 
        $q = $pdo->prepare("SELECT barbero_id FROM citas WHERE id = :id AND cliente_id = :cliente LIMIT 1"); 
        $q->execute([':id'=>$cita_id, ':cliente'=>$cliente_id]); 
        $r = $q->fetch(); 

        if ($r) { 
            $ins = $pdo->prepare("
                INSERT INTO valoraciones (cita_id, cliente_id, barbero_id, puntuacion, comentario) 
                VALUES (:cita, :cliente, :barbero, :punt, :coment)
            "); 
            $ins->execute([ 
                ':cita'=>$cita_id, 
                ':cliente'=>$cliente_id, 
                ':barbero'=>$r['barbero_id'], 
                ':punt'=>$puntuacion, 
                ':coment'=>$comentario 
            ]); 
            $success = "✅ ¡Gracias por tu valoración!"; 
        } else { 
            $errors = "❌ Cita no encontrada o no pertenece a tu cuenta."; 
        } 
    } 
} 

// Citas realizadas sin valoración
$stmt = $pdo->prepare("
    SELECT c.id, c.fecha, c.hora, u.nombre AS barbero, s.nombre AS servicio 
    FROM citas c 
    JOIN usuarios u ON c.barbero_id=u.id 
    JOIN servicios s ON c.servicio_id=s.id 
    WHERE c.cliente_id = :cliente 
    AND c.estado = 'realizada' 
    AND c.id NOT IN (SELECT cita_id FROM valoraciones) 
    ORDER BY c.fecha DESC
"); 
$stmt->execute([':cliente'=>$cliente_id]); 
$citas = $stmt->fetchAll(); 

include __DIR__ . '/../../includes/header.php'; 
?> 

<div class="container mt-4">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-warning text-center fw-bold">
      💬 Valorar Servicio
    </div>
    <div class="card-body bg-light">

      <?php if ($errors): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($errors) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <?php if (empty($citas)): ?>
        <div class="alert alert-info text-center">No tienes citas pendientes por valorar.</div>
      <?php else: ?>
        <form method="post" class="mx-auto" style="max-width: 600px;">
          <div class="mb-3">
            <label class="form-label fw-semibold">Selecciona tu cita</label>
            <select name="cita_id" class="form-select border-dark" required>
              <option value="">Selecciona...</option>
              <?php foreach($citas as $c): ?>
                <option value="<?= $c['id'] ?>">
                  <?= htmlspecialchars($c['fecha'].' '.$c['hora'].' - '.$c['barbero'].' ('.$c['servicio'].')') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Puntuación</label>
            <div class="d-flex justify-content-between px-1">
              <label><input type="radio" name="puntuacion" value="5" required> ⭐⭐⭐⭐⭐</label>
              <label><input type="radio" name="puntuacion" value="4"> ⭐⭐⭐⭐</label>
              <label><input type="radio" name="puntuacion" value="3"> ⭐⭐⭐</label>
              <label><input type="radio" name="puntuacion" value="2"> ⭐⭐</label>
              <label><input type="radio" name="puntuacion" value="1"> ⭐</label>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Comentario (opcional)</label>
            <textarea name="comentario" rows="3" class="form-control border-dark" placeholder="Cuéntanos tu experiencia..."></textarea>
          </div>

          <button class="btn btn-warning text-dark fw-bold w-100 shadow-sm">
            💈 Enviar Valoración
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
