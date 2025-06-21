<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../index/navbar.php"); ?>

<div class="container py-5">
    <h2 class="text-center mb-4">Order Management</h2>

    <!-- Search -->
    <form method="GET" class="d-flex justify-content-center mb-4">
        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search by food or user..." value="">
        <button class="btn btn-primary">Search</button>
    </form>

    <!-- Edit Form (Static Example) -->
    <div class="card mb-5">
        <div class="card-header bg-info text-white">Edit Order</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="order_id" value="">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label>Food Item</label>
                        <input type="text" class="form-control" value="Burger" disabled>
                    </div>
                    <div class="col-md-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required value="2">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" name="update" class="btn btn-info w-100">Update Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Food</th>
                <th>Quantity</th>
                <th>Total ($)</th>
                <th>User</th>
                <th>Ordered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Static Row Example -->
            <tr>
                <td>Burger</td>
                <td>2</td>
                <td>$12.00</td>
                <td>John Doe</td>
                <td>2025-06-21 10:30</td>
                <td>
                    <a href="#" class="btn btn-sm btn-outline-info">Edit</a>
                    <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                </td>
            </tr>
            <!-- Add more static rows as needed -->
        </tbody>
    </table>
</div>
<?php include("../index/footer.php"); ?>

</body>
</html>
