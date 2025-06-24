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
    <a href="contactlist.php">Contact Meassage</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Dashboard</h1>
      <div id="time"></div>
    </div>

    <div class="cards">
      <div class="card">
        <h3>Users</h3>
        <p id="userCount">Loading...</p>
      </div>
      <div class="card">
        <h3>Sales</h3>
        <p>$12,345</p>
      </div>
      <div class="card">
        <h3>Performance</h3>
        <p>87%</p>
      </div>
    </div>
  </div>

  <script src="../js/admin.js"></script>
  </script>
</body>
</html>
