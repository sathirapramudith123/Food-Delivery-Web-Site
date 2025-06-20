<?php
include 'database.php';
$message = '';
$user = null;

// Create / Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
  $id = $_POST['user_id'] ?? '';
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
  $phone = trim($_POST['phone']);
  $uploadDir = 'uploads/';
  $profilePic = '';

  // Handle file upload
  if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $profilePic = $uploadDir . basename($_FILES['profile_pic']['name']);
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profilePic);
  }

  if ($id) {
    // Update
    $sql = "UPDATE users SET name=?, email=?, phone=?" . ($password ? ", password=?" : "") . ($profilePic ? ", profile_pic=?" : "") . " WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($password && $profilePic) {
      $stmt->bind_param("sssssi", $name, $email, $phone, $password, $profilePic, $id);
    } elseif ($password) {
      $stmt->bind_param("ssssi", $name, $email, $phone, $password, $id);
    } elseif ($profilePic) {
      $stmt->bind_param("ssssi", $name, $email, $phone, $profilePic, $id);
    } else {
      $stmt->bind_param("sssi", $name, $email, $phone, $id);
    }

    if ($stmt->execute()) {
      $message = "User updated successfully.";
    } else {
      $message = "Error updating user.";
    }
    $stmt->close();
  } else {
    // Create
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, profile_pic) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $phone, $profilePic);
    if ($stmt->execute()) {
      $message = "User created successfully.";
    } else {
      $message = "Error creating user.";
    }
    $stmt->close();
  }
}

// Read for Edit
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = $conn->query("SELECT * FROM users WHERE id = $id");
  $user = $result->fetch_assoc();
}

// Delete
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM users WHERE id = $id");
  header("Location: profile.php");
  exit();
}

// List all users
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-5">
  <h2 class="mb-4">User Profile Management</h2>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?? '' ?>">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= $user['name'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= $user['email'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password (leave blank to keep current)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= $user['phone'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Profile Picture</label>
      <input type="file" name="profile_pic" class="form-control">
      <?php if (!empty($user['profile_pic'])): ?>
        <img src="<?= $user['profile_pic'] ?>" alt="Profile" class="img-thumbnail mt-2" width="100">
      <?php endif; ?>
    </div>
    <button type="submit" name="save" class="btn btn-success">Save</button>
  </form>

  <h4>All Users</h4>
  <table class="table table-bordered bg-white">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Profile Picture</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $users->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= $row['name'] ?></td>
          <td><?= $row['email'] ?></td>
          <td><?= $row['phone'] ?></td>
          <td>
            <?php if ($row['profile_pic']): ?>
              <img src="<?= $row['profile_pic'] ?>" width="50" class="img-thumbnail">
            <?php endif; ?>
          </td>
          <td>
            <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
