<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('barbero');
require_once __DIR__ . '/../../config/conexion.php';

$barbero_id = $_SESSION['user_id'];
$msg = null;

// Guardar nueva disponibilidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $dia = $_POST['dia'] ?? '';
    $inicio = $_POST['hora_inicio'] ?? '';
    $fin = $_POST['hora_fin'] ?? '';

    if ($dia && $inicio && $fin) {
        $stmt = $pdo->prepare("INSERT INTO disponibilidades (barbero_id, dia_semana, hora_inicio, hora_fin) VALUES (:b, :d, :i, :f)");
        $stmt->execute([':b'=>$barbero_id, ':d'=>$dia, ':i'=>$inicio, ':f'=>$fin]);
        $msg = "Disponibilidad agregada.";
    } else {
        $msg = "Complete todos los campos.";
    }
}

// Eliminar disponibilidad
if (isset($_GET['delete'])) {
    $del = $pdo->prepare("DELETE FROM disponibilidades WHERE id = :id AND barbero_id = :b");
    $del->execute([':id'=>$_GET['delete'], ':b'=>$barbero_id]);
    $msg = "Disponibilidad eliminada.";
}

// Mostrar las disponibilidades actuales
$stmt = $pdo->prepare("SELECT * FROM disponibilidades WHERE barbero_id = :b ORDER BY FIELD(dia_semana,'lunes','martes','miercoles','jueves','viernes','sabado','domingo'), hora_inicio");
$stmt->execute([':b'=>$barbero_id]);
$horarios = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container mt-4">
  <h3>Mi Disponibilidad</h3>
  <?php if ($msg): ?><div class="alert alert-info"><?=htmlspecialchars($msg)?></div><?php endif; ?>

  <form method="post" class="row g-2 mb-4">
    <input type="hidden" name="add" value="1">
    <div class="col-md-3">
      <select name="dia" class="form-select" required>
        <option value="">Día...</option>
        <option value="lunes">Lunes</option>
        <option value="martes">Martes</option>
        <option value="miercoles">Miércoles</option>
        <option value="jueves">Jueves</option>
        <option value="viernes">Viernes</option>
        <option value="sabado">Sábado</option>
        <option value="domingo">Domingo</option>
      </select>
    </div>
    <div class="col-md-3"><input type="time" name="hora_inicio" class="form-control" required></div>
    <div class="col-md-3"><input type="time" name="hora_fin" class="form-control" required></div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Agregar</button></div>
  </form>

  <?php if (empty($horarios)): ?>
    <div class="alert alert-secondary">No tienes horarios registrados.</div>
  <?php else: ?>
    <table class="table table-bordered">
      <thead><tr><th>Día</th><th>Inicio</th><th>Fin</th><th>Acción</th></tr></thead>
      <tbody>
        <?php foreach($horarios as $h): ?>
          <tr>
            <td><?=ucfirst($h['dia_semana'])?></td>
            <td><?=$h['hora_inicio']?></td>
            <td><?=$h['hora_fin']?></td>
            <td><a href="?delete=<?=$h['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar horario?')">Eliminar</a></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
