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

  <!-- Message -->
  <div class="alert alert-info text-center">
    Sample message: Item added to cart.
  </div>

  <!-- Search Form -->
  <form class="mb-4 d-flex justify-content-center">
    <input type="text" class="form-control w-25 me-2" placeholder="Search food name...">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <!-- Cart Items -->
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
      <tr>
        <td>Burger</td>
        <td>
          <form class="d-flex">
            <input type="number" value="2" min="1" class="form-control me-2" style="width: 80px;">
            <button type="submit" class="btn btn-sm btn-primary">Update</button>
          </form>
        </td>
        <td>$12.00</td>
        <td>
          <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
          <form class="mt-2">
            <button type="submit" class="btn btn-sm btn-outline-success">Place Order</button>
          </form>
        </td>
      </tr>
      <tr>
        <td>Pizza</td>
        <td>
          <form class="d-flex">
            <input type="number" value="1" min="1" class="form-control me-2" style="width: 80px;">
            <button type="submit" class="btn btn-sm btn-primary">Update</button>
          </form>
        </td>
        <td>$8.50</td>
        <td>
          <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
          <form class="mt-2">
            <button type="submit" formaction="order.php" class="btn btn-sm btn-outline-success">Place Order</button>
          </form>
        </td>
      </tr>
    </tbody>
  </table>

  <h5 class="text-end">Total: <span class="text-success">$20.50</span></h5>
</div>
<?php include("../index/footer.php"); ?>

</body>
</html>
