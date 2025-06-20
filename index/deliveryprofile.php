<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'delivery') {
  header("Location: login.php");
  exit();
}

include 'database.php'; // Ensure it connects $conn

$message = '';
$delivery = null;

// Create or Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
  $id = $_POST['id'] ?? '';
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
  $profile_pic = '';

  // Handle image upload
  if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $uploadDir = 'uploads/';
    $profile_pic = $uploadDir . basename($_FILES['profile_pic']['name']);
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
  }

  if ($id) {
    // Update
    $sql = "UPDATE users SET name=?, email=?, phone=?";
    $types = "sss";
    $params = [$name, $email, $phone];

    if ($password) {
      $sql .= ", password=?";
      $types .= "s";
      $params[] = $password;
    }

    if ($profile_pic) {
      $sql .= ", profile_pic=?";
      $types .= "s";
      $params[] = $profile_pic;
    }

    $sql .= " WHERE id=?";
    $types .= "i";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
      $message = "Updated successfully!";
    } else {
      $message = "Update failed!";
    }
    $stmt->close();
  } else {
    // Create new delivery member
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, profile_pic, role) VALUES (?, ?, ?, ?, ?, 'delivery')");
    $stmt->bind_param("sssss", $name, $email, $password, $phone, $profile_pic);

    if ($stmt->execute()) {
      $message = "Created successfully!";
    } else {
      $message = "Creation failed!";
    }
    $stmt->close();
  }
}

// Read (Edit)
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = $conn->query("SELECT * FROM users WHERE id = $id AND role = 'delivery'");
  $delivery = $result->fetch_assoc();
}

// Delete
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM users WHERE id = $id AND role = 'delivery'");
  header("Location: delivery_profile.php");
  exit();
}

// List all delivery members
$deliveryList = $conn->query("SELECT * FROM users WHERE role = 'delivery'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delivery Profile Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <h2 class="mb-4 text-danger">Delivery Profile - Welcome <?= $_SESSION['user_name'] ?></h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4">
    <input type="hidden" name="id" value="<?= $delivery['id'] ?? '' ?>">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="<?= $delivery['name'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?= $delivery['email'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= $delivery['phone'] ?? '' ?>" required>
    </div>
    <div class="mb-3">
      <label>Password (leave blank to keep)</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label>Profile Picture</label>
      <input type="file" name="profile_pic" class="form-control">
      <?php if (!empty($delivery['profile_pic'])): ?>
        <img src="<?= $delivery['profile_pic'] ?>" alt="Profile" class="img-thumbnail mt-2" width="100">
      <?php endif; ?>
    </div>
    <button type="submit" name="save" class="btn btn-primary">Save</button>
    <a href="logout.php" class="btn btn-danger float-end">Logout</a>
  </form>

  <h4>All Delivery Members</h4>
  <table class="table table-bordered bg-white">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Photo</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $deliveryList->fetch_assoc()): ?>
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
            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this delivery member?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
