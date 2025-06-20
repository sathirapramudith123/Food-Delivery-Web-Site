<?php
include 'database.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ========== Handle POST requests ==========
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ADD to cart
    if (isset($_POST['foodId'], $_POST['quantity']) && !isset($_POST['updateId'])) {
        $foodId = (int)$_POST['foodId'];
        $quantity = max(1, (int)$_POST['quantity']);

        $food = $conn->query("SELECT * FROM food_menu WHERE id = $foodId")->fetch_assoc();
        if (!$food) die("Invalid food ID");

        $total = $food['price'] * $quantity;

        // Check if item already in cart
        $existing = $conn->query("SELECT * FROM cart WHERE food_id = $foodId AND user_id = $userId")->fetch_assoc();
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $newTotal = $newQty * $food['price'];
            $conn->query("UPDATE cart SET quantity = $newQty, total = $newTotal WHERE food_id = $foodId AND user_id = $userId");
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, food_id, quantity, total) VALUES (?, ?, ?, ?)");
            if (!$stmt) die("Prepare failed: " . $conn->error);
            $stmt->bind_param("iiid", $userId, $foodId, $quantity, $total);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: cart.php");
        exit;
    }

    // UPDATE quantity
    if (isset($_POST['updateId'], $_POST['quantity'])) {
        $cartId = (int)$_POST['updateId'];
        $quantity = max(1, (int)$_POST['quantity']);

        $cartItem = $conn->query("SELECT food_id FROM cart WHERE id = $cartId AND user_id = $userId")->fetch_assoc();
        if ($cartItem) {
            $foodId = $cartItem['food_id'];
            $food = $conn->query("SELECT price FROM food_menu WHERE id = $foodId")->fetch_assoc();
            if (!$food) die("Food not found");

            $total = $quantity * $food['price'];
            $conn->query("UPDATE cart SET quantity = $quantity, total = $total WHERE id = $cartId AND user_id = $userId");
        }

        header("Location: cart.php");
        exit;
    }

    // PLACE order
    if (isset($_POST['order_food_id'], $_POST['order_quantity'])) {
        $foodId = (int)$_POST['order_food_id'];
        $quantity = max(1, (int)$_POST['order_quantity']);

        $food = $conn->query("SELECT price FROM food_menu WHERE id = $foodId")->fetch_assoc();
        if (!$food) die("Invalid food ID");

        $total = $food['price'] * $quantity;

        $stmt = $conn->prepare("INSERT INTO orders (user_id, food_id, quantity, total, order_date) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) die("Prepare failed (order insert): " . $conn->error);
        $stmt->bind_param("iiid", $userId, $foodId, $quantity, $total);
        $stmt->execute();
        $stmt->close();

        $conn->query("DELETE FROM cart WHERE food_id = $foodId AND user_id = $userId");

        header("Location: cart.php");
        exit;
    }
}

// ========== Handle DELETE request ==========
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM cart WHERE id = $id AND user_id = $userId");
    header("Location: cart.php");
    exit;
}

// ========== Handle SEARCH ==========
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($searchTerm !== '') {
    $stmt = $conn->prepare("
        SELECT c.id, c.food_id, c.quantity, c.total, f.name, f.price
        FROM cart c
        JOIN food_menu f ON c.food_id = f.id
        WHERE c.user_id = ? AND f.name LIKE ?
        ORDER BY c.id DESC
    ");

    if (!$stmt) die("Prepare failed (search): " . $conn->error);

    $likeTerm = '%' . $searchTerm . '%';
    $stmt->bind_param("is", $userId, $likeTerm);
} else {
    $stmt = $conn->prepare("
        SELECT c.id, c.food_id, c.quantity, c.total, f.name, f.price
        FROM cart c
        JOIN food_menu f ON c.food_id = f.id
        WHERE c.user_id = ?
        ORDER BY c.id DESC
    ");

    if (!$stmt) die("Prepare failed (default): " . $conn->error);

    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);
$cartTotal = array_sum(array_column($cartItems, 'total'));
$stmt->close();
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

  <!-- Search Form -->
  <form method="GET" class="mb-4 d-flex justify-content-center">
    <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search food name..." value="<?= htmlspecialchars($searchTerm) ?>">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <?php if (empty($cartItems)): ?>
    <p class="text-center">Your cart is empty<?= $searchTerm ? " for '<strong>" . htmlspecialchars($searchTerm) . "</strong>'" : "" ?>.</p>
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
            <form method="POST" class="mt-2">
              <input type="hidden" name="order_food_id" value="<?= $item['food_id'] ?>">
              <input type="hidden" name="order_quantity" value="<?= $item['quantity'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-success">Place Order</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <h5 class="text-end">Total: <span class="text-success">$<?= number_format($cartTotal, 2) ?></span></h5>
  <?php endif; ?>
</div>

<?php include("../index/footer.php"); ?>
</body>
</html>

<?php $conn->close(); ?>
