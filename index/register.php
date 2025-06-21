<?php
include 'database.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['regName']);
  $email = trim($_POST['regEmail']);
  $password = trim($_POST['regPassword']);
  $phone = trim($_POST['regPhone']);
  $role = $_POST['regRole'];

  if (!empty($name) && !empty($email) && !empty($password) && !empty($phone) && !empty($role)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
      $message = "<div class='alert alert-warning'>Email is already registered.</div>";
    } else {
      $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $role);

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
  <title>Register</title>
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
      border-color: #dc3545;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
<?php include 'navbar.php'; ?>

<div class="container py-5 d-flex justify-content-center align-items-center flex-grow-1">
  <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
    <h3 class="text-center mb-4">Create an Account</h3>
    <?php if (!empty($message)) echo $message; ?>
    <form method="POST" action="register.php">
      <div class="mb-3">
        <label for="regName" class="form-label">Full Name</label>
        <input type="text" name="regName" id="regName" class="form-control" placeholder="John Doe" required>
      </div>
      <div class="mb-3">
        <label for="regEmail" class="form-label">Email</label>
        <input type="email" name="regEmail" id="regEmail" class="form-control" placeholder="you@example.com" required>
      </div>
      <div class="mb-3">
        <label for="regPhone" class="form-label">Phone Number</label>
        <input type="text" name="regPhone" id="regPhone" class="form-control" placeholder="e.g., 9876543210" required>
      </div>
      <div class="mb-3">
        <label for="regPassword" class="form-label">Password</label>
        <input type="password" name="regPassword" id="regPassword" class="form-control" placeholder="Create a password" required>
      </div>
      <div class="mb-3">
        <label for="regRole" class="form-label">Register As</label>
        <select name="regRole" id="regRole" class="form-select" required>
          <option value="user">User</option>
          <option value="delivery">Delivery Member</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn btn-danger w-100">Register</button>
      <div class="text-center mt-3">
        <small>Already have an account? <a href="login.php">Login here</a></small>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
