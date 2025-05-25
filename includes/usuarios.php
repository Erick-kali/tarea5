<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user']['username'];
$editando = false;
$user_edit = [
    'id' => '',
    'username' => '',
    'email' => '',
    'role_id' => ''
];

// Cargar roles para el select
$roles_result = $conn->query("SELECT id, name FROM roles");
$roles = [];
while ($row = $roles_result->fetch_assoc()) {
    $roles[$row['id']] = $row['name'];
}

// Agregar usuario
if (isset($_POST['add_user'])) {
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, role_id, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $_POST['username'], $_POST['email'], $_POST['role_id'], $hashed_password);
    $stmt->execute();
    $_POST = [];
}

// Preparar datos para edición
if (isset($_GET['edit'])) {
    $editando = true;
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT id, username, email, role_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_edit = $result->fetch_assoc();
}

// Actualizar usuario
if (isset($_POST['update_user'])) {
    if (!empty($_POST['password'])) {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role_id=?, password=? WHERE id=?");
        $stmt->bind_param("ssisi", $_POST['username'], $_POST['email'], $_POST['role_id'], $hashed_password, $_POST['id']);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role_id=? WHERE id=?");
        $stmt->bind_param("ssii", $_POST['username'], $_POST['email'], $_POST['role_id'], $_POST['id']);
    }
    $stmt->execute();
    $_POST = [];
    $editando = false;
    $user_edit = ['id' => '', 'username' => '', 'email' => '', 'role_id' => ''];
}

// Eliminar usuario
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>window.location.href='usuarios.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">

<h3>Hola, <?php echo htmlspecialchars($username); ?> 👋</h3>
<a href="../logout.php" class="btn btn-danger btn-sm float-end">Cerrar sesión</a>
<h2 class="mb-4">👥 Gestión de Usuarios</h2>

<!-- Formulario -->
<form method="POST" action="usuarios.php" class="mb-4">
  <input type="hidden" name="id" value="<?php echo $user_edit['id']; ?>" />
  <input type="text" name="username" class="form-control mb-2" placeholder="Nombre de usuario" required
         value="<?php echo htmlspecialchars($user_edit['username']); ?>" />
  <input type="email" name="email" class="form-control mb-2" placeholder="Email" required
         value="<?php echo htmlspecialchars($user_edit['email']); ?>" />
  <select name="role_id" class="form-control mb-2" required>
    <option value="">Selecciona un rol</option>
    <?php foreach ($roles as $id => $name): ?>
      <option value="<?php echo $id; ?>" <?php if($user_edit['role_id'] == $id) echo 'selected'; ?>>
        <?php echo htmlspecialchars($name); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <input type="password" name="password" class="form-control mb-2" placeholder="<?php echo $editando ? 'Nueva contraseña (dejar vacío para no cambiar)' : 'Contraseña'; ?>" <?php echo $editando ? '' : 'required'; ?> />
  <button type="submit" name="<?php echo $editando ? 'update_user' : 'add_user'; ?>" class="btn btn-<?php echo $editando ? 'warning' : 'primary'; ?>">
    <?php echo $editando ? 'Actualizar Usuario' : 'Agregar Usuario'; ?>
  </button>
</form>

<!-- Tabla de usuarios -->
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Nombre de usuario</th>
      <th>Email</th>
      <th>Rol</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT users.id, users.username, users.email, roles.name AS role_name 
            FROM users 
            JOIN roles ON users.role_id = roles.id";
    $result = $conn->query($sql);
    while ($user = $result->fetch_assoc()):
    ?>
      <tr>
        <td><?php echo htmlspecialchars($user['username']); ?></td>
        <td><?php echo htmlspecialchars($user['email']); ?></td>
        <td><?php echo htmlspecialchars($user['role_name']); ?></td>
        <td>
          <a href="usuarios.php?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="usuarios.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
