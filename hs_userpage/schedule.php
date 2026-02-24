<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'] ?? null;

// Handle AJAX request for real-time check
if (isset($_GET['action']) && $_GET['action'] === 'check_deadline' && $email) {
    date_default_timezone_set('Asia/Manila');
    $now = new DateTimeImmutable('now');

    $stmt = $conn->prepare("SELECT upload_deadline, status, first_name, middle_name, last_name FROM highschool_account WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($upload_deadline, $status, $first_name, $middle_name, $last_name);
    $stmt->fetch();
    $stmt->close();

    $expired = false;
    $fullName = trim("$first_name $middle_name $last_name");

    if (!empty($upload_deadline)) {
        // If stored as date-only (YYYY-MM-DD), treat as end of day
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($upload_deadline))) {
            $upload_deadline = trim($upload_deadline) . ' 23:59:00';
        }

        try {
            $deadline = new DateTimeImmutable($upload_deadline);
        } catch (Exception $e) {
            // fallback: try parsing date-only
            try {
                $deadline = new DateTimeImmutable(trim($upload_deadline) . ' 23:59:00');
            } catch (Exception $ex) {
                $deadline = null;
            }
        }

        if ($deadline instanceof DateTimeImmutable) {
            if ($now > $deadline && strtolower(trim($status)) !== 'expired') {
                // Update status in DB
                $upd = $conn->prepare("UPDATE highschool_account SET status='expired' WHERE email=?");
                if ($upd) {
                    $upd->bind_param("s", $email);
                    $upd->execute();
                    $upd->close();
                }

                $status = 'expired';
                $expired = true;

                // Send email notification
                try {
                    $mail = getMailer();
                    $mail->addAddress($email, $fullName);
                    $mail->Subject = 'KCEAP Upload Deadline Expired';
                    $deadline_text = $deadline->format('F j, Y \a\t g:i A');
                    $mail->Body = "<p>Dear <strong>{$fullName}</strong>,</p>"
                        . "<p>Your upload deadline has passed. Your status is now <b>expired</b> in our records.</p>"
                        . "<p>Deadline was: {$deadline_text}</p>"
                        . "<p>If you need assistance, contact the administrator.</p>"
                        . "<p>Sincerely,<br>KCEAP Team</p>";
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Email not sent to {$email}: " . ($mail->ErrorInfo ?? $e->getMessage()));
                }
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'expired' => $expired,
        'current_status' => $status,
        'deadline' => $upload_deadline ?? null
    ]);
    exit();
}

// --- Normal page load ---
$schedule_date = '';
$schedule_time = '';
$upload_deadline = '';
$status = '';
$fullName = '';

if ($email) {
    $stmt = $conn->prepare("
        SELECT schedule_date, schedule_time, upload_deadline, status, first_name, middle_name, last_name
        FROM highschool_account
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($sd, $st, $ud, $stt, $first, $middle, $last);
    if ($stmt->fetch()) {
        $schedule_date = $sd;
        $schedule_time = $st;
        $upload_deadline = $ud;
        $status = $stt;
        $fullName = trim("$first $middle $last");
    }
    $stmt->close();
}

$schedule_date_formatted = $schedule_date ? date('F j, Y', strtotime($schedule_date)) : 'Not scheduled yet';
$schedule_time_formatted = $schedule_time ? date('g:i A', strtotime($schedule_time)) : 'Not scheduled yet';
$upload_deadline_formatted = $upload_deadline ? date('F j, Y \a\t g:i A', strtotime($upload_deadline)) : 'No deadline set';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>KCEAP - Schedule</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="../img/logo.png" type="image/png">
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

.status-expired { color: red; font-weight: 600; }
.status-active { color: green; font-weight: 600; }
/* Force upload deadline and status text to black for readability */
#upload_deadline, #status { color: #000 !important; }
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
            <p class="field-value" id="schedule_date"><?= htmlspecialchars($schedule_date_formatted) ?></p>
        </div>

        <div class="col-md-6">
            <p class="field-label"><span class="material-symbols-outlined field-icon">schedule</span>Time</p>
            <p class="field-value" id="schedule_time"><?= htmlspecialchars($schedule_time_formatted) ?></p>
        </div>

        <div class="col-md-12">
            <p class="field-label"><span class="material-symbols-outlined field-icon">timer</span>Upload Deadline</p>
            <p class="field-value status-active" id="upload_deadline"><?= htmlspecialchars($upload_deadline_formatted) ?></p>
        </div>

        <div class="col-md-12">
            <p class="field-label"><span class="material-symbols-outlined field-icon">info</span>Status</p>
            <p class="field-value <?= $status === 'expired' ? 'status-expired' : 'status-active' ?>" id="status"><?= htmlspecialchars($status) ?></p>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Real-time deadline check every 30 seconds
function checkDeadline() {
    $.getJSON('schedule.php?action=check_deadline', function(data) {
        if(data.status === 'success') {
            const statusElem = $('#status');
            const deadlineElem = $('#upload_deadline');

            if(data.expired) {
                statusElem.text(data.current_status).removeClass('status-active').addClass('status-expired');
                deadlineElem.addClass('status-expired');
            } else {
                statusElem.text(data.current_status).removeClass('status-expired').addClass('status-active');
                deadlineElem.removeClass('status-expired').addClass('status-active');
            }
        }
    });
}

// Check immediately and every 30 seconds
checkDeadline();
setInterval(checkDeadline, 30000);
</script>

</body>
</html>
