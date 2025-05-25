<?php
session_start();
require 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user']['username'];
$editando = false;
$libro_edit = [
    'id' => '',
    'title' => '',
    'author' => '',
    'year' => '',
    'genre' => '',
    'quantity' => ''
];

// Agregar libro
if (isset($_POST['add_book'])) {
    $stmt = $conn->prepare("INSERT INTO books (title, author, year, genre, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $_POST['title'], $_POST['author'], $_POST['year'], $_POST['genre'], $_POST['quantity']);
    $stmt->execute();

    // Limpiar campos
    $_POST = [];
}

// Actualizar libro
if (isset($_POST['update_book'])) {
    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, year=?, genre=?, quantity=? WHERE id=?");
    $stmt->bind_param("ssisii", $_POST['title'], $_POST['author'], $_POST['year'], $_POST['genre'], $_POST['quantity'], $_POST['id']);
    $stmt->execute();

    // Limpiar campos después de actualizar
    $_POST = [];
}

// Eliminar libro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM books WHERE id = $id");

    // Redirigir a sí mismo para evitar reenvío por GET
    echo "<script>window.location.href='libros.php';</script>";
    exit();
}

// Preparar datos para edición
if (isset($_GET['edit'])) {
    $editando = true;
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM books WHERE id = $id");
    $libro_edit = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Libros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h3>Hola, <?php echo htmlspecialchars($username); ?> 👋</h3>
<a href="../logout.php" class="btn btn-danger btn-sm float-end">Cerrar sesión</a>
<h2 class="mb-4">📚 Gestión de Libros</h2>

<!-- Formulario -->
<form method="POST" action="libros.php" class="mb-4">
  <input type="hidden" name="id" value="<?php echo $libro_edit['id']; ?>">
  <input type="text" name="title" class="form-control mb-2" placeholder="Título" required value="<?php echo isset($libro_edit['title']) ? $libro_edit['title'] : ''; ?>">
  <input type="text" name="author" class="form-control mb-2" placeholder="Autor" required value="<?php echo isset($libro_edit['author']) ? $libro_edit['author'] : ''; ?>">
  <input type="number" name="year" class="form-control mb-2" placeholder="Año" required value="<?php echo isset($libro_edit['year']) ? $libro_edit['year'] : ''; ?>">
  <input type="text" name="genre" class="form-control mb-2" placeholder="Género" required value="<?php echo isset($libro_edit['genre']) ? $libro_edit['genre'] : ''; ?>">
  <input type="number" name="quantity" class="form-control mb-2" placeholder="Cantidad" required value="<?php echo isset($libro_edit['quantity']) ? $libro_edit['quantity'] : ''; ?>">
  <button type="submit" name="<?php echo $editando ? 'update_book' : 'add_book'; ?>" class="btn btn-<?php echo $editando ? 'warning' : 'primary'; ?>">
    <?php echo $editando ? 'Actualizar Libro' : 'Agregar Libro'; ?>
  </button>
</form>

<!-- Tabla de libros -->
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Título</th>
      <th>Autor</th>
      <th>Año</th>
      <th>Género</th>
      <th>Cantidad</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $result = $conn->query("SELECT * FROM books");
    while ($book = $result->fetch_assoc()):
    ?>
      <tr>
        <td><?php echo htmlspecialchars($book['title']); ?></td>
        <td><?php echo htmlspecialchars($book['author']); ?></td>
        <td><?php echo $book['year']; ?></td>
        <td><?php echo htmlspecialchars($book['genre']); ?></td>
        <td><?php echo $book['quantity']; ?></td>
        <td>
          <a href="libros.php?edit=<?php echo $book['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="libros.php?delete=<?php echo $book['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este libro?')">Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
