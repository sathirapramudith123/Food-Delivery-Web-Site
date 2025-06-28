<?php
include 'database.php'; // adjust the path if needed

$searchTerm = trim($_GET['search'] ?? '');
$messages = [];

// base SQL
$sql = "SELECT * FROM contact_messages";
$params = [];
$types = "";

// if searching
if ($searchTerm) {
    $sql .= " WHERE name LIKE ? OR email LIKE ?";
    $params = ["%$searchTerm%", "%$searchTerm%"];
    $types = "ss";
}

$sql .= " ORDER BY created_at DESC";

// prepare
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Prepare failed: " . $conn->error);
}

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Messages - Admin</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
</head>
<body>
<?php include("../index/header.php"); ?>
<div class="container py-5">
  <h2 class="mb-4">Contact Messages</h2>

  <form method="GET" class="mb-4 d-flex">
    <input
      type="text"
      name="search"
      class="form-control me-2"
      placeholder="Search by name or email"
      value="<?= htmlspecialchars($searchTerm) ?>"
    />
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <?php if (count($messages) > 0): ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Message</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($messages as $msg): ?>
          <tr>
            <td><?= $msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
            <td><?= $msg['created_at'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-info">No messages found.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include("../index/footer.php"); ?>
</body>
</html>

