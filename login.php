<?php include('includes/db.php'); session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Iniciar Sesión</h2>
<form action="" method="POST">
  <input type="email" name="email" class="form-control mb-2" placeholder="Correo electrónico" required>
  <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña" required>
  <button type="submit" name="login" class="btn btn-primary">Ingresar</button>
  <div class="mb-2">
    <a href="register.php">no tienes una cuenta?, registrate aqui..</a>
  </div>
  
</form>

<?php
if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user'] = $user;
      $_SESSION['user_id'] = $user['id'];          // <- 🔑 NECESARIO para validación en otras páginas
      $_SESSION['username'] = $user['username'];   // <- 🔑 Para mostrar el nombre
      $_SESSION['role_id'] = $user['role_id'];     // <- 🔑 Si vas a hacer control por rol
          header("Location: dashboard.php");
    } else {
      echo "<div class='alert alert-danger mt-3'>Contraseña incorrecta</div>";
    }
  } else {
    echo "<div class='alert alert-danger mt-3'>Correo no registrado</div>";
  }
}
?>

</body>
</html>
