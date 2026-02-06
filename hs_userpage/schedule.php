<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$schedule_date = '';
$schedule_time = '';
$schedule_remarks = '';

$email = $_SESSION['email'] ?? null;
if ($email) {
    $stmt = $conn->prepare("SELECT schedule_date, schedule_time FROM highschool_account WHERE email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($sd, $st);
        if ($stmt->fetch()) {
            $schedule_date = $sd;
            $schedule_time = $st;
        }
        $stmt->close();
    }
}

$schedule_date_formatted = $schedule_date ? date('F j, Y', strtotime($schedule_date)) : 'Not scheduled yet';
$schedule_time_formatted = $schedule_time ? date('g:i A', strtotime($schedule_time)) : 'Not scheduled yet';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>KCEAP - Schedule</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Material+Symbols+Outlined" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
    min-height: 100vh;
    padding-top: 80px;
    padding-bottom: 100px;
}

.navbar {
     background: #0d6efd !important;
}

.schedule-card {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.95);
    border-radius: 1.2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 2rem;
    width: 95%;
    max-width: 900px;
    margin: auto;
}

.field-icon { font-size:20px; margin-right:8px; color:#0d6efd; }
.field-label { font-weight:600; color:#222; }
.field-value {
    color:#444;
    border-bottom:1px solid #ddd;
    padding:0.3rem 0;
    margin-bottom:1rem;
    word-wrap: break-word;
}

.footer-nav {
    position: fixed;
    bottom:0; left:0;
    width:100%;
    background:#fff;
    border-top:1px solid #ddd;
    display:flex;
    justify-content:space-around;
    padding:0.5rem 0;
}

.footer-nav a {
    color:#0d6efd;
    text-decoration:none;
    display:flex;
    flex-direction:column;
    align-items:center;
    font-size:.75rem;
}

.footer-nav a .material-symbols-outlined {
    font-size:1.5rem;
}

</style>
</head>

<body>

<nav class="navbar navbar-dark navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="mainpage.php">
            <img src="../img/logo.png" width="40" height="40" class="me-2 rounded-circle"> KCEAP
        </a>
    </div>
</nav>

<div class="schedule-card">
    <h3 class="text-center mb-4 fw-semibold">📅 Your Schedule</h3>

    <div class="row g-3">
        <div class="col-md-6">
            <p class="field-label"><span class="material-symbols-outlined field-icon">event</span>Date</p>
            <p class="field-value"><?= htmlspecialchars($schedule_date_formatted) ?></p>
        </div>

        <div class="col-md-6">
            <p class="field-label"><span class="material-symbols-outlined field-icon">schedule</span>Time</p>
            <p class="field-value"><?= htmlspecialchars($schedule_time_formatted) ?></p>
        </div>

        <div class="col-12">
            <p class="field-label"><span class="material-symbols-outlined field-icon">location_on</span>Location</p>
            <p class="field-value">KCEAP Office</p>
        </div>

    </div>

    <hr class="my-4">
    <div class="text-center">
        <a href="mainpage.php" class="btn btn-secondary px-4">Back</a>
    </div>
</div>

<!-- MOBILE FOOTER NAV -->
<div class="footer-nav">
    <a href="profile.php"><span class="material-symbols-outlined">account_circle</span>Profile</a>
    <a href="schedule.php"><span class="material-symbols-outlined">calendar_month</span>Schedule</a>
    <a href="upload_docs.php"><span class="material-symbols-outlined">upload</span>Upload</a>
    <a href="renew.php"><span class="material-symbols-outlined">refresh</span>Renew</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
