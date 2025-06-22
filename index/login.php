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
          header("Location: home.php");
          break;
        case 'delivery':
          header("Location: home.php");
          break;
        case 'admin':
          header("Location: home.php");
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border: none;
      border-radius: 1rem;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #0d6efd;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
  <?php include 'navbar.php'; ?>

  <div class="container py-5 d-flex justify-content-center align-items-center flex-grow-1">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
      <h3 class="text-center mb-4">Welcome Back</h3>
      <?php if (!empty($message)) echo $message; ?>
      <form method="POST" action="login.php">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div class="text-center mt-3">
          <small>No account? <a href="register.php">Register here</a></small>
        </div>
      </form>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
