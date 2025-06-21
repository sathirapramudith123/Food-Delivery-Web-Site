<?php
include 'database.php';

$msg = '';

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_food_id'])) {
    $foodId = (int)$_POST['cart_food_id'];
    $quantity = max(1, (int)($_POST['cart_quantity'] ?? 1));

    $stmt = $conn->prepare("SELECT name, price FROM food_menu WHERE id = ?");
    $stmt->bind_param("i", $foodId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($food = $res->fetch_assoc()) {
        $name = $food['name'];
        $price = $food['price'];

        // Check if already in cart
        $check = $conn->prepare("SELECT id, quantity FROM cart WHERE food_id = ?");
        $check->bind_param("i", $foodId);
        $check->execute();
        $checkRes = $check->get_result();

        if ($row = $checkRes->fetch_assoc()) {
            $newQty = $row['quantity'] + $quantity;
            $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $upd->bind_param("ii", $newQty, $row['id']);
            $upd->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO cart (food_id, food_name, price, quantity) VALUES (?, ?, ?, ?)");
            $ins->bind_param("isdi", $foodId, $name, $price, $quantity);
            $ins->execute();
        }

        $msg = "Item added to cart.";
    }
}

// Handle Update Quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart_id'])) {
    $id = (int)$_POST['update_cart_id'];
    $qty = max(1, (int)$_POST['update_quantity']);
    $up = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $up->bind_param("ii", $qty, $id);
    $up->execute();
    $msg = "Quantity updated.";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM cart WHERE id = $id");
    $msg = "Item deleted.";
}

// Handle Place Order (Clears Cart)
if (isset($_POST['place_order'])) {
    $conn->query("DELETE FROM cart");
    $msg = "Order placed successfully!";
}

// Handle Search
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE food_name LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $items = $stmt->get_result();
} else {
    $items = $conn->query("SELECT * FROM cart");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Cart - FoodExpress</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include("../index/navbar.php"); ?>

<div class="container py-5">
  <h2 class="text-danger text-center mb-4">Your Cart</h2>

  <?php if ($msg): ?>
    <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Search Form -->
  <form method="GET" class="mb-4 d-flex justify-content-center">
    <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search food name..." value="<?= htmlspecialchars($search ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="cart.php" class="btn btn-secondary ms-2">Reset</a>
  </form>

  <?php if ($items->num_rows == 0): ?>
    <p class="text-center">No items found.</p>
  <?php else: ?>
    <table class="table table-bordered align-middle">
      <thead>
        <tr>
          <th>Food Name</th>
          <th>Quantity</th>
          <th>Total ($)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($item = $items->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($item['food_name']) ?></td>
            <td>
              <form method="POST" class="d-flex">
                <input type="hidden" name="update_cart_id" value="<?= $item['id'] ?>">
                <input type="number" name="update_quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control me-2" style="width: 80px;">
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
              </form>
            </td>
            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            <td>
              <a href="?delete=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- Place Order -->
    <form method="POST" class="text-end">
      <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
    </form>
  <?php endif; ?>
</div>

<?php include("../index/footer.php"); ?>
</body>
</html>

<?php $conn->close(); ?>
