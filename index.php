<!-- index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .welcome-container {
      margin-top: 10%;
      text-align: center;
    }
    .welcome-card {
      padding: 2rem;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<div class="container welcome-container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="welcome-card">
        <h1 class="mb-4">📚 Bienvenido a la Biblioteca Online</h1>
        <p>¿Qué deseas hacer?</p>
        <div class="d-grid gap-2 mt-4">
          <a href="register.php" class="btn btn-primary btn-lg">Registrarse</a>
          <a href="login.php" class="btn btn-outline-secondary btn-lg">Iniciar Sesión</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
