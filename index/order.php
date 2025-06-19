<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Orders - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/order.css" />
</head>
<body>

  <?php include ("../index/navbar.php"); ?>
  <!-- Order Management Section -->
  <section class="py-5 order-section">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Manage Orders</h2>

      <!-- Order Form -->
      <form id="orderForm" class="row g-3 mb-4">
        <div class="col-md-3">
          <input type="text" id="customerName" class="form-control" placeholder="Customer Name" required />
        </div>
        <div class="col-md-3">
          <input type="text" id="orderItem" class="form-control" placeholder="Order Item" required />
        </div>
        <div class="col-md-2">
          <input type="number" id="orderQuantity" class="form-control" placeholder="Qty" min="1" required />
        </div>
        <div class="col-md-2">
          <select id="orderStatus" class="form-select" required>
            <option value="Pending">Pending</option>
            <option value="Preparing">Preparing</option>
            <option value="Delivered">Delivered</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-danger w-100">Add Order</button>
        </div>
      </form>

      <!-- Orders Table -->
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Item</th>
              <th>Quantity</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="orderTableBody"></tbody>
        </table>
      </div>
    </div>
  </section>

  

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/order.js"></script>

  <?php include ("../index/footer.php"); ?>
</body>
</html>
