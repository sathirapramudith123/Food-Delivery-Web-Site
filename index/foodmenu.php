<?php
include 'database.php';

$error = '';
$uploadDir = 'uploads/';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $result = $conn->query("SELECT image FROM food_menu WHERE id=$id");
    if ($row = $result->fetch_assoc()) {
        if (file_exists($row['image'])) unlink($row['image']);
    }
    $conn->query("DELETE FROM food_menu WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['foodId'] ?? '';
    $name = $_POST['foodName'] ?? '';
    $desc = $_POST['foodDesc'] ?? '';
    $price = $_POST['foodPrice'] ?? 0;
    $imageName = $_FILES['foodImage']['name'] ?? '';
    $imageTmp = $_FILES['foodImage']['tmp_name'] ?? '';
    $targetPath = '';

    if ($name && $desc && $price > 0) {
        if ($imageTmp) {
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $targetPath = $uploadDir . time() . '_' . basename($imageName);
            move_uploaded_file($imageTmp, $targetPath);
        }

        if ($id) {
            // UPDATE
            if ($targetPath) {
                $stmt = $conn->prepare("UPDATE food_menu SET name=?, description=?, price=?, image=? WHERE id=?");
                $stmt->bind_param("ssdsi", $name, $desc, $price, $targetPath, $id);
            } else {
                $stmt = $conn->prepare("UPDATE food_menu SET name=?, description=?, price=? WHERE id=?");
                $stmt->bind_param("ssdi", $name, $desc, $price, $id);
            }
        } else {
            // INSERT
            $stmt = $conn->prepare("INSERT INTO food_menu (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $name, $desc, $price, $targetPath);
        }

        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Please fill all required fields correctly.";
    }
}

$editItem = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM food_menu WHERE id=$id");
    $editItem = $res->fetch_assoc();
}

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
  <style>
    img.card-img-top { max-height: 200px; object-fit: cover; }
  </style>
</head>
<body>

<?php include("../index/navbar.php"); ?>

<section class="menu-section py-5">
  <div class="container">
    <h2 class="text-danger text-center mb-4"> Food Menu</h2>

    <!-- Error -->
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Search -->
    <input type="text" class="form-control mb-4" id="searchInput" placeholder="Search food by name...">

    <!-- Form -->
    <form class="row g-3 mb-4" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="foodId" value="<?= $editItem['id'] ?? '' ?>">
      <div class="col-md-3">
        <input type="text" name="foodName" class="form-control" placeholder="Food Name" required value="<?= $editItem['name'] ?? '' ?>" />
      </div>
      <div class="col-md-3">
        <input type="text" name="foodDesc" class="form-control" placeholder="Description" required value="<?= $editItem['description'] ?? '' ?>" />
      </div>
      <div class="col-md-2">
        <input type="number" name="foodPrice" class="form-control" step="0.01" placeholder="Price ($)" required value="<?= $editItem['price'] ?? '' ?>" />
      </div>
      <div class="col-md-2">
        <input type="file" name="foodImage" class="form-control" accept="image/*" <?= isset($editItem) ? '' : 'required' ?> />
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-<?= isset($editItem) ? 'primary' : 'danger' ?> w-100"><?= isset($editItem) ? 'Update' : 'Add' ?> Food</button>
      </div>
    </form>

    <!-- Food Items -->
    <div class="row row-cols-1 row-cols-md-2 g-4" id="menuList">
      <?php if (empty($menuItems)): ?>
        <p class="text-center">No food items found.</p>
      <?php else: ?>
        <?php foreach ($menuItems as $food): ?>
          <div class="col food-card">
            <div class="card h-100">
              <?php if (!empty($food['image']) && file_exists($food['image'])): ?>
                <img src="<?= htmlspecialchars($food['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($food['name']) ?>" />
              <?php endif; ?>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($food['name']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($food['description']) ?></p>
                <p class="card-text fw-bold">$<?= number_format($food['price'], 2) ?></p>
                <div class="d-flex gap-2">
                  <a href="?edit=<?= $food['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="?delete=<?= $food['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?')">Delete</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include("../index/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('searchInput').addEventListener('keyup', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#menuList .food-card').forEach(card => {
      const text = card.innerText.toLowerCase();
      card.style.display = text.includes(val) ? '' : 'none';
    });
  });
</script>
</body>
</html>

<?php $conn->close(); ?>
