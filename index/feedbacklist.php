<?php
// Connect to database
include 'database.php';

// Search handling
$searchTerm = trim($_GET['search'] ?? '');
$whereClause = '';
$params = [];
$types = '';

if ($searchTerm !== '') {
    $whereClause = "WHERE comment LIKE ?";
    $params[] = '%' . $searchTerm . '%';
    $types .= 's';
}

// Build query
$sql = "SELECT * FROM feedback " . $whereClause . " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $feedbacks = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Feedback - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .star-rating .fa-star.checked { color: gold; }
  </style>
</head>
<body>

  <?php include("../index/header.php"); ?>

  <section class="feedback-section py-5">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Customer Feedback</h2>

      <!-- Search Form -->
      <form method="GET" class="row mb-4">
        <div class="col-md-10">
          <input type="text" name="search" class="form-control"
                 placeholder="Search feedback..." value="<?= htmlspecialchars($searchTerm) ?>" />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-danger w-100">Search</button>
        </div>
      </form>

      <!-- Feedback List -->
      <div class="list-group">
        <?php if (empty($feedbacks)): ?>
          <p class="text-center">No feedback found.</p>
        <?php else: ?>
          <?php foreach ($feedbacks as $fb): ?>
            <div class="list-group-item">
              <p><?= htmlspecialchars($fb['comment']) ?></p>
              <p>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <span class="fa fa-star <?= $i <= $fb['rating'] ? 'checked' : '' ?>"></span>
                <?php endfor; ?>
              </p>
              <small class="text-muted">Posted on <?= $fb['created_at'] ?></small>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php include("../index/footer.php"); ?>

</body>
</html>

<?php
$conn->close();
?>
