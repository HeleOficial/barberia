<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role('admin');
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // No permitir eliminar al propio admin
  if ($id == $_SESSION['user_id']) {
    echo "<script>alert('No puedes eliminar tu propio usuario'); window.location.href='usuarios.php';</script>";
    exit;
  }

  $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
  $stmt->execute([$id]);

  echo "<script>alert('Usuario eliminado correctamente'); window.location.href='usuarios.php';</script>";
} else {
  header("Location: usuarios.php");
  exit;
}
?>
