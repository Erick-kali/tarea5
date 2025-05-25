<?php include('includes/db.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Registrar Usuario</h2>
<form action="" method="POST">
  <input type="text" name="username" class="form-control mb-2" placeholder="Usuario" required>
  <input type="email" name="email" class="form-control mb-2" placeholder="Correo electrónico" required>
  <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña" required>
  <select name="role_id" class="form-control mb-2" required>
    <option value="1">Administrador</option>
    <option value="2">Bibliotecario</option>
    <option value="3">Lector</option>
  </select>
  <button type="submit" name="register" class="btn btn-primary">Registrar</button>
  <div class="mb-2">
    <a href="login.php">¡ya tiene una cuenta?</a>
  </div>
</form>

<?php
if (isset($_POST['register'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role_id = $_POST['role_id'];

  $sql = "INSERT INTO users (username, email, password, role_id) 
          VALUES ('$username', '$email', '$password', '$role_id')";
  if ($conn->query($sql) === TRUE) {
    echo "<div class='alert alert-success mt-3'>Usuario registrado con éxito.</div>";
  } else {
    echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
  }
}
?>

</body>
</html>
