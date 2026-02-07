<?php
session_start();
include 'header.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Read current deadline settings
$deadlineFile = __DIR__ . '/deadline.json';
$currentSettings = [
    'college' => [
        'deadline' => '',
        'limit' => 0,
        'disabled' => false
    ],
    'highschool' => [
        'deadline' => '',
        'limit' => 0,
        'disabled' => false
    ]
];

if (file_exists($deadlineFile)) {
    $currentSettings = json_decode(file_get_contents($deadlineFile), true);
}

// Ensure the 'limit' key exists with a default value of 0
$currentSettings['college']['limit'] = $currentSettings['college']['limit'] ?? 0;
$currentSettings['highschool']['limit'] = $currentSettings['highschool']['limit'] ?? 0;

// Check if deadlines have passed
$currentDateTime = date('Y-m-d\TH:i');
$collegeDeadlinePassed = !empty($currentSettings['college']['deadline']) && $currentDateTime > $currentSettings['college']['deadline'];
$hsDeadlinePassed = !empty($currentSettings['highschool']['deadline']) && $currentDateTime > $currentSettings['highschool']['deadline'];

// Update settings to disable applications if deadlines are passed
if ($collegeDeadlinePassed) {
    $currentSettings['college']['disabled'] = true;
} else {
    $currentSettings['college']['disabled'] = false;
}

if ($hsDeadlinePassed) {
    $currentSettings['highschool']['disabled'] = true;
} else {
    $currentSettings['highschool']['disabled'] = false;
}

// Initialize college and high school applicant counts
$collegeCount = $conn->query("SELECT COUNT(*) AS total FROM college_account")->fetch_assoc()['total'] ?? 0;
$hsCount = $conn->query("SELECT COUNT(*) AS total FROM highschool_account")->fetch_assoc()['total'] ?? 0;

// Ensure the radio button is checked if the limit is reached
if ($currentSettings['college']['limit'] > 0 && $collegeCount >= $currentSettings['college']['limit']) {
    $currentSettings['college']['disabled'] = true;
}

if ($currentSettings['highschool']['limit'] > 0 && $hsCount >= $currentSettings['highschool']['limit']) {
    $currentSettings['highschool']['disabled'] = true;
}

// Save updated settings back to the file
file_put_contents($deadlineFile, json_encode($currentSettings, JSON_PRETTY_PRINT));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collegeDeadline = trim($_POST['college_deadline'] ?? '');
    $collegeLimit = (int)($_POST['college_limit'] ?? 0);
    $collegeDisabled = isset($_POST['college_disabled']) ? true : false;

    $hsDeadline = trim($_POST['hs_deadline'] ?? '');
    $hsLimit = (int)($_POST['hs_limit'] ?? 0);
    $hsDisabled = isset($_POST['hs_disabled']) ? true : false;

    // Validate deadline formats (YYYY-MM-DDTHH:MM)
    $validCollegeDeadline = empty($collegeDeadline) || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $collegeDeadline);
    $validHsDeadline = empty($hsDeadline) || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $hsDeadline);

    if (!$validCollegeDeadline || !$validHsDeadline) {
        $_SESSION['message'] = 'Invalid deadline format. Use YYYY-MM-DDTHH:MM';
        $_SESSION['message_type'] = 'danger';
    } else {
        $newSettings = [
            'college' => [
                'deadline' => $collegeDeadline,
                'limit' => $collegeLimit,
                'disabled' => $collegeDisabled
            ],
            'highschool' => [
                'deadline' => $hsDeadline,
                'limit' => $hsLimit,
                'disabled' => $hsDisabled
            ]
        ];

        if (file_put_contents($deadlineFile, json_encode($newSettings, JSON_PRETTY_PRINT))) {
            $_SESSION['message'] = 'Deadline settings updated successfully!';
            $_SESSION['message_type'] = 'success';
            $currentSettings = $newSettings;
        } else {
            $_SESSION['message'] = 'Failed to save settings.';
            $_SESSION['message_type'] = 'danger';
        }
    }

    header('Location: deadline.php');
    exit;
}
?>

<body>
    <div class="d-flex">
        <?php include 'aside.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand navbar-light mb-4">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Deadline & Limit Control</a>
                </div>
            </nav>

            <!-- Page Content -->
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
                        <form method="POST" action="">
                        <div class="row">
                            <!-- College Settings -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <span class="material-symbols-outlined me-2">school</span>
                                            College Applications
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="college_deadline" class="form-label">
                                                <strong>Application Deadline</strong>
                                            </label>
                                            <input type="datetime-local"
                                                   class="form-control"
                                                   id="college_deadline"
                                                   name="college_deadline"
                                                   value="<?= htmlspecialchars($currentSettings['college']['deadline']) ?>">
                                            <div class="form-text">
                                                Set the deadline for college applications.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="college_limit" class="form-label">
                                                <strong>Application Limit</strong>
                                            </label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="college_limit"
                                                   name="college_limit"
                                                   min="0"
                                                   value="<?= htmlspecialchars($currentSettings['college']['limit']) ?>">
                                            <div class="form-text">
                                                Maximum college applicants. Set to 0 for unlimited.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="college_disabled"
                                                       name="college_disabled"
                                                       <?= $currentSettings['college']['disabled'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="college_disabled">
                                                    <strong>Disable College Applications</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- High School Settings -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <span class="material-symbols-outlined me-2">school</span>
                                            High School Applications
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="hs_deadline" class="form-label">
                                                <strong>Application Deadline</strong>
                                            </label>
                                            <input type="datetime-local"
                                                   class="form-control"
                                                   id="hs_deadline"
                                                   name="hs_deadline"
                                                   value="<?= htmlspecialchars($currentSettings['highschool']['deadline']) ?>">
                                            <div class="form-text">
                                                Set the deadline for high school applications.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="hs_limit" class="form-label">
                                                <strong>Application Limit</strong>
                                            </label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="hs_limit"
                                                   name="hs_limit"
                                                   min="0"
                                                   value="<?= htmlspecialchars($currentSettings['highschool']['limit']) ?>">
                                            <div class="form-text">
                                                Maximum high school applicants. Set to 0 for unlimited.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="hs_disabled"
                                                       name="hs_disabled"
                                                       <?= $currentSettings['highschool']['disabled'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="hs_disabled">
                                                    <strong>Disable High School Applications</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span class="material-symbols-outlined me-2">save</span>
                                Save All Settings
                            </button>
                        </div>
                        </form>

                        <!-- Current Status -->
                        <div class="card shadow mt-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <span class="material-symbols-outlined me-2">info</span>
                                    Current Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php
                                // College status
                                $collegeActive = true;
                                $collegeReasons = [];

                                if ($currentSettings['college']['disabled']) {
                                    $collegeActive = false;
                                    $collegeReasons[] = 'Manually disabled';
                                }

                                $today = date('Y-m-d');
                                if (!empty($currentSettings['college']['deadline'])) {
                                    $deadlineDate = date('Y-m-d', strtotime($currentSettings['college']['deadline']));
                                    if ($today > $deadlineDate) {
                                        $collegeActive = false;
                                        $collegeReasons[] = 'Deadline passed (' . date('M j, Y', strtotime($currentSettings['college']['deadline'])) . ')';
                                    }
                                }

                                if ($currentSettings['college']['limit'] > 0) {
                                    $collegeCount = $conn->query("SELECT COUNT(*) AS total FROM college_account")->fetch_assoc()['total'];
                                    if ($collegeCount >= $currentSettings['college']['limit']) {
                                        $collegeActive = false;
                                        $collegeReasons[] = 'Limit reached (' . $collegeCount . '/' . $currentSettings['college']['limit'] . ')';
                                    }
                                }

                                // High School status
                                $hsActive = true;
                                $hsReasons = [];

                                if ($currentSettings['highschool']['disabled']) {
                                    $hsActive = false;
                                    $hsReasons[] = 'Manually disabled';
                                }

                                if (!empty($currentSettings['highschool']['deadline'])) {
                                    $deadlineDate = date('Y-m-d', strtotime($currentSettings['highschool']['deadline']));
                                    if ($today > $deadlineDate) {
                                        $hsActive = false;
                                        $hsReasons[] = 'Deadline passed (' . date('M j, Y', strtotime($currentSettings['highschool']['deadline'])) . ')';
                                    }
                                }

                                if ($currentSettings['highschool']['limit'] > 0) {
                                    $hsCount = $conn->query("SELECT COUNT(*) AS total FROM highschool_account")->fetch_assoc()['total'];
                                    if ($hsCount >= $currentSettings['highschool']['limit']) {
                                        $hsActive = false;
                                        $hsReasons[] = 'Limit reached (' . $hsCount . '/' . $currentSettings['highschool']['limit'] . ')';
                                    }
                                }
                                ?>

                                <div class="row text-center mb-3">
                                    <!-- College Status -->
                                    <div class="col-md-6">
                                        <div class="card border-primary mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">College Applications</h6>
                                                <div class="mb-2 display-6">
                                                    <span class="material-symbols-outlined <?= $collegeActive ? 'text-success' : 'text-danger' ?>">
                                                        <?= $collegeActive ? 'check_circle' : 'cancel' ?>
                                                    </span>
                                                </div>
                                                <strong class="<?= $collegeActive ? 'text-success' : 'text-danger' ?>">
                                                    <?= $collegeActive ? 'Active' : 'Inactive' ?>
                                                </strong>
                                                <?php if (!$collegeActive && !empty($collegeReasons)): ?>
                                                    <div class="mt-2 small text-muted">
                                                        <strong>Reasons:</strong>
                                                        <ul class="mb-0">
                                                            <?php foreach ($collegeReasons as $reason): ?>
                                                                <li><?= $reason ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- High School Status -->
                                    <div class="col-md-6">
                                        <div class="card border-success mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title text-success">High School Applications</h6>
                                                <div class="mb-2 display-6">
                                                    <span class="material-symbols-outlined <?= $hsActive ? 'text-success' : 'text-danger' ?>">
                                                        <?= $hsActive ? 'check_circle' : 'cancel' ?>
                                                    </span>
                                                </div>
                                                <strong class="<?= $hsActive ? 'text-success' : 'text-danger' ?>">
                                                    <?= $hsActive ? 'Active' : 'Inactive' ?>
                                                </strong>
                                                <?php if (!$hsActive && !empty($hsReasons)): ?>
                                                    <div class="mt-2 small text-muted">
                                                        <strong>Reasons:</strong>
                                                        <ul class="mb-0">
                                                            <?php foreach ($hsReasons as $reason): ?>
                                                                <li><?= $reason ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                          
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
