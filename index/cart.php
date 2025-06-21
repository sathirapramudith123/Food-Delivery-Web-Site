<?php
session_start();
include 'database.php';

$msg = '';

// Simulated login for demo purposes — replace with actual user session logic
$userId = $_SESSION['user_id'] ?? 1;
$userName = $_SESSION['user_name'] ?? 'Guest';

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

        $check = $conn->prepare("SELECT id, quantity FROM cart WHERE food_id = ? AND user_id = ?");
        $check->bind_param("ii", $foodId, $userId);
        $check->execute();
        $checkRes = $check->get_result();

        if ($row = $checkRes->fetch_assoc()) {
            $newQty = $row['quantity'] + $quantity;
            $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $upd->bind_param("ii", $newQty, $row['id']);
            $upd->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO cart (user_id, food_id, food_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("iisdi", $userId, $foodId, $name, $price, $quantity);
            $ins->execute();
        }

        $msg = "Item added to cart.";
    }
}

// Handle Update Quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart_id'])) {
    $id = (int)$_POST['update_cart_id'];
    $qty = max(1, (int)$_POST['update_quantity']);

    $up = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $up->bind_param("iii", $qty, $id, $userId);
    $up->execute();
    $msg = "Quantity updated.";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $del->bind_param("ii", $id, $userId);
    $del->execute();
    $msg = "Item deleted.";
}

// Handle Place Order
if (isset($_POST['place_order'])) {
    $cartItems = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $cartItems->bind_param("i", $userId);
    $cartItems->execute();
    $result = $cartItems->get_result();

    while ($item = $result->fetch_assoc()) {
        $cartId = $item['id'];
        $foodName = $item['food_name'];
        $quantity = $item['quantity'];
        $total = $item['price'] * $quantity;

        $insert = $conn->prepare("INSERT INTO orders (cart_id, user_id, food_name, quantity, total, user_name, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $insert->bind_param("iisids", $cartId, $userId, $foodName, $quantity, $total, $userName);
        $insert->execute();
    }

    // Clear user's cart only
    $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear->bind_param("i", $userId);
    $clear->execute();

    $msg = "Order placed successfully!";
}

// Handle Search
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND food_name LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("is", $userId, $like);
    $stmt->execute();
    $items = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $items = $stmt->get_result();
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
    <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search food name..." value="<?= htmlspecialchars($search) ?>">
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
        <?php 
          $grandTotal = 0;
          while ($item = $items->fetch_assoc()): 
            $itemTotal = $item['price'] * $item['quantity'];
            $grandTotal += $itemTotal;
        ?>
          <tr>
            <td><?= htmlspecialchars($item['food_name']) ?></td>
            <td>
              <form method="POST" class="d-flex">
                <input type="hidden" name="update_cart_id" value="<?= $item['id'] ?>">
                <input type="number" name="update_quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control me-2" style="width: 80px;">
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
              </form>
            </td>
            <td>$<?= number_format($itemTotal, 2) ?></td>
            <td>
              <a href="?delete=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>

        <!-- Place Order Button Row -->
        <tr>
          <td colspan="3" class="text-end fw-bold">Total: $<?= number_format($grandTotal, 2) ?></td>
          <td>
            <form method="POST">
              <button type="submit" name="place_order" class="btn btn-success w-100">Place Order</button>
            </form>
          </td>
        </tr>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include("../index/footer.php"); ?>
</body>
</html>

<?php $conn->close(); ?>
