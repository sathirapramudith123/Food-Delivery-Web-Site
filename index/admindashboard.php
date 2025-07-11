<?php
include 'database.php';

// Get user count grouped by role
$roleCounts = [];
$result = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");

while ($row = $result->fetch_assoc()) {
    $roleCounts[$row['role']] = $row['count'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../style/admin.css" />
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="userstatus.php">Users</a>
    <a href="order.php">Order</a>
    <a href="foodmenu.php">Products</a>
    <a href="feedbacklist.php">Feedback</a>
    <a href="contactlist.php">Contact Message</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Dashboard</h1>
      <div id="time"></div>
    </div>
    <div class="card" style="margin-top: 40px; padding: 20px;">
        <h3>User Roles Chart</h3>
        <canvas id="userBarChart" height="100"></canvas>
    </div>

  </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  window.userRoleCounts = <?= json_encode($roleCounts) ?>;
</script>
<script src="../js/admin.js"></script>
</body>
</html>
