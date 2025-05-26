<?php
session_start();
require 'db.php';

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


if (isset($_POST['add_book'])) {
    $stmt = $conn->prepare("INSERT INTO books (title, author, year, genre, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $_POST['title'], $_POST['author'], $_POST['year'], $_POST['genre'], $_POST['quantity']);
    $stmt->execute();
    $_POST = [];
}


if (isset($_POST['update_book'])) {
    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, year=?, genre=?, quantity=? WHERE id=?");
    $stmt->bind_param("ssisii", $_POST['title'], $_POST['author'], $_POST['year'], $_POST['genre'], $_POST['quantity'], $_POST['id']);
    $stmt->execute();
    $_POST = [];
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM books WHERE id = $id");
    echo "<script>window.location.href='libros.php';</script>";
    exit();
}

if (isset($_GET['loan'])) {
    $book_id = $_GET['loan'];
    $user_id = $_SESSION['user']['id'];
    $res = $conn->query("SELECT quantity FROM books WHERE id = $book_id");
    $data = $res->fetch_assoc();
    if ($data['quantity'] > 0) {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, book_id, date_of_issue) VALUES (?, ?, CURDATE())");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();

        $conn->query("UPDATE books SET quantity = quantity - 1 WHERE id = $book_id");
    } else {
        echo "<script>alert('❌ No hay ejemplares disponibles para prestar.');</script>";
    }

    echo "<script>window.location.href='libros.php';</script>";
    exit();
}

if (isset($_GET['return'])) {
    $book_id = $_GET['return'];
    $user_id = $_SESSION['user']['id'];

    $res = $conn->query("SELECT id FROM transactions WHERE user_id = $user_id AND book_id = $book_id AND date_of_return IS NULL LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $transaction_id = $row['id'];


        $conn->query("UPDATE transactions SET date_of_return = CURDATE() WHERE id = $transaction_id");

        $conn->query("UPDATE books SET quantity = quantity + 1 WHERE id = $book_id");
    } else {
        echo "<script>alert('⚠️ No hay préstamo activo para este libro.');</script>";
    }

    echo "<script>window.location.href='libros.php';</script>";
    exit();
}


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
  <style>
    body {
      background-color: #f8f9fa;
    }
    .header-title {
      color: #0d6efd;
    }
    .info-box {
      background-color: #e9f5ff;
      border-left: 5px solid #0d6efd;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body class="container mt-5">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="header-title">👋 Hola, <?php echo htmlspecialchars($username); ?></h3>
    <a href="../logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesión</a>
  </div>

  <h2 class="mb-4">📚 Gestión de Libros</h2>

  <div class="info-box">
    <strong>Bibliotecario:</strong> Puede agregar, editar y eliminar libros del catálogo, además de facilitar préstamos y devoluciones.
  </div>

  <div class="card mb-4">
    <div class="card-header bg-<?php echo $editando ? 'warning' : 'primary'; ?> text-white">
      <?php echo $editando ? '✏️ Editar Libro' : '➕ Agregar Nuevo Libro'; ?>
    </div>
    <div class="card-body">
      <form method="POST" action="libros.php">
        <input type="hidden" name="id" value="<?php echo $libro_edit['id']; ?>">
        <div class="mb-3">
          <input type="text" name="title" class="form-control" placeholder="Título" required value="<?php echo $libro_edit['title']; ?>">
        </div>
        <div class="mb-3">
          <input type="text" name="author" class="form-control" placeholder="Autor" required value="<?php echo $libro_edit['author']; ?>">
        </div>
        <div class="mb-3">
          <input type="number" name="year" class="form-control" placeholder="Año" required value="<?php echo $libro_edit['year']; ?>">
        </div>
        <div class="mb-3">
          <input type="text" name="genre" class="form-control" placeholder="Género" required value="<?php echo $libro_edit['genre']; ?>">
        </div>
        <div class="mb-3">
          <input type="number" name="quantity" class="form-control" placeholder="Cantidad" required value="<?php echo $libro_edit['quantity']; ?>">
        </div>
        <button type="submit" name="<?php echo $editando ? 'update_book' : 'add_book'; ?>" class="btn btn-<?php echo $editando ? 'warning' : 'success'; ?>">
          <?php echo $editando ? 'Actualizar Libro' : 'Agregar Libro'; ?>
        </button>
        <?php if ($editando): ?>
          <a href="libros.php" class="btn btn-secondary ms-2">Cancelar</a>
        <?php endif; ?>
      </form>
    </div>
  </div>


  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-primary text-center">
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
            <td class="text-center"><?php echo $book['quantity']; ?></td>
            <td class="text-center">
              <a href="libros.php?loan=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-primary me-1">📖 Prestar</a>
              <a href="libros.php?return=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-success">🔄 Devolver</a>
              <a href="libros.php?edit=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-warning me-1">✏️</a>
              <a href="libros.php?delete=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este libro?')">🗑️</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
