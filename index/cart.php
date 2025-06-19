<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cart (CRUD) - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/cart.css" />
</head>
<body>

<?php include ("../index/navbar.php"); ?>

  <!-- Cart Section -->
  <section class="py-5 cart-section">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Cart Page</h2>

      <!-- Add Item Form -->
      <form id="addForm" class="row g-3 mb-4">

        <div class="col-md-3">
          <input type="number" id="itemQuantity" class="form-control" placeholder="Quantity" min="1" required />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-danger w-100">Add Item</button>
        </div>
      </form>

      <!-- Cart Display -->
      <div id="cartList" class="row row-cols-1 row-cols-md-2 g-3"></div>

      <!-- Total -->
      <div class="text-end mt-4">
        <h5>Total: <span id="cartTotal" class="text-danger">$0.00</span></h5>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/cart.js"></script>

<?php include ("../index/footer.php"); ?>
</body>
</html>
