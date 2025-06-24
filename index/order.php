<?php
// --- DB CONNECTION ---
include 'database.php'; // Adjust path to your DB connection file

$search = $_GET['search'] ?? '';
$msg = '';

// --- HANDLE UPDATE ---
if (isset($_POST['update']) && isset($_POST['order_id'], $_POST['quantity'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $quantity = max(1, (int)$_POST['quantity']); // min 1 quantity
    $status = $_POST['status'];

    // Fetch current order details to calculate unit price
    $stmt = $conn->prepare("SELECT total, quantity FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($order = $result->fetch_assoc()) {
        // Calculate unit price safely (avoid division by zero)
        $unit_price = $order['quantity'] > 0 ? $order['total'] / $order['quantity'] : 0;
        $new_total = $unit_price * $quantity;

        // Update order record
        $update = $conn->prepare("UPDATE orders SET quantity = ?, total = ?, status = ? WHERE id = ?");
        $update->bind_param("idsi", $quantity, $new_total, $status, $order_id);
        $update->execute();

        $msg = "Order updated successfully.";
    }
}

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $msg = "Order deleted successfully.";
}

// --- FETCH ORDERS (SEARCH + DEFAULT) ---
if (!empty($search)) {
    $search_term = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT * FROM orders WHERE food_name LIKE ? OR user_name LIKE ? ORDER BY created_at DESC");
    $stmt->bind_param("ss", $search_term, $search_term);
} else {
    $stmt = $conn->prepare("SELECT * FROM orders ORDER BY created_at DESC");
}
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../index/header.php"); ?>

<div class="container py-5">
    <h2 class="text-center mb-4">Order Management</h2>

    <?php if ($msg): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Search -->
    <form method="GET" class="d-flex justify-content-center mb-4">
        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search by food or user..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <a href="?" class="btn btn-secondary ms-2">Reset</a>
    </form>

    <!-- Edit Form -->
    <?php if (isset($_GET['edit'])):
        $edit_id = (int)$_GET['edit'];
        $edit_stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $edit_stmt->bind_param("i", $edit_id);
        $edit_stmt->execute();
        $edit_result = $edit_stmt->get_result();
        $edit_order = $edit_result->fetch_assoc();
        if ($edit_order):
    ?>
    <div class="card mb-5">
        <div class="card-header bg-info text-white">Edit Order</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $edit_order['id'] ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Food Item</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($edit_order['food_name']) ?>" disabled>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required value="<?= $edit_order['quantity'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $edit_order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $edit_order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $edit_order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="update" value="1" class="btn btn-info w-100">Update Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; endif; ?>

    <!-- Orders Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Food</th>
                <th>Quantity</th>
                <th>Total ($)</th>
                <th>User</th>
                <th>Ordered At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['food_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>$<?= number_format($row['total'], 2) ?></td>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <select class="form-select form-select-sm" disabled>
                        <option <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option <?= $row['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option <?= $row['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("../index/footer.php"); ?>
</body>
</html>
