<?php
session_start();
include 'header.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get unique school years from both tables
$years_sql = "
    SELECT DISTINCT school_year FROM hs_reports 
    UNION 
    SELECT DISTINCT school_year FROM college_reports 
    ORDER BY school_year DESC
";
$years_result = $conn->query($years_sql);
$school_years = $years_result ? $years_result->fetch_all(MYSQLI_ASSOC) : [];

// Get selected school year (default to most recent)
$selected_year = $_GET['year'] ?? ($school_years[0]['school_year'] ?? date('Y') . '-' . (date('Y') + 1));

// Get data based on selected year
$hs_sql = "
    SELECT applicant_id, first_name, middle_name, last_name, school, strand, year_level, semester, 
           address, phone_number, email, status, school_year, archived_at
    FROM hs_reports 
    WHERE school_year = ? 
    ORDER BY school ASC, first_name ASC
";

$college_sql = "
    SELECT applicant_id, first_name, middle_name, last_name, school, course, year_level, semester, 
           address, phone_number, email, status, school_year, created_at
    FROM college_reports 
    WHERE school_year = ? 
    ORDER BY school ASC, first_name ASC
";

// Prepare and execute queries
$hs_stmt = $conn->prepare($hs_sql);
$hs_stmt->bind_param("s", $selected_year);
$hs_stmt->execute();
$hs_data = $hs_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$hs_stmt->close();

$college_stmt = $conn->prepare($college_sql);
$college_stmt->bind_param("s", $selected_year);
$college_stmt->execute();
$college_data = $college_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$college_stmt->close();

// Group highschool data by school
$hs_by_school = [];
foreach ($hs_data as $row) {
    $school = $row['school'] ?? 'Unspecified';
    if (!isset($hs_by_school[$school])) {
        $hs_by_school[$school] = [];
    }
    $hs_by_school[$school][] = $row;
}

// Group college data by school
$college_by_school = [];
foreach ($college_data as $row) {
    $school = $row['school'] ?? 'Unspecified';
    if (!isset($college_by_school[$school])) {
        $college_by_school[$school] = [];
    }
    $college_by_school[$school][] = $row;
}

// Get unique schools from both datasets
$all_schools = array_unique(array_merge(array_keys($hs_by_school), array_keys($college_by_school)));
sort($all_schools);

// Get selected school filter (default to 'all')
$selected_school = $_GET['school'] ?? 'all';

// Filter data by selected school
if ($selected_school !== 'all') {
    $hs_by_school = array_filter($hs_by_school, function($key) use ($selected_school) {
        return $key === $selected_school;
    }, ARRAY_FILTER_USE_KEY);
    
    $college_by_school = array_filter($college_by_school, function($key) use ($selected_school) {
        return $key === $selected_school;
    }, ARRAY_FILTER_USE_KEY);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Archive - KCEAP</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="../style/kceapadmin.css" rel="stylesheet">
    <style>
        .filter-section { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .nav-tabs { border-bottom: 2px solid #0d6efd; }
        .nav-tabs .nav-link { color: #666; font-weight: 500; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; }
        .nav-tabs .nav-link:hover { color: #0d6efd; }
        .nav-tabs .nav-link.active { color: white; background: #0d6efd; border-bottom-color: #0d6efd; }
        .school-section { margin-bottom: 1.5rem; }
        .school-header { background: #f8f9fa; padding: 1rem; border-radius: 8px; border-left: 4px solid #0d6efd; cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center; }
        .school-header:hover { background: #e9ecef; }
        .school-header h5 { margin: 0; font-weight: 600; }
        .school-data { margin-top: 0.5rem; }
        .table { font-size: 0.9rem; }
        .table thead { background: #f8f9fa; }
        .table th { font-weight: 600; color: #0d6efd; }
        .badge-count { background: #0d6efd; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-badge { font-size: 0.85rem; padding: 0.4rem 0.8rem; }
        .stats { display: flex; gap: 2rem; margin-top: 1rem; flex-wrap: wrap; }
        .stat-item { display: flex; flex-direction: column; }
        .stat-label { font-size: 0.85rem; color: #666; margin-bottom: 0.25rem; }
        .stat-value { font-size: 1.5rem; font-weight: 600; color: #0d6efd; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: #999; }
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
        .main-content .container-fluid { padding-left: 2rem; padding-right: 2rem; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include 'aside.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand navbar-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">KCEAP Archives</a>
            </div>
        </nav><br><br><br>
        <!-- Page Content -->
        <div class="container-fluid py-4">
    <!-- Filter Section -->
    <div class="filter-section">
        <h5 class="mb-3"><span class="material-symbols-outlined align-middle me-2">filter_list</span>Filter Archives</h5>
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="yearSelect" class="form-label fw-semibold">School Year</label>
                <select class="form-select" id="yearSelect" name="year" onchange="this.form.submit()">
                    <?php if (empty($school_years)): ?>
                        <option>No data available</option>
                    <?php else: ?>
                        <?php foreach ($school_years as $year): ?>
                            <option value="<?= htmlspecialchars($year['school_year']) ?>" <?= ($year['school_year'] === $selected_year) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year['school_year']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="schoolSelect" class="form-label fw-semibold">School</label>
                <select class="form-select" id="schoolSelect" name="school" onchange="this.form.submit()">
                    <option value="all" <?= ($selected_school === 'all') ? 'selected' : '' ?>>All Schools</option>
                    <?php if (!empty($all_schools)): ?>
                        <?php foreach ($all_schools as $school): ?>
                            <option value="<?= htmlspecialchars($school) ?>" <?= ($school === $selected_school) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($school) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($hs_data) || !empty($college_data)): ?>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hs-tab" data-bs-toggle="tab" data-bs-target="#hs-content" type="button" role="tab" aria-controls="hs-content" aria-selected="true">
                    <span class="material-symbols-outlined align-middle me-1">school</span>Highschool
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="college-tab" data-bs-toggle="tab" data-bs-target="#college-content" type="button" role="tab" aria-controls="college-content" aria-selected="false">
                   <span class="material-symbols-outlined align-middle me-1">school</span>College
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Highschool Tab -->
            <div class="tab-pane fade show active" id="hs-content" role="tabpanel" aria-labelledby="hs-tab">
                <?php if (!empty($hs_data)): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0"><span class="material-symbols-outlined align-middle me-2">school</span>Highschool Graduates - <?= htmlspecialchars($selected_year) ?></h5>
                        <span class="badge-count"><?= count($hs_data) ?> Total</span>
                    </div>

                    <div class="stats">
                        <div class="stat-item">
                            <span class="stat-label">Schools</span>
                            <span class="stat-value"><?= count($hs_by_school) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Graduates</span>
                            <span class="stat-value"><?= count($hs_data) ?></span>
                        </div>
                    </div>

                    <!-- Highschool Schools -->
                    <div class="mt-4">
                        <?php foreach ($hs_by_school as $school => $students): ?>
                            <div class="school-section">
                                <div class="school-header" data-bs-toggle="collapse" data-bs-target="#hs-<?= md5($school) ?>">
                                    <div>
                                        <h5 class="mb-0"><?= htmlspecialchars($school) ?></h5>
                                        <small class="text-muted"><?= count($students) ?> student(s)</small>
                                    </div>
                                    <span class="material-symbols-outlined">expand_more</span>
                                </div>
                                <div class="collapse show" id="hs-<?= md5($school) ?>">
                                    <div class="school-data mt-2">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                         <th>Adress</th>
                                                         <th>School</th>
                                                        <th>Strand</th>
                                                        <th>Year Level</th>
                                                        <th>Semester</th>
                                                        <th>Status</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $idx = 1; foreach ($students as $student): ?>
                                                        <tr>
                                                            <td><?= $idx++ ?></td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? substr($student['middle_name'], 0, 1) . '. ' : '') . $student['last_name']) ?></strong>
                                                            </td>
                                                            <td><?= htmlspecialchars($student['address'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['school'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['strand'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['year_level'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['semester'] ?? 'N/A') ?></td>
                                                            <td><span class="badge bg-success status-badge"><?= htmlspecialchars($student['status'] ?? 'graduated') ?></span></td>
                                                            <td><small><?= htmlspecialchars($student['email'] ?? 'N/A') ?></small></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <p>No highschool graduate records found for <?= htmlspecialchars($selected_year) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- College Tab -->
            <div class="tab-pane fade" id="college-content" role="tabpanel" aria-labelledby="college-tab">
                <?php if (!empty($college_data)): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0"><span class="material-symbols-outlined align-middle me-2">school</span>College Graduates - <?= htmlspecialchars($selected_year) ?></h5>
                        <span class="badge-count"><?= count($college_data) ?> Total</span>
                    </div>

                    <div class="stats">
                        <div class="stat-item">
                            <span class="stat-label">Schools</span>
                            <span class="stat-value"><?= count($college_by_school) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Graduates</span>
                            <span class="stat-value"><?= count($college_data) ?></span>
                        </div>
                    </div>

                    <!-- College Schools -->
                    <div class="mt-4">
                        <?php foreach ($college_by_school as $school => $students): ?>
                            <div class="school-section">
                                <div class="school-header" data-bs-toggle="collapse" data-bs-target="#college-<?= md5($school) ?>">
                                    <div>
                                        <h5 class="mb-0"><?= htmlspecialchars($school) ?></h5>
                                        <small class="text-muted"><?= count($students) ?> student(s)</small>
                                    </div>
                                    <span class="material-symbols-outlined">expand_more</span>
                                </div>
                                <div class="collapse show" id="college-<?= md5($school) ?>">
                                    <div class="school-data mt-2">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Address</th>
                                                        <th>School</th>
                                                        <th>Course</th>
                                                        <th>Year Level</th>
                                                        <th>Semester</th>
                                                        <th>Status</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $idx = 1; foreach ($students as $student): ?>
                                                        <tr>
                                                            <td><?= $idx++ ?></td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? substr($student['middle_name'], 0, 1) . '. ' : '') . $student['last_name']) ?></strong>
                                                            </td>
                                                             <td><?= htmlspecialchars($student['address'] ?? 'N/A') ?></td>
                                                              <td><?= htmlspecialchars($student['school'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['course'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['year_level'] ?? 'N/A') ?></td>
                                                            <td><?= htmlspecialchars($student['semester'] ?? 'N/A') ?></td>
                                                            <td><span class="badge bg-success status-badge"><?= htmlspecialchars($student['status'] ?? 'graduated') ?></span></td>
                                                            <td><small><?= htmlspecialchars($student['email'] ?? 'N/A') ?></small></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <p>No college graduate records found for <?= htmlspecialchars($selected_year) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <span class="material-symbols-outlined">folder_open</span>
            </div>
            <p>No archived records available</p>
            <small class="text-muted">Archive records will appear here after graduates complete their scholarships</small>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle collapse icon rotation
    document.querySelectorAll('.school-header').forEach(header => {
        header.addEventListener('click', function() {
            const icon = this.querySelector('.material-symbols-outlined');
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target.classList.contains('show')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
</script>
        </div>
    </div>
</div>

</body>
</html>
