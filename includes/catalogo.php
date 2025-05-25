<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Catálogo de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-white">
<div class="container mt-4">
  <h3>Bienvenido, <?php echo htmlspecialchars($username); ?> 👋</h3>
  <a href="../logout.php" class="btn btn-danger btn-sm float-end">Cerrar sesión</a>
  <h2 class="mt-4">📚 Catálogo de Libros</h2>
  <table class="table table-striped mt-3">
    <thead>
      <tr>
        <th>Título</th>
        <th>Autor</th>
        <th>Año</th>
        <th>Género</th>
        <th>Disponibles</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM books");
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
          <td>{$row['title']}</td>
          <td>{$row['author']}</td>
          <td>{$row['year']}</td>
          <td>{$row['genre']}</td>
          <td>{$row['quantity']}</td>
        </tr>";
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
