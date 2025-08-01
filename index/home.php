<?php 
// Start session if needed
session_start();

require_once 'database.php'; // adjust path as needed

// Fetch featured food items (latest 3)
$featuredItems = [];
$result = $conn->query("SELECT * FROM food_menu ORDER BY id DESC LIMIT 3");
if ($result && $result->num_rows > 0) {
    $featuredItems = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch latest 3 customer feedbacks
$feedbacks = [];
$feedbackResult = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC LIMIT 3");
if ($feedbackResult && $feedbackResult->num_rows > 0) {
    $feedbacks = $feedbackResult->fetch_all(MYSQLI_ASSOC);
}
?>

<?php include ("../index/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FoodExpress - Home</title>
  <link rel="icon" type="image/png" href="../images/favicon.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .fa-star.checked {
      color: gold;
    }
  </style>
</head>
<body>

  <!-- Hero Section -->
  <header class="hero-section text-white d-flex align-items-center" >
    <div class="container text-center">
      <h1 class="display-4 fw-bold">Delicious Food Delivered Fast</h1>
      <p class="lead" style="color: ghostwhite;">Order your favorite meals anytime, anywhere!</p>
      <a href="foodmenu.php" class="btn btn-light btn-lg mt-3" style="border-radius: 20px; color: #dc3545;">Browse Menu</a>
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

  <!-- Customer Feedback Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="text-danger text-center mb-4">What Our Customers Say</h2>
      <div class="row g-4">
        <?php if (!empty($feedbacks)): ?>
          <?php foreach ($feedbacks as $fb): ?>
            <div class="col-md-4">
              <div class="p-4 bg-white shadow-sm rounded h-100">
                <p class="mb-2"><?= htmlspecialchars($fb['comment']) ?></p>
                <div>
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="fa fa-star <?= $i <= $fb['rating'] ? 'checked' : 'text-muted' ?>"></span>
                  <?php endfor; ?>
                </div>
                <small class="text-muted">Posted on <?= date('F j, Y', strtotime($fb['created_at'])) ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center">No customer feedback yet.</p>
        <?php endif; ?>
      </div>
      <div class="text-center mt-4">
        <a href="feedback.php" class="btn btn-outline-danger">Leave Feedback</a>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <?php include ("../index/footer.php"); ?>

</body>
</html>

<?php $conn->close(); ?>
