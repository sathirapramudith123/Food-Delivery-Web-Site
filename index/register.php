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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
  <div class="container py-5">
    <h2 class="text-center mb-4">Register</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form method="POST" action="register.php">
      <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="regName" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="regEmail" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Phone Number</label>
        <input type="text" name="regPhone" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="regPassword" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="regRole" class="form-control" required>
          <option value="user">User</option>
          <option value="delivery">Delivery Member</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn btn-danger w-100">Register</button>
      <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
    </form>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>
