<?php
session_start();
require_once '../config/config.php';

$applicant_id = $_SESSION['user_id'] ?? $_SESSION['highschool_user']['account_id'] ?? $_SESSION['account_id'] ?? $_SESSION['id'] ?? null;
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
$course = $_SESSION['strand'] ?? '';
$year_level = $_SESSION['year_level'] ?? '';
$address = $_SESSION['address'] ?? '';
$phone_number = $_SESSION['phone_number'] ?? '';

$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? 'info';
unset($_SESSION['message'], $_SESSION['message_type']);

// Check if applicant exists in college_record
$exists = false;
if (!empty($applicant_id)) {
  $stmt = $conn->prepare("SELECT id FROM highschool_account WHERE applicant_id = ? LIMIT 1");
  $stmt->bind_param("i", $applicant_id);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) $exists = true;
  $stmt->close();
}

// Check applicant status (from college_account)
$status = '';
$submitDisabled = false;
// helper: return true when status is pending
function isPending($s) {
  return strtolower(trim((string)$s)) === 'pending';
}
if (!empty($applicant_id)) {
  $stmt = $conn->prepare("SELECT status FROM highschool_account WHERE applicant_id = ? LIMIT 1");
  $stmt->bind_param("i", $applicant_id);
  $stmt->execute();
  $stmt->bind_result($status);
  if ($stmt->fetch()) {
    // enable submit only when status is 'pending'
    $submitDisabled = !isPending($status);
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>KCEAP - Renew Application</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Material+Symbols+Outlined" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0d6efd, #0dcaf0); min-height:100vh; padding-top:5rem; }
.navbar { background: #0d6efd !important; }
.card-style { backdrop-filter: blur(12px); background: rgba(255,255,255,0.95); border-radius: 1rem; padding:2rem; max-width:900px; margin:0 auto 2rem; }
.section-title { font-weight:600; font-size:1.1rem; color:#0d6efd; margin-bottom:0.75rem; }
.btn-primary { background-color:#0d6efd; border:none; }
.footer-nav { position: fixed; bottom:0; left:0; width:100%; background:#fff; border-top:1px solid #ddd; display:flex; justify-content:space-around; padding:0.5rem 0; z-index:100; }
.footer-nav a { color:#0d6efd; text-decoration:none; font-size:0.75rem; display:flex; flex-direction:column; align-items:center; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="mainpage.php"><img src="../img/logo.png" width="40" height="40" class="me-2 rounded-circle"> KCEAP</a>
  </div>
</nav>

<div class="card-style">
  <h3 class="text-center mb-4">📄 Renew Application</h3>

  <?php if ($message): ?>
    <div class="alert alert-<?= htmlspecialchars($message_type) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>


  <form action="renew_process.php" method="post" enctype="multipart/form-data" class="row g-3">
    <div class="col-12">
      <label class="form-label fw-semibold">Select Renewal Documents</label>
      <input type="file" name="renewal_documents[]" class="form-control" multiple <?= !$exists ? 'disabled' : '' ?> accept="image/*,application/pdf">
      <small class="text-muted">Accepted formats: JPG, PNG, PDF. Max size: 5MB each.</small>
    </div>

    <div class="col-12">
      <!-- Visible dropdowns for Year Level and Semester; keep other applicant info as hidden fields -->
      <div class="row g-3 mb-2">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Year Level</label>
          <?php
            $year_opts = ['Grade 11','Grade 12'];
            $cur_year = $year_level;
          ?>
          <select name="year_level" class="form-select" <?= (!$exists || $submitDisabled) ? 'disabled' : '' ?>>
            <?php if ($cur_year && !in_array($cur_year, $year_opts)): ?>
              <option selected value="<?= htmlspecialchars($cur_year) ?>"><?= htmlspecialchars($cur_year) ?></option>
            <?php endif; ?>
            <?php foreach ($year_opts as $opt): ?>
              <option value="<?= htmlspecialchars($opt) ?>" <?= ($opt === $cur_year) ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Semester</label>
          <?php
            $sem_opts = ['1st semester','2nd semester'];
            $cur_sem = $_SESSION['semester'] ?? '';
          ?>
          <select name="semester" class="form-select" <?= (!$exists || $submitDisabled) ? 'disabled' : '' ?>>
            <?php if ($cur_sem && !in_array($cur_sem, $sem_opts)): ?>
              <option selected value="<?= htmlspecialchars($cur_sem) ?>"><?= htmlspecialchars($cur_sem) ?></option>
            <?php endif; ?>
            <?php foreach ($sem_opts as $s): ?>
              <option value="<?= htmlspecialchars($s) ?>" <?= ($s === $cur_sem) ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Hidden fields for applicant information -->
      <input type="hidden" name="applicant_id" value="<?= htmlspecialchars($applicant_id) ?>">
      <input type="hidden" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
      <input type="hidden" name="middle_name" value="<?= htmlspecialchars($middle_name) ?>">
      <input type="hidden" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <input type="hidden" name="school" value="<?= htmlspecialchars($school) ?>">
      <input type="hidden" name="course" value="<?= htmlspecialchars($strand) ?>">
      <input type="hidden" name="address" value="<?= htmlspecialchars($address) ?>">
      <input type="hidden" name="phone_number" value="<?= htmlspecialchars($phone_number) ?>">
      <button type="submit" class="btn btn-primary px-4 mb-2 mb-md-0" <?= (!$exists || $submitDisabled) ? 'disabled' : '' ?>>Submit Renewal</button>
      <a href="mainpage.php" class="btn btn-secondary px-4 ms-md-2">Back</a>
    </div>

    <?php if (!$exists || $submitDisabled): ?>
      <div class="col-12 mt-2">
        <?php if (!$exists): ?>
          <div class="alert alert-warning">Wait for further notice.</div>
        <?php endif; ?>
        <?php if ($submitDisabled): ?>
          <div class="alert alert-info mt-2">
            <strong>Note:</strong> Renewals are currently disabled.
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </form>
</div>

<div class="footer-nav">
  <a href="profile.php"><span class="material-symbols-outlined">account_circle</span>Profile</a>
  <a href="schedule.php"><span class="material-symbols-outlined">calendar_month</span>Schedule</a>
  <a href="upload_docs.php"><span class="material-symbols-outlined">upload</span>Upload</a>
  <a href="renew.php"><span class="material-symbols-outlined">refresh</span>Renew</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
