<?php
include 'database.php';

$error = '';
$uploadDir = 'uploads/';

// Disabled delete operation
// if (isset($_GET['delete'])) {
//     $id = (int)$_GET['delete'];
//     $result = $conn->query("SELECT image FROM food_menu WHERE id=$id");
//     if ($result && $row = $result->fetch_assoc()) {
//         if (!empty($row['image']) && file_exists($row['image'])) unlink($row['image']);
//     }
//     $conn->query("DELETE FROM food_menu WHERE id=$id");
//     header("Location: " . $_SERVER['PHP_SELF']);
//     exit;
// }

// Disabled form submission for create/update
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $id = $_POST['foodId'] ?? '';
//     $name = $_POST['foodName'] ?? '';
//     $desc = $_POST['foodDesc'] ?? '';
//     $price = $_POST['foodPrice'] ?? 0;
//     $imageName = $_FILES['foodImage']['name'] ?? '';
//     $imageTmp = $_FILES['foodImage']['tmp_name'] ?? '';
//     $targetPath = '';

//     if ($name && $desc && $price > 0) {
//         if ($imageTmp) {
//             if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
//             $targetPath = $uploadDir . time() . '_' . basename($imageName);
//             move_uploaded_file($imageTmp, $targetPath);
//         }

//         if ($id) {
//             if ($targetPath) {
//                 $stmt = $conn->prepare("UPDATE food_menu SET name=?, description=?, price=?, image=? WHERE id=?");
//                 $stmt->bind_param("ssdsi", $name, $desc, $price, $targetPath, $id);
//             } else {
//                 $stmt = $conn->prepare("UPDATE food_menu SET name=?, description=?, price=? WHERE id=?");
//                 $stmt->bind_param("ssdi", $name, $desc, $price, $id);
//             }
//         } else {
//             $stmt = $conn->prepare("INSERT INTO food_menu (name, description, price, image) VALUES (?, ?, ?, ?)");
//             $stmt->bind_param("ssds", $name, $desc, $price, $targetPath);
//         }

//         $stmt->execute();
//         $stmt->close();
//         header("Location: " . $_SERVER['PHP_SELF']);
//         exit;
//     } else {
//         $error = "Please fill all required fields correctly.";
//     }
// }

// Disable editing mode
$editItem = null;

$result = $conn->query("SELECT * FROM food_menu ORDER BY id DESC");
$menuItems = [];
if ($result) {
    $menuItems = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Food Menu - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    .card-img-top {
      max-height: 180px;
      object-fit: cover;
      border-top-left-radius: 0.5rem;
      border-top-right-radius: 0.5rem;
    }

    .food-card .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .food-card .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    /* Removed form styles since form is removed */

    .search-wrapper {
      position: relative;
    }

    .search-wrapper input {
      padding-left: 2.5rem;
    }

    .search-wrapper i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: gray;
    }

    footer {
      margin-top: 60px;
    }
  </style>
</head>
<body>

<?php include("../index/header.php"); ?>

<section class="menu-section py-5">
  <div class="container">
    <h2 class="text-danger text-center mb-4">Food Menu</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Search Input -->
    <div class="search-wrapper mb-4">
      <i class="bi bi-search"></i>
      <input type="text" class="form-control" id="searchInput" placeholder="Search food by name...">
    </div>

    <!-- Removed Food Form -->

    <!-- Food Cards -->
    <div class="row row-cols-1 row-cols-md-2 g-4" id="menuList">
      <?php if (empty($menuItems)): ?>
        <p class="text-center">No food items found.</p>
      <?php else: ?>
        <?php foreach ($menuItems as $food): ?>
          <div class="col food-card">
            <div class="card h-100 border-0 shadow-sm">
              <?php if (!empty($food['image']) && file_exists($food['image'])): ?>
                <img src="<?= htmlspecialchars($food['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($food['name']) ?>" />
              <?php endif; ?>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($food['name']) ?></h5>
                <p class="card-text text-muted"><?= htmlspecialchars($food['description']) ?></p>
                <span class="badge bg-success mb-2">$<?= number_format($food['price'], 2) ?></span>
                <div class="d-flex gap-2 flex-wrap">
                  <!-- Removed Edit and Delete buttons -->
                  
                  <form method="POST">
                    <input type="hidden" name="cart_food_id" value="<?= $food['id'] ?>">
                    <input type="hidden" name="cart_quantity" value="1">
                    <button type="submit" formaction="cart.php" class="btn btn-sm btn-success">
                      <i class="bi bi-cart-plus-fill me-1"></i>Add to Cart
                    </button>
                  </form>
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
