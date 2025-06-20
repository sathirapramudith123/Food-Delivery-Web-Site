<?php
session_start();
include 'database.php';

// Dummy session for testing
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Admin';

// Handle Update
if (isset($_POST['update'])) {
    $order_id = (int)$_POST['order_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    $order = $conn->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();
    if ($order) {
        $new_total = $quantity * ($order['total'] / $order['quantity']);
        $stmt = $conn->prepare("UPDATE orders SET quantity = ?, total = ? WHERE id = ?");
        $stmt->bind_param("idi", $quantity, $new_total, $order_id);
        $stmt->execute();
    }
    header("Location: orders.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id = $id");
    header("Location: orders.php");
    exit;
}

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = "SELECT * FROM orders";
if (!empty($search)) {
    $query .= " WHERE food_name LIKE '%$search%' OR users_name LIKE '%$search%'";
}
$query .= " ORDER BY created_at DESC";
$orders = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Get edit data
$edit_order = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_order = $conn->query("SELECT * FROM orders WHERE id = $id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
<div class="container py-5">
    <h2 class="text-center mb-4">Order Management</h2>

    <!-- Search -->
    <form method="GET" class="d-flex justify-content-center mb-4">
        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search by food or user..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
    </form>

    <!-- Edit Form -->
    <?php if ($edit_order): ?>
    <div class="card mb-5">
        <div class="card-header bg-info text-white">
            Edit Order
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $edit_order['id'] ?>">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label>Food Item</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($edit_order['food_name']) ?>" disabled>
                    </div>
                    <div class="col-md-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required value="<?= $edit_order['quantity'] ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" name="update" class="btn btn-info w-100">Update Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <?php if (empty($orders)): ?>
        <p class="text-center">No orders found.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Food</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>User</th>
                    <th>Ordered At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['food_name']) ?></td>
                    <td><?= $order['quantity'] ?></td>
                    <td>$<?= number_format($order['total'], 2) ?></td>
                    <td><?= htmlspecialchars($order['users_name']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td>
                        <a href="?edit=<?= $order['id'] ?>" class="btn btn-sm btn-outline-info">Edit</a>
                        <a href="?delete=<?= $order['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
