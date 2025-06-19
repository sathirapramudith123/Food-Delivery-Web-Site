<?php
// Optional: Start a session if you plan to manage user login
session_start();
?>

<?php include ("../index/navbar.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FoodExpress - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/home.css" />
</head>
<body>



  <!-- Hero Section -->
  <header class="hero-section text-white d-flex align-items-center">
    <div class="container text-center">
      <h1 class="display-4 fw-bold">Delicious Food Delivered Fast</h1>
      <p class="lead">Order your favorite meals anytime, anywhere!</p>
      <a href="food_menu.php" class="btn btn-danger btn-lg mt-3">Browse Menu</a>
    </div>
  </header>

  <!-- Featured Section -->
  <section class="py-5 bg-light">
    <div class="container text-center">
      <h2 class="mb-4 text-danger">Why Choose Us?</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="p-4 bg-white shadow-sm rounded">
            <h4>Fast Delivery</h4>
            <p>Get your meals delivered to your doorstep in no time.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 bg-white shadow-sm rounded">
            <h4>Fresh Ingredients</h4>
            <p>We use only the freshest ingredients in our food.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 bg-white shadow-sm rounded">
            <h4>24/7 Service</h4>
            <p>We’re available day and night to serve your cravings.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <?php include ("../index/footer.php"); ?>
</body>
</html>
