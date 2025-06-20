<?php
include 'database.php';

// Add to cart
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adding a new item
    if (isset($_POST['foodId'], $_POST['quantity']) && !isset($_POST['updateId'])) {
        $foodId = (int)$_POST['foodId'];
        $quantity = max(1, (int)$_POST['quantity']);

        $food = $conn->query("SELECT * FROM food_menu WHERE id = $foodId")->fetch_assoc();
        if (!$food) die("Invalid food ID");

        $total = $food['price'] * $quantity;

        // Check if already in cart
        $existing = $conn->query("SELECT * FROM cart WHERE food_id = $foodId")->fetch_assoc();
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $newTotal = $newQty * $food['price'];
            $conn->query("UPDATE cart SET quantity = $newQty, total = $newTotal WHERE food_id = $foodId");
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (food_id, quantity, total) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $foodId, $quantity, $total);
            $stmt->execute();
        }
        header("Location: cart.php");
        exit;
    }

    // Update quantity
    if (isset($_POST['updateId'], $_POST['quantity'])) {
        $cartId = (int)$_POST['updateId'];
        $quantity = max(1, (int)$_POST['quantity']);
        $cartItem = $conn->query("SELECT food_id FROM cart WHERE id = $cartId")->fetch_assoc();

        if ($cartItem) {
            $foodId = $cartItem['food_id'];
            $food = $conn->query("SELECT price FROM food_menu WHERE id = $foodId")->fetch_assoc();
            $total = $quantity * $food['price'];
            $conn->query("UPDATE cart SET quantity = $quantity, total = $total WHERE id = $cartId");
        }
        header("Location: cart.php");
        exit;
    }
}

// Delete item
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM cart WHERE id = $id");
    header("Location: cart.php");
    exit;
}

// Fetch cart items
$cartItems = $conn->query("
    SELECT c.id, c.food_id, c.quantity, c.total, f.name
    FROM cart c
    JOIN food_menu f ON c.food_id = f.id
    ORDER BY c.id DESC
")->fetch_all(MYSQLI_ASSOC);

$cartTotal = array_sum(array_column($cartItems, 'total'));
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

  <?php if (empty($cartItems)): ?>
    <p class="text-center">Your cart is empty.</p>
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
        <?php foreach ($cartItems as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td>
            <form method="POST" class="d-flex">
              <input type="hidden" name="updateId" value="<?= $item['id'] ?>">
              <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control me-2" style="width: 80px;">
              <button type="submit" class="btn btn-sm btn-primary">Update</button>
            </form>
          </td>
          <td>$<?= number_format($item['total'], 2) ?></td>
          <td>
            <a href="?delete=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="text-end">
      <h5>Total: <span class="text-success">$<?= number_format($cartTotal, 2) ?></span></h5>
    </div>
  <?php endif; ?>
</div>

<?php include("../index/footer.php"); ?>
</body>
</html>

<?php $conn->close(); ?>
