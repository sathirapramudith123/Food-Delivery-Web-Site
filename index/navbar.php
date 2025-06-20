<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="home.php">FoodExpress</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'home.php' ? ' active' : '' ?>" href="home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'foodmenu.php' ? ' active' : '' ?>" href="foodmenu.php">Food Menu</a>
        </li>

        <?php if (isset($_SESSION['user_role'])): ?>
          <?php if ($_SESSION['user_role'] == 'user'): ?>
            <li class="nav-item">
              <a class="nav-link" href="profile.php">My Profile</a>
            </li>
          <?php elseif ($_SESSION['user_role'] == 'delivery'): ?>
            <li class="nav-item">
              <a class="nav-link" href="order.php">Delivery Panel</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="deliveryprofile.php">Delivery Profile</a>
            </li>
          <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="adminprofile.php">Admin Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="admindashboard.php">Admin Dashboard</a>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>

        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'login.php' ? ' active' : '' ?>" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'register.php' ? ' active' : '' ?>" href="register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
