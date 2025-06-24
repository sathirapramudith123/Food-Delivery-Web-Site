<?php
include 'database.php';

$searchTerm = '';
$users = [];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
  $searchTerm = trim($_GET['search']);
  $stmt = $conn->prepare("SELECT id, name, email, phone, role FROM users WHERE name LIKE ? OR email LIKE ?");
  $likeTerm = "%" . $searchTerm . "%";
  $stmt->bind_param("ss", $likeTerm, $likeTerm);
} else {
  $stmt = $conn->prepare("SELECT id, name, email, phone, role FROM users");
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container py-4">
  <h2 class="mb-4">Registered Users</h2>

  <form method="GET" class="mb-4 d-flex" action="users.php">
    <input type="text" name="search" class="form-control me-2" placeholder="Search by name or email" value="<?php echo htmlspecialchars($searchTerm); ?>">
    <button class="btn btn-danger">Search</button>
  </form>

  <?php if (count($users) > 0): ?>
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $index => $user): ?>
          <tr>
            <td><?php echo $index + 1; ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['phone']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-warning">No users found.</div>
  <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
