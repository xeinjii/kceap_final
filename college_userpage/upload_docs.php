<?php
session_start();
require_once '../config/config.php';
// You may want to check if the user is logged in here, similar to profile.php
$applicant_id = $_SESSION['user_id'] ?? $_SESSION['college_user']['account_id'] ?? $_SESSION['account_id'] ?? $_SESSION['id'] ?? null;
if (empty($applicant_id)) {
  header('Location: login.php');
  exit;
}
// Collect applicant information from session
$first_name = $_SESSION['first_name'] ?? '';
$middle_name = $_SESSION['middle_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';
$email = $_SESSION['email'] ?? '';
$school = $_SESSION['school'] ?? '';
$course = $_SESSION['course'] ?? '';
$year_level = $_SESSION['year_level'] ?? '';
$address = $_SESSION['address'] ?? '';
$phone_number = $_SESSION['phone_number'] ?? '';

$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? 'info';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>KCEAP - Upload Documents</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="../img/logo.png" type="image/png">
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
.navbar { background: #0d6efd !important; }
.upload-card {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.95);
    border-radius: 1.2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 2rem;
    width: 95%;
    max-width: 900px;
    margin-bottom: 2rem;
}
.section-title { font-weight:600; font-size:1.25rem; color:#0d6efd; margin:1rem 0; }
.btn-primary { background-color:#0d6efd; border:none; }
.btn-primary:hover { background-color:#0b5ed7; }
.btn-secondary { background-color:#6c757d; border:none; }
.btn-secondary:hover { background-color:#5c636a; }
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
<div class="upload-card">
  <h3 class="text-center mb-4 fw-semibold">📄 Upload Documents</h3>
  <?php if ($message): ?>
    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  <?php

// Check applicant status
$status = '';
$submitDisabled = false;
// Check if applicant exists in college_account (avoid undefined $exists)
$exists = false;
if (!empty($applicant_id)) {
  $stmt = $conn->prepare("SELECT id FROM college_account WHERE applicant_id = ? LIMIT 1");
  $stmt->bind_param("i", $applicant_id);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) $exists = true;
  $stmt->close();
}
if (!empty($applicant_id)) {
  $stmt = $conn->prepare("SELECT status FROM college_account WHERE applicant_id = ? LIMIT 1");
  $stmt->bind_param("i", $applicant_id);
  $stmt->execute();
  $stmt->bind_result($status);
  if ($stmt->fetch()) {
    $status_l = strtolower(trim($status));
    // disable submit when status is 'pending', 'active', or 'incomplete'
    $submitDisabled = in_array($status_l, ['pending', 'active', 'waiting for results']);
  }
  $stmt->close();
}
?>
<form action="upload_process.php" method="post" enctype="multipart/form-data" class="row g-3">
  <div class="col-12">
    <label class="form-label fw-semibold">Select Requirement to Upload</label>
    <input type="file" name="documents[]" class="form-control" multiple required <?= !$exists ? 'disabled' : '' ?>>
    <small class="text-muted">Accepted formats: PDF, JPG, PNG, DOCX. Max size: 5MB each.</small>
  </div>
  <div class="col-12 mt-3">
    <!-- Hidden fields for applicant information -->
    <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($applicant_id) ?>">
    <input type="hidden" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
    <input type="hidden" name="middle_name" value="<?= htmlspecialchars($middle_name) ?>">
    <input type="hidden" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <input type="hidden" name="school" value="<?= htmlspecialchars($school) ?>">
    <input type="hidden" name="course" value="<?= htmlspecialchars($course) ?>">
    <input type="hidden" name="year_level" value="<?= htmlspecialchars($year_level) ?>">
    <input type="hidden" name="address" value="<?= htmlspecialchars($address) ?>">
    <input type="hidden" name="phone_number" value="<?= htmlspecialchars($phone_number) ?>">
    <button type="submit" class="btn btn-primary px-4 mb-2 mb-md-0" <?= (!$exists || $submitDisabled) ? 'disabled' : '' ?>>Upload</button>
    <a href="mainpage.php" class="btn btn-secondary px-4 ms-md-2">Back</a>
  </div>
  <?php if (!$exists || $submitDisabled): ?>
    <div class="col-12 mt-2">
      <?php if (!$exists): ?>
        <div class="alert alert-warning">Wait for further notice.</div>
      <?php endif; ?>
      <?php if ($submitDisabled): ?>
        <div class="alert alert-info mt-2">
          <strong>Note:</strong> Document uploads are currently disabled.
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</form>
  <div class="text-center mt-4">
    <small class="text-muted">Please upload all required documents. If you have issues, contact the administrator.</small>
  </div>
</div>
<div class="footer-nav">
  <a href="profile.php"><span class="material-symbols-outlined">account_circle</span>Profile</a>
  <a href="schedule.php"><span class="material-symbols-outlined">calendar_month</span>Schedule</a>
  <a href="apply.php"><span class="material-symbols-outlined">upload</span>Upload</a>
  <a href="renew.php"><span class="material-symbols-outlined">refresh</span>Renew</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>