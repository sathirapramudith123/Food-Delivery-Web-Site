<?php
include 'database.php'; // Adjust path to your DB connection

$success = '';
$error = '';

// Handle form submission to save contact message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $success = "Thank you for contacting us!";
            // Clear POST data so form is empty after submission
            $_POST = [];
        } else {
            $error = "Error saving message. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}

// Handle search input
$searchTerm = trim($_GET['search'] ?? '');

// Fetch messages (filtered by search if applicable)
$messages = [];
if ($searchTerm !== '') {
    $likeTerm = "%$searchTerm%";
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE name LIKE ? OR email LIKE ? OR message LIKE ? ORDER BY created_at DESC");
    $stmt->bind_param("sss", $likeTerm, $likeTerm, $likeTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    if ($result) {
        $messages = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us - FoodExpress</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style/contact.css" />
</head>
<body>

<?php include("../index/header.php"); ?>

<section class="contact-section py-5">
  <div class="container">
    <h2 class="text-center mb-4">Get in Touch</h2>
    <p class="text-center mb-5">Have questions, feedback, or partnership inquiries? We'd love to hear from you!</p>

    <?php if ($success): ?>
      <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
      <!-- Contact Form -->
      <div class="col-md-6 mb-4">
        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
          <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Your Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Your Message</label>
            <textarea class="form-control" id="message" name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-danger">Send Message</button>
        </form>
      </div>

      <!-- Contact Details and Search -->
      <div class="col-md-6">
        <h5>Contact Details</h5>
        <p><strong>Email:</strong> foodexpress@gmail.com</p>
        <p><strong>Phone:</strong> +9447 639 0724</p>
        <p><strong>Address:</strong> 12 Jaya Street, Malabe Town, Sri Lanka</p>

        <h5 class="mt-4">Follow Us</h5>
        <a href="#" class="btn btn-outline-dark btn-sm me-2">Facebook</a>
        <a href="#" class="btn btn-outline-dark btn-sm me-2">Instagram</a>
        <a href="#" class="btn btn-outline-dark btn-sm">Twitter</a>

        <!-- Search form for messages -->
        <form method="GET" class="mt-4 d-flex" role="search">
          <input
            class="form-control me-2"
            type="search"
            name="search"
            placeholder="Search messages..."
            aria-label="Search"
            value="<?= htmlspecialchars($searchTerm) ?>"
          >
          <button class="btn btn-primary" type="submit">Search</button>
          <?php if ($searchTerm !== ''): ?>
            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary ms-2">Clear</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Display Messages -->
    <div class="mt-5">
      <h3 class="mb-3">Messages <?= $searchTerm ? "(filtered by '" . htmlspecialchars($searchTerm) . "')" : "" ?></h3>

      <?php if (empty($messages)): ?>
        <p>No messages found.</p>
      <?php else: ?>
        <div class="list-group">
          <?php foreach ($messages as $msg): ?>
            <div class="list-group-item mb-2">
              <h5><?= htmlspecialchars($msg['name']) ?> <small class="text-muted">&lt;<?= htmlspecialchars($msg['email']) ?>&gt;</small></h5>
              <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
              <small class="text-muted">Sent on <?= htmlspecialchars($msg['created_at']) ?></small>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include("../index/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
