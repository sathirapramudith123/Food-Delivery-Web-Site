<?php
session_start();
include 'database.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $name, $hashedPassword, $role);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      $_SESSION['user_id'] = $id;
      $_SESSION['user_name'] = $name;
      $_SESSION['user_role'] = $role;

      switch ($role) {
        case 'user':
          header("Location: profile.php");
          break;
        case 'delivery':
          header("Location: deliveryprofile.php");
          break;
        case 'admin':
          header("Location: adminprofile.php");
          break;
      }
      exit();
    } else {
      $message = "<div class='alert alert-danger'>Invalid password.</div>";
    }
  } else {
    $message = "<div class='alert alert-danger'>User not found.</div>";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container py-5">
    <h2 class="text-center mb-4">Login</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form method="POST" action="login.php">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <p class="text-center mt-3">No account? <a href="register.php">Register here</a></p>
    </form>
  </div>
  <?php include 'footer.php'; ?>
</body>
</html>
