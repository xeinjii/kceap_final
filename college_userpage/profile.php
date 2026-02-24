<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle AJAX request for profile info refresh
if (isset($_GET['action']) && $_GET['action'] === 'refresh_profile_info') {
    header('Content-Type: application/json');
    $email = $_SESSION['email'] ?? null;
    if ($email) {
        $stmt = $conn->prepare("SELECT status, semester FROM college_account WHERE email = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($db_status, $db_semester);
            if ($stmt->fetch()) {
                echo json_encode([
                    'status' => 'success',
                    'account_status' => $db_status ?? 'N/A',
                    'semester' => $db_semester ?? 'N/A'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    }
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? null;

// Fetch fresh data from database on every page load to get latest updates
$first_name = $_SESSION['first_name'] ?? '';
$middle_name = $_SESSION['middle_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';
$course = $_SESSION['course'] ?? '';
$year_level = $_SESSION['year_level'] ?? '';
$address = $_SESSION['address'] ?? '';
$phone_number = $_SESSION['phone_number'] ?? '';
$status = '';
$semester = '';

if ($email) {
    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, course, year_level, address, phone_number, status, semester FROM college_account WHERE email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($db_first, $db_middle, $db_last, $db_course, $db_year, $db_address, $db_phone, $db_status, $db_semester);
        if ($stmt->fetch()) {
            $first_name = $db_first;
            $middle_name = $db_middle;
            $last_name = $db_last;
            $course = $db_course;
            $year_level = $db_year;
            $address = $db_address;
            $phone_number = $db_phone;
            $status = $db_status;
            $semester = $db_semester;
        }
        $stmt->close();
    }
}

$display_status = $status ? htmlspecialchars($status) : 'N/A';
$display_semester = $semester ? htmlspecialchars($semester) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>KCEAP - Profile</title>
<link rel="icon" href="../img/logo.png" type="image/png">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Material+Symbols+Outlined" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 5rem;
}
.navbar { 
  background: #0d6efd !important;
}
.profile-card {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.95);
    border-radius: 1.2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 2rem;
    width: 95%;
    max-width: 900px;
    margin-bottom: 2rem;
}
.field-icon { font-size:20px; margin-right:8px; color:#0d6efd; }
.field-label { font-weight:600; color:#222; margin-bottom:0.2rem; }
.field-value { color:#444; border-bottom:1px solid #ddd; padding:0.3rem 0; margin-bottom:1rem; word-wrap: break-word; }
.section-title { font-weight:600; font-size:1.25rem; color:#0d6efd; margin:1rem 0; }
.btn-primary { background-color:#0d6efd; border:none; }
.btn-primary:hover { background-color:#0b5ed7; }
.btn-secondary { background-color:#6c757d; border:none; }
.btn-secondary:hover { background-color:#5c636a; }

/* Footer for mobile */
.footer-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #fff;
  border-top: 1px solid #ddd;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 0.5rem 0;
  box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
  z-index: 100;
  flex-wrap: wrap;
}

.footer-nav a {
  color: #0d6efd;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
}

.footer-nav a .material-symbols-outlined {
  font-size: 1.5rem;
  margin-bottom: 2px;
}


</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="mainpage.php">
      <img src="../img/logo.png" width="40" height="40" class="me-2 rounded-circle"> KCEAP
    </a>
  </div>
</nav>

<div class="profile-card">
  <h3 class="text-center mb-4 fw-semibold">👤 Student Profile</h3>
  <div class="row g-3">
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">badge</span>First Name</p>
      <p class="field-value"><?php echo htmlspecialchars($first_name); ?></p></div>
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">person</span>Middle Name</p>
      <p class="field-value"><?php echo htmlspecialchars($middle_name); ?></p></div>
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">face</span>Last Name</p>
      <p class="field-value"><?php echo htmlspecialchars($last_name); ?></p></div>
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">mail</span>Email</p>
      <p class="field-value"><?php echo htmlspecialchars($_SESSION['email']); ?></p></div>
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">school</span>Course</p>
      <p class="field-value"><?php echo htmlspecialchars($course); ?></p></div>
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">calendar_month</span>Year Level</p>
      <p class="field-value"><?php echo htmlspecialchars($year_level); ?></p></div>
    <div class="col-md-6"><p class="field-label"><span class="material-symbols-outlined field-icon">menu_book</span>Semester</p>
      <p class="field-value"><?php echo $display_semester; ?></p></div>
    <div class="col-12"><p class="field-label"><span class="material-symbols-outlined field-icon">home</span>Address</p>
      <p class="field-value"><?php echo htmlspecialchars($address); ?></p></div>
    <div class="col-12"><p class="field-label"><span class="material-symbols-outlined field-icon">call</span>Phone</p>
      <p class="field-value"><?php echo htmlspecialchars($phone_number); ?></p></div>
    <div class="col-12"><p class="field-label"><span class="material-symbols-outlined field-icon">info</span>Status</p>
      <p class="field-value"><?php echo $display_status; ?></p></div>
  </div>

  <hr class="my-4">

  <div class="section-title"><span class="material-symbols-outlined field-icon">lock_reset</span>Change Password</div>

  <?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo ($_SESSION['message_type'] ?? '') === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
      <?php 
        echo htmlspecialchars($_SESSION['message']); 
        unset($_SESSION['message'], $_SESSION['message_type']); 
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <form action="change_password_process.php" method="post" class="row g-3">
    <div class="col-md-4 col-12"><label class="form-label fw-semibold">Current Password</label>
      <input type="password" name="current_password" class="form-control" required></div>

    <div class="col-md-4 col-12"><label class="form-label fw-semibold">New Password</label>
      <input type="password" name="new_password" class="form-control" required minlength="8" placeholder="At least 8 characters"></div>

    <div class="col-md-4 col-12"><label class="form-label fw-semibold">Confirm New Password</label>
      <input type="password" name="confirm_password" class="form-control" required minlength="8"></div>

    <div class="col-12 mt-3">
      <input type="hidden" name="account_id" value="<?= htmlspecialchars($_SESSION['id'] ?? '') ?>">
      <button type="submit" class="btn btn-primary px-4 mb-2 mb-md-0">Update Password</button>
      <a href="mainpage.php" class="btn btn-secondary px-4 ms-md-2">Back</a>
    </div>
  </form>

  <div class="text-center mt-4">
    <small class="text-muted">If you forgot your current password, please contact the administrator.</small>
  </div>
</div>

<!-- Mobile Footer -->
<div class="footer-nav">
  <a href="profile.php"><span class="material-symbols-outlined">account_circle</span>Profile</a>
  <a href="schedule.php"><span class="material-symbols-outlined">calendar_month</span>Schedule</a>
  <a href="upload_docs.php"><span class="material-symbols-outlined">upload</span>Upload</a>
  <a href="renew.php"><span class="material-symbols-outlined">refresh</span>Renew</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
