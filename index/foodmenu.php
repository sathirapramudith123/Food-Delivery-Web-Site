<?php
// Include database connection
include 'database.php';  // Adjust path as needed

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['foodName'] ?? '';
    $desc = $_POST['foodDesc'] ?? '';
    $price = $_POST['foodPrice'] ?? 0;
    $image = $_FILES['foodImage']['name'] ?? '';

    if ($name && $desc && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO food_menu (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $desc, $price, $image);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Please fill all required fields correctly.";
    }
}

// Fetch all food menu items
$result = $conn->query("SELECT * FROM food_menu ORDER BY id DESC");
$menuItems = $result->fetch_all(MYSQLI_ASSOC);
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

  <?php include("../index/navbar.php"); ?>

  <section class="menu-section py-5">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Manage Food Menu</h2>

      <!-- Search Box -->
      <input type="text" class="form-control mb-3" id="searchInput" placeholder="Search food by name..." />

      <!-- Error message -->
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Add Food Form -->
      <form id="foodForm" class="row g-3 mb-4" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
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
          <input type="file" name="foodImage" class="form-control" placeholder="Image file/*" required />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-danger w-100" id="submitBtn">Add Food</button>
        </div>
      </form>

      <!-- Menu List -->
      <div id="menuList" class="row row-cols-1 row-cols-md-2 g-3">
        <?php if (count($menuItems) === 0): ?>
          <p class="text-center">No food items found.</p>
        <?php else: ?>
          <?php foreach ($menuItems as $food): ?>
            <div class="col">
              <div class="card">
                <?php if (!empty($food['image'])): ?>
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
        <?php endif; ?>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/food menu.js"></script>

  <?php include("../index/footer.php"); ?>

</body>
</html>

<?php $conn->close(); ?>
