<?php
// Connect to database
include 'database.php'; // Adjust path if needed

// Handle create/update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);

    if ($comment !== '' && $rating >= 1 && $rating <= 5) {
        if ($id) {
            // Update existing feedback
            $stmt = $conn->prepare("UPDATE feedback SET comment=?, rating=? WHERE id=?");
            $stmt->bind_param("sii", $comment, $rating, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Insert new feedback
            $stmt = $conn->prepare("INSERT INTO feedback (comment, rating) VALUES (?, ?)");
            $stmt->bind_param("si", $comment, $rating);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit (load data for form)
$editItem = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editItem = $result->fetch_assoc();
    $stmt->close();
}

// Fetch all feedback to display
$feedbacks = [];
$result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
if ($result) {
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
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
  <link rel="stylesheet" href="../style/feedback.css" />
  <style>
    .star-rating .fa-star { cursor: pointer; }
    .star-rating .fa-star.checked { color: gold; }
  </style>
</head>
<body>

  <?php include("../index/navbar.php"); ?>

  <section class="feedback-section py-5">
    <div class="container">
      <h2 class="text-danger text-center mb-4">Customer Feedback</h2>

      <!-- Feedback Form -->
      <form method="POST" class="row g-3 mb-4" id="feedbackForm">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editItem['id'] ?? '') ?>">
        <div class="col-md-6">
          <input type="text" name="comment" class="form-control" placeholder="Your Feedback" required
                 value="<?= htmlspecialchars($editItem['comment'] ?? '') ?>" />
        </div>
        <div class="col-md-4 star-rating d-flex align-items-center" id="starRating">
          <?php
          $currentRating = $editItem['rating'] ?? 0;
          for ($i = 1; $i <= 5; $i++) {
              $class = ($i <= $currentRating) ? 'fa-star checked' : 'fa-star';
              echo "<span class='fa fa-star $class' data-rating='$i'></span>";
          }
          ?>
          <input type="hidden" name="rating" id="ratingInput" value="<?= $currentRating ?>" required />
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-danger w-100"><?= isset($editItem) ? 'Update' : 'Submit' ?></button>
          <?php if (isset($editItem)): ?>
            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary w-100 mt-2">Cancel</a>
          <?php endif; ?>
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
              <div class="mt-2">
                <a href="?edit=<?= $fb['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="?delete=<?= $fb['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this feedback?')">Delete</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Star rating click handler
    const stars = document.querySelectorAll('#starRating .fa-star');
    const ratingInput = document.getElementById('ratingInput');
    stars.forEach(star => {
      star.addEventListener('click', () => {
        const rating = star.getAttribute('data-rating');
        ratingInput.value = rating;
        stars.forEach(s => s.classList.remove('checked'));
        for(let i=0; i<rating; i++) {
          stars[i].classList.add('checked');
        }
      });
    });
  </script>

  <?php include("../index/footer.php"); ?>
</body>
</html>

<?php
$conn->close();
?>
