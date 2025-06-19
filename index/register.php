<?php
include 'database.php'; // Include DB connection at the top

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['regName']);
  $email = trim($_POST['regEmail']);
  $password = trim($_POST['regPassword']);

  if (!empty($name) && !empty($email) && !empty($password)) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
      $message = "<div class='alert alert-warning'>Email is already registered.</div>";
    } else {
      // Insert new user
      $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $name, $email, $hashedPassword);

      if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Registration successful!</div>";
      } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
      }

      $stmt->close();
    }

    $checkStmt->close();
  } else {
    $message = "<div class='alert alert-danger'>Please fill in all fields.</div>";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style/register.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="home.php">FoodExpress</a>
    </div>
  </nav>

  <!-- Register Form -->
  <section class="form-section py-5">
    <div class="container">
      <h2 class="text-center text-danger mb-4">Create an Account Here</h2>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php if (!empty($message)) echo $message; ?>
          <form id="registerForm" method="POST" action="register.php">
            <div class="mb-3">
              <label for="regName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="regName" name="regName" required>
            </div>
            <div class="mb-3">
              <label for="regEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="regEmail" name="regEmail" required>
            </div>
            <div class="mb-3">
              <label for="regPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="regPassword" name="regPassword" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Register</button>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
          </form>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
