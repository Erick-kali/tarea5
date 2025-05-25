<?php 
session_start(); 
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Bienvenido, <?= $user['username']; ?> (<?= $user['role_id'] == 1 ? 'Administrador' : ($user['role_id'] == 2 ? 'Bibliotecario' : 'Lector') ?>)</h2>

<?php if ($user['role_id'] == 1): ?>
  <a href="includes/usuarios.php" class="btn btn-success">Gestionar Usuarios</a>
<?php elseif ($user['role_id'] == 2): ?>
  <a href="includes/libros.php" class="btn btn-primary">Agregar Libros</a>
<?php else: ?>
  <a href="includes/catalogo.php" class="btn btn-info">Ver Catálogo</a>
<?php endif; ?>

<a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>

</body>
</html>