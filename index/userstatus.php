<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'database.php';

$message = '';
$editUser = null;

// Handle Create / Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($id) {
        // Update user
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
        // Create new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $phone, $role);
        $message = $stmt->execute() ? "User created successfully!" : "Creation failed!";
        $stmt->close();
    }
}

// Handle Edit
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editUser = $result->fetch_assoc();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_profile.php");
    exit();
}

// Handle Search
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id ASC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $users = $stmt->get_result();
    $stmt->close();
} else {
    $users = $conn->query("SELECT * FROM users ORDER BY id ASC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 1rem;
    }
    .table thead th {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">Admin Dashboard</h2>
    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- User Form -->
  <div class="card shadow mb-5">
    <div class="card-header bg-white border-bottom">
      <h5 class="mb-0"><?= $editUser ? 'Edit User' : 'Add New User' ?></h5>
    </div>
    <div class="card-body">
      <form method="POST" action="admin_profile.php">
        <input type="hidden" name="id" value="<?= $editUser['id'] ?? '' ?>">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($editUser['name'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($editUser['email'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($editUser['phone'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Password <?= $editUser ? '(leave blank to keep current)' : '' ?></label>
            <input type="password" name="password" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="user" <?= (isset($editUser['role']) && $editUser['role'] === 'user') ? 'selected' : '' ?>>User</option>
              <option value="delivery" <?= (isset($editUser['role']) && $editUser['role'] === 'delivery') ? 'selected' : '' ?>>Delivery</option>
              <option value="admin" <?= (isset($editUser['role']) && $editUser['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" name="save" class="btn btn-success">Save</button>
          <?php if ($editUser): ?>
            <a href="admin_profile.php" class="btn btn-secondary ms-2">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- Search Form -->
  <form class="d-flex mb-3" method="get" action="admin_profile.php">
    <input class="form-control me-2" type="search" name="search" placeholder="Search by name or email"
           value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-outline-primary" type="submit">Search</button>
  </form>

  <!-- Users Table -->
  <div class="card shadow">
    <div class="card-header bg-white border-bottom">
      <h5 class="mb-0">All Users</h5>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th style="width: 140px;">Actions</th>
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
              <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a>
              <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
