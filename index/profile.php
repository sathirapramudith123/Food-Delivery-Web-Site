<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  header("Location: login.php");
  exit();
}

include 'database.php';

$message = '';
$user = null;
$userId = $_SESSION['user_id'];

// Handle form submit (Create/Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
  $phone = trim($_POST['phone']);
  $uploadDir = 'uploads/';
  $profilePic = '';

  // File upload validation
  if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
      $profilePic = $uploadDir . uniqid('img_') . '.' . $ext;
      move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profilePic);
    }
  }

  // Update only current user
  $query = "UPDATE users SET name = ?, email = ?, phone = ?";
  $types = "sss";
  $params = [$name, $email, $phone];

  if ($password) {
    $query .= ", password = ?";
    $types .= "s";
    $params[] = $password;
  }

  if ($profilePic) {
    $query .= ", profile_pic = ?";
    $types .= "s";
    $params[] = $profilePic;
  }

  $query .= " WHERE id = ?";
  $types .= "i";
  $params[] = $userId;

  $stmt = $conn->prepare($query);
  $stmt->bind_param($types, ...$params);
  if ($stmt->execute()) {
    $message = "Profile updated successfully!";
  } else {
    $message = "Error updating profile. Please try again.";
  }
  $stmt->close();
}

// Read current user's profile
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle self-deletion
if (isset($_GET['delete']) && intval($_GET['delete']) === $userId) {
  $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $stmt->close();
  session_destroy();
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #4e73df;
      --secondary-color: #f8f9fc;
      --accent-color: #2e59d9;
      --text-color: #5a5c69;
    }
    body {
      background-color: var(--secondary-color);
      color: var(--text-color);
    }
    .profile-card {
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      border: none;
    }
    .profile-header {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
      color: white;
      border-radius: 15px 15px 0 0 !important;
      padding: 1.5rem;
    }
    .profile-pic-container {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 5px solid white;
      overflow: hidden;
      margin: -75px auto 20px;
      background-color: #fff;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .profile-pic {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .btn-primary-custom {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    .btn-primary-custom:hover {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
    }
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    .nav-pills .nav-link.active {
      background-color: var(--primary-color);
    }
    .nav-pills .nav-link {
      color: var(--text-color);
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card profile-card mb-4">
        <div class="card-header profile-header text-center">
          <h3><i class="bi bi-person-circle me-2"></i>My Profile</h3>
        </div>
        
        <div class="card-body p-4">
          <?php if (!empty($message)): ?>
            <div class="alert alert-dismissible alert-<?= strpos($message, 'successfully') !== false ? 'success' : 'danger' ?>">
              <?= htmlspecialchars($message) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <div class="profile-pic-container">
            <?php if (!empty($user['profile_pic'])): ?>
              <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile" class="profile-pic">
            <?php else: ?>
              <div class="d-flex align-items-center justify-content-center h-100">
                <i class="bi bi-person-fill" style="font-size: 5rem; color: #ddd;"></i>
              </div>
            <?php endif; ?>
          </div>
          
          <form method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label"><i class="bi bi-person-fill me-2"></i>Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label"><i class="bi bi-lock-fill me-2"></i>Password (leave blank to keep current)</label>
                <div class="input-group">
                  <input type="password" name="password" id="password" class="form-control" placeholder="New password">
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye-fill"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label"><i class="bi bi-telephone-fill me-2"></i>Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
              </div>
            </div>
            
            <div class="mb-4">
              <label class="form-label"><i class="bi bi-image-fill me-2"></i>Profile Picture</label>
              <input type="file" name="profile_pic" class="form-control" accept="image/*">
              <small class="text-muted">Max size 2MB. Allowed formats: JPG, PNG, GIF</small>
            </div>
            
            <div class="d-flex justify-content-between">
              <button type="submit" name="save" class="btn btn-primary-custom text-white">
                <i class="bi bi-save-fill me-2"></i>Save Changes
              </button>
              <a href="?delete=<?= $userId ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.')">
                <i class="bi bi-trash-fill me-2"></i>Delete Account
              </a>
            </div>
          </form>
        </div>
      </div>
      
      <div class="card profile-card">
        <div class="card-header bg-white">
          <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Account Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong><i class="bi bi-person-badge-fill me-2"></i>User ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
              <p><strong><i class="bi bi-calendar-fill me-2"></i>Member Since:</strong> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
            </div>
            <div class="col-md-6">
              <p><strong><i class="bi bi-people-fill me-2"></i>Role:</strong> <?= htmlspecialchars(ucfirst($user['role'])) ?></p>
              <p><strong><i class="bi bi-clock-fill me-2"></i>Last Updated:</strong>
                <?php if (!empty($user['updated_at'])): ?>
                  <?= date('F j, Y g:i A', strtotime($user['updated_at'])) ?>
                <?php else: ?>
                  N/A
                <?php endif; ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle password visibility
  document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-eye-slash-fill"></i>';
  });
  
  // Auto-dismiss alerts after 5 seconds
  setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);
</script>
</body>
</html>