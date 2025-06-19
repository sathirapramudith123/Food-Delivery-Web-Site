<?php 
// Optional: Start a session if you plan to manage user login
session_start();

require_once 'database.php'; // Adjust path if needed

// Fetch featured food items (latest 3)
$featuredItems = [];
$result = $conn->query("SELECT * FROM food_menu ORDER BY id DESC LIMIT 3");
if ($result && $result->num_rows > 0) {
    $featuredItems = $result->fetch_all(MYSQLI_ASSOC);
}
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
      <a href="foodmenu.php" class="btn btn-danger btn-lg mt-3">Browse Menu</a>
    </div>
  </header>

  <!-- Why Choose Us Section -->
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

  <!-- Featured Dishes Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Featured Dishes</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (!empty($featuredItems)): ?>
          <?php foreach ($featuredItems as $item): ?>
            <div class="col">
              <div class="card h-100 shadow-sm">
                <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                  <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>" style="height:200px; object-fit:cover;" />
                <?php endif; ?>
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                  <p class="card-text"><?= htmlspecialchars($item['description']) ?></p>
                  <p class="fw-bold text-danger">$<?= number_format($item['price'], 2) ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center">No featured items available.</p>
        <?php endif; ?>
      </div>
      <div class="text-center mt-4">
        <a href="foodmenu.php" class="btn btn-outline-danger">View Full Menu</a>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <?php include ("../index/footer.php"); ?>

</body>
</html>

<?php $conn->close(); ?>
