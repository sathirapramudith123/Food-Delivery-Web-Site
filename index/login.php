<?php
session_start();
require_once 'database.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['loginEmail'];
  $password = $_POST['loginPassword'];

  if (!empty($email) && !empty($password)) {
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: home.php");
        exit();
      } else {
        $message = "Invalid password.";
      }
    } else {
      $message = "No user found with that email.";
    }
  } else {
    $message = "Please fill in all fields.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/login.css" />
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="home.php">FoodExpress</a>
    </div>
  </nav>

  <!-- Login Form -->
  <section class="form-section py-5">
    <div class="container">
      <h2 class="text-center text-danger mb-4">Login Here</h2>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php if ($message): ?>
            <div class="alert alert-danger"><?php echo $message; ?></div>
          <?php endif; ?>
          <form method="POST" action="login.php">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="loginEmail" name="loginEmail" required>
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Login</button>
            <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
