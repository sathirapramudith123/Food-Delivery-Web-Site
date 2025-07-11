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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --accent: #4895ef;
      --dark: #1b263b;
      --light: #f8f9fa;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #f72585;
      --gray: #adb5bd;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background-color: #f5f7fa;
    }

    .sidebar {
      width: 280px;
      background: linear-gradient(180deg, var(--dark), var(--secondary));
      color: white;
      padding: 2rem 1.5rem;
      transition: all 0.3s ease;
      position: relative;
      z-index: 10;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header {
      display: flex;
      align-items: center;
      margin-bottom: 2.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-left: 10px;
    }

    .sidebar-header i {
      font-size: 1.8rem;
      color: var(--accent);
    }

    .sidebar a {
      display: flex;
      align-items: center;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      padding: 0.8rem 1rem;
      margin: 0.5rem 0;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    .sidebar a i {
      margin-right: 12px;
      font-size: 1.1rem;
      width: 20px;
      text-align: center;
    }

    .sidebar a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      transform: translateX(5px);
    }

    .sidebar a.active {
      background-color: var(--accent);
      color: white;
      font-weight: 500;
    }

    .main-content {
      flex: 1;
      padding: 2rem;
      overflow-y: auto;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }

    .header h1 {
      color: var(--dark);
      font-size: 2rem;
      font-weight: 700;
    }

    .user-info {
      display: flex;
      align-items: center;
    }

    .user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      object-fit: cover;
    }

    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .card {
      background-color: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .card-header h3 {
      font-size: 1.1rem;
      color: var(--dark);
      font-weight: 600;
    }

    .card-header i {
      font-size: 1.5rem;
      color: var(--accent);
    }

    .card-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .card-description {
      color: var(--gray);
      font-size: 0.9rem;
    }

    .chart-card {
      background-color: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
    }

    .chart-header {
      margin-bottom: 1.5rem;
    }

    .chart-header h3 {
      font-size: 1.2rem;
      color: var(--dark);
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 80px;
        padding: 1rem 0.5rem;
        overflow: hidden;
      }
      
      .sidebar-header h2, 
      .sidebar a span {
        display: none;
      }
      
      .sidebar a {
        justify-content: center;
        padding: 1rem 0;
      }
      
      .sidebar a i {
        margin-right: 0;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="sidebar-header">
      <i class="fas fa-shield-alt"></i>
      <h2>Admin Panel</h2>
    </div>
    <a href="userstatus.php" class="active">
      <i class="fas fa-users"></i>
      <span>Users</span>
    </a>
    <a href="order.php">
      <i class="fas fa-shopping-cart"></i>
      <span>Orders</span>
    </a>
    <a href="foodmenu.php">
      <i class="fas fa-utensils"></i>
      <span>Products</span>
    </a>
    <a href="feedbacklist.php">
      <i class="fas fa-comment-alt"></i>
      <span>Feedback</span>
    </a>
    <a href="contactlist.php">
      <i class="fas fa-envelope"></i>
      <span>Messages</span>
    </a>
    <a href="logout.php">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Dashboard Overview</h1>
      <div class="user-info">
        <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Admin">
        <span>Admin</span>
      </div>
    </div>

    <div class="card-container">
      <div class="card">
        <div class="card-header">
          <h3>Total Users</h3>
          <i class="fas fa-users"></i>
        </div>
        <div class="card-value"><?= array_sum($roleCounts) ?></div>
        <div class="card-description">All registered users</div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>Admins</h3>
          <i class="fas fa-user-shield"></i>
        </div>
        <div class="card-value"><?= $roleCounts['admin'] ?? 0 ?></div>
        <div class="card-description">Administrator accounts</div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>Customers</h3>
          <i class="fas fa-user"></i>
        </div>
        <div class="card-value"><?= $roleCounts['user'] ?? 0 ?></div>
        <div class="card-description">Regular customers</div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>Delivery</h3>
          <i class="fas fa-truck"></i>
        </div>
        <div class="card-value"><?= $roleCounts['delivery'] ?? 0 ?></div>
        <div class="card-description">Delivery personnel</div>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-header">
        <h3>User Roles Distribution</h3>
      </div>
      <canvas id="userBarChart" height="120"></canvas>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Convert PHP array to JS object
    const userRoleCounts = <?= json_encode($roleCounts) ?>;
    
    // Prepare data for chart
    const roles = Object.keys(userRoleCounts);
    const counts = Object.values(userRoleCounts);
    const backgroundColors = ['#4361ee', '#4895ef', '#4cc9f0', '#7209b7', '#f72585'];
    
    const ctx = document.getElementById('userBarChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: roles,
        datasets: [{
          label: 'Number of Users',
          data: counts,
          backgroundColor: backgroundColors,
          borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 12
            },
            padding: 12,
            cornerRadius: 6
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0,0,0,0.05)'
            },
            ticks: {
              precision: 0
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  </script>
</body>
</html>