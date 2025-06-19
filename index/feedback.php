<?php
include 'database.php'; // Include database connection"

// Handle form submission (Add Food)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['foodName'] ?? '';
    $desc = $_POST['foodDesc'] ?? '';
    $price = $_POST['foodPrice'] ?? 0;
    $image = $_POST['foodImage'] ?? '';

    // Basic validation
    if ($name && $desc && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO food_menu (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $desc, $price, $image);
        $stmt->execute();
        $stmt->close();
        header("Location: food_menu.php"); // redirect to clear POST data
        exit;
    } else {
        $error = "Please fill all required fields correctly.";
    }
}

// Fetch food items from DB
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM food_menu WHERE name LIKE ?");
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $stmt = $conn->prepare("SELECT * FROM food_menu");
}
$stmt->execute();
$result = $stmt->get_result();
$menuItems = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Food Menu - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/food menu.css" />
</head>
<body>

  <?php include ("../index/navbar.php"); ?>

  <section class="menu-section py-5">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Manage Food Menu</h2>

      <!-- Search Box -->
      <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search food by name..." value="<?= htmlspecialchars($search) ?>" />
      </form>

      <!-- Error message -->
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Add Form -->
      <form id="foodForm" class="row g-3 mb-4" method="POST" action="food_menu.php">
        <div class="col-md-3">
          <input type="text" name="foodName" class="form-control" placeholder="Food Name" required />
        </div>
        <div class="col-md-3">
          <input type="text" name="foodDesc" class="form-control" placeholder="Description" required />
        </div>
        <div class="col-md-2">
          <input type="number" name="foodPrice" class="form-control" placeholder="Price ($)" step="0.01" required />
        </div>
        <div class="col-md-2">
          <input type="url" name="foodImage" class="form-control" placeholder="Image URL" />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-danger w-100" id="submitBtn">Add Food</button>
        </div>
      </form>

      <!-- Menu List -->
      <div id="menuList" class="row row-cols-1 row-cols-md-2 g-3">
        <?php foreach ($menuItems as $food): ?>
          <div class="col">
            <div class="card">
              <?php if ($food['image']): ?>
                <img src="<?= htmlspecialchars($food['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($food['name']) ?>" />
              <?php endif; ?>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($food['name']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($food['description']) ?></p>
                <p class="card-text"><strong>$<?= number_format($food['price'], 2) ?></strong></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (count($menuItems) === 0): ?>
          <p class="text-center">No food items found.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <?php include ("../index/footer.php"); ?>
</body>
</html>

<?php $conn->close(); ?>
