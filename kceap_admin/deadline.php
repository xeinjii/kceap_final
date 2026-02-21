<?php
session_start();
include 'header.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// File path
$deadlineFile = __DIR__ . '/deadline.json';

// Default settings
$currentSettings = [
    'college' => ['deadline'=>'','limit'=>0,'disabled'=>false],
    'highschool' => ['deadline'=>'','limit'=>0,'disabled'=>false]
];

// Load existing JSON
if (file_exists($deadlineFile)) {
    $currentSettings = json_decode(file_get_contents($deadlineFile), true);
}

// Ensure defaults
$currentSettings['college']['limit'] = $currentSettings['college']['limit'] ?? 0;
$currentSettings['college']['disabled'] = $currentSettings['college']['disabled'] ?? false;
$currentSettings['highschool']['limit'] = $currentSettings['highschool']['limit'] ?? 0;
$currentSettings['highschool']['disabled'] = $currentSettings['highschool']['disabled'] ?? false;

// Current datetime
$currentDateTime = new DateTime();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // College settings
    if (isset($_POST['save_college'])) {
        $collegeDeadline = trim($_POST['college_deadline'] ?? '');
        $collegeLimit = (int)($_POST['college_limit'] ?? 0);
        $collegeDisabled = isset($_POST['college_disabled']) ? true : false;

        $validCollegeDeadline = empty($collegeDeadline) || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $collegeDeadline);

        if (!$validCollegeDeadline) {
            $_SESSION['message'] = 'Invalid college deadline format. Use YYYY-MM-DDTHH:MM';
            $_SESSION['message_type'] = 'danger';
        } else {
            $currentSettings['college'] = [
                'deadline' => $collegeDeadline,
                'limit' => $collegeLimit,
                'disabled' => $collegeDisabled
            ];

            if (file_put_contents($deadlineFile, json_encode($currentSettings, JSON_PRETTY_PRINT))) {
                $_SESSION['message'] = 'College deadline settings updated successfully!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Failed to save college settings.';
                $_SESSION['message_type'] = 'danger';
            }
        }

        header('Location: deadline.php');
        exit;
    }

    // High School settings
    if (isset($_POST['save_highschool'])) {
        $hsDeadline = trim($_POST['hs_deadline'] ?? '');
        $hsLimit = (int)($_POST['hs_limit'] ?? 0);
        $hsDisabled = isset($_POST['hs_disabled']) ? true : false;

        $validHsDeadline = empty($hsDeadline) || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $hsDeadline);

        if (!$validHsDeadline) {
            $_SESSION['message'] = 'Invalid high school deadline format. Use YYYY-MM-DDTHH:MM';
            $_SESSION['message_type'] = 'danger';
        } else {
            $currentSettings['highschool'] = [
                'deadline' => $hsDeadline,
                'limit' => $hsLimit,
                'disabled' => $hsDisabled
            ];

            if (file_put_contents($deadlineFile, json_encode($currentSettings, JSON_PRETTY_PRINT))) {
                $_SESSION['message'] = 'High school deadline settings updated successfully!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Failed to save high school settings.';
                $_SESSION['message_type'] = 'danger';
            }
        }

        header('Location: deadline.php');
        exit;
    }
}

// --- AUTOMATIC DEADLINE AND LIMIT CHECKS ---
$updateJson = false;

// College
$collegeCount = $conn->query("SELECT COUNT(*) AS total FROM college_schedule")->fetch_assoc()['total'] ?? 0;
if (!empty($currentSettings['college']['deadline'])) {
    $collegeDeadline = new DateTime($currentSettings['college']['deadline']);
    if ($currentDateTime >= $collegeDeadline && !$currentSettings['college']['disabled']) {
        $currentSettings['college']['disabled'] = true;
        $updateJson = true;
    }
}
if ($currentSettings['college']['limit'] > 0 && $collegeCount >= $currentSettings['college']['limit'] && !$currentSettings['college']['disabled']) {
    $currentSettings['college']['disabled'] = true;
    $updateJson = true;
}

// High School
$hsCount = $conn->query("SELECT COUNT(*) AS total FROM highschool_schedule")->fetch_assoc()['total'] ?? 0;
if (!empty($currentSettings['highschool']['deadline'])) {
    $hsDeadline = new DateTime($currentSettings['highschool']['deadline']);
    if ($currentDateTime >= $hsDeadline && !$currentSettings['highschool']['disabled']) {
        $currentSettings['highschool']['disabled'] = true;
        $updateJson = true;
    }
}
if ($currentSettings['highschool']['limit'] > 0 && $hsCount >= $currentSettings['highschool']['limit'] && !$currentSettings['highschool']['disabled']) {
    $currentSettings['highschool']['disabled'] = true;
    $updateJson = true;
}

// Save if changed
if ($updateJson) {
    file_put_contents($deadlineFile, json_encode($currentSettings, JSON_PRETTY_PRINT));
}
?>

<body>
<div class="d-flex">
    <?php include 'aside.php'; ?>

    <div class="main-content flex-grow-1">
        <nav class="navbar navbar-expand navbar-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Deadline & Limit Control</a>
            </div>
        </nav>

        <div class="container py-3">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row">
                        <!-- College -->
                        <div class="col-md-6 mb-4">
                            <form method="POST" action="">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">College Applications</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Deadline</strong></label>
                                            <input type="datetime-local" class="form-control" name="college_deadline"
                                                   value="<?= htmlspecialchars($currentSettings['college']['deadline']) ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Limit</strong></label>
                                            <input type="number" class="form-control" name="college_limit" min="0"
                                                   value="<?= htmlspecialchars($currentSettings['college']['limit']) ?>">
                                            <small class="text-muted">Current applications: <?= $collegeCount ?></small>
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input college-checkbox" name="college_disabled"
                                                <?= $currentSettings['college']['disabled'] ? 'checked' : '' ?>
                                                data-type="college">
                                            <label class="form-check-label"><strong>Disable College Applications</strong></label>
                                        </div>

                                        <div id="college-alert"></div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <button type="submit" name="save_college" class="btn btn-primary w-100">Save College Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- High School -->
                        <div class="col-md-6 mb-4">
                            <form method="POST" action="">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">High School Applications</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Deadline</strong></label>
                                            <input type="datetime-local" class="form-control" name="hs_deadline"
                                                   value="<?= htmlspecialchars($currentSettings['highschool']['deadline']) ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><strong>Limit</strong></label>
                                            <input type="number" class="form-control" name="hs_limit" min="0"
                                                   value="<?= htmlspecialchars($currentSettings['highschool']['limit']) ?>">
                                            <small class="text-muted">Current applications: <?= $hsCount ?></small>
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input hs-checkbox" name="hs_disabled"
                                                <?= $currentSettings['highschool']['disabled'] ? 'checked' : '' ?>
                                                data-type="highschool">
                                            <label class="form-check-label"><strong>Disable High School Applications</strong></label>
                                        </div>

                                        <div id="hs-alert"></div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <button type="submit" name="save_highschool" class="btn btn-success w-100">Save High School Settings</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Auto-disable via AJAX
function autoDisable(type) {
    fetch('auto_disable.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'type=' + encodeURIComponent(type)
    });
}

function checkApplications() {
    const now = new Date();

    // College
    const collegeCheckbox = document.querySelector('.college-checkbox');
    if (collegeCheckbox) {
        const deadline = new Date("<?= $currentSettings['college']['deadline'] ?>");
        const limit = <?= $currentSettings['college']['limit'] ?>;
        const count = <?= $collegeCount ?>;
        if ((deadline && now >= deadline) || (limit > 0 && count >= limit)) {
            if (!collegeCheckbox.checked) {
                collegeCheckbox.checked = true;
                autoDisable('college');
            }
            collegeCheckbox.disabled = true;
            document.getElementById('college-alert').innerHTML =
                `<div class="alert alert-warning mt-2 mb-0 py-2">
                    <small>⚠️ Deadline passed or limit reached - applications disabled</small>
                </div>`;
        }
    }

    // High School
    const hsCheckbox = document.querySelector('.hs-checkbox');
    if (hsCheckbox) {
        const deadline = new Date("<?= $currentSettings['highschool']['deadline'] ?>");
        const limit = <?= $currentSettings['highschool']['limit'] ?>;
        const count = <?= $hsCount ?>;
        if ((deadline && now >= deadline) || (limit > 0 && count >= limit)) {
            if (!hsCheckbox.checked) {
                hsCheckbox.checked = true;
                autoDisable('highschool');
            }
            hsCheckbox.disabled = true;
            document.getElementById('hs-alert').innerHTML =
                `<div class="alert alert-warning mt-2 mb-0 py-2">
                    <small>⚠️ Deadline passed or limit reached - applications disabled</small>
                </div>`;
        }
    }
}

// Run every 10 seconds
setInterval(checkApplications, 10000);
checkApplications();
</script>
</body>
</html>