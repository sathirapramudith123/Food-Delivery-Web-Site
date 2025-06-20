<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

include 'database.php'; // Ensure it connects $conn

$message = '';
$editUser = null;

// Create / Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
  $id = $_POST['id'] ?? '';
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $role = $_POST['role'];
  $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

  if ($id) {
    // Update
    $sql = "UPDATE users SET name=?, email=?, phone=?, role=?";
    $types = "ssss";
    $params = [$name, $email, $phone, $role];

    if ($password) {
      $sql .= ", password=?";
      $types .= "s";
      $params[] = $password;
    }

    $sql .= " WHERE id=?";
    $types .= "i";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    $message = $stmt->execute() ? "User updated successfully!" : "Update failed!";
    $stmt->close();
  } else {
    // Create
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $phone, $role);
    $message = $stmt->execute() ? "User created successfully!" : "Creation failed!";
    $stmt->close();
  }
}

// Edit
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = $conn->query("SELECT * FROM users WHERE id = $id");
  $editUser = $result->fetch_assoc();
}

// Delete
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  if ($id != $_SESSION['user_id']) { // Prevent deleting self
    $conn->query("DELETE FROM users WHERE id = $id");
  }
  header("Location: admin_profile.php");
  exit();
}

// Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h2 class="mb-4 text-primary">Admin Dashboard - Welcome <?= htmlspecialchars($_SESSION['user_name']) ?></h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <!-- User Form -->
  <form method="POST" class="card p-4 mb-4">
    <input type="hidden" name="id" value="<?= $editUser['id'] ?? '' ?>">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="<?= $editUser['name'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= $editUser['email'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= $editUser['phone'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label>Password <?= $editUser ? '(leave blank to keep current)' : '' ?></label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-control" required>
        <option value="user" <?= (isset($editUser['role']) && $editUser['role'] === 'user') ? 'selected' : '' ?>>User</option>
        <option value="delivery" <?= (isset($editUser['role']) && $editUser['role'] === 'delivery') ? 'selected' : '' ?>>Delivery</option>
        <option value="admin" <?= (isset($editUser['role']) && $editUser['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>
    <button type="submit" name="save" class="btn btn-success">Save User</button>
    <a href="logout.php" class="btn btn-danger float-end">Logout</a>
  </form>

  <!-- Users Table -->
  <h4>All Users</h4>
  <table class="table table-bordered bg-white">
    <thead class="table-light">
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['phone']) ?></td>
          <td><?= ucfirst($user['role']) ?></td>
          <td>
            <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <?php if ($user['id'] != $_SESSION['user_id']): ?>
              <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
