<?php
session_start();
include 'header.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get unique school years from highschool table
$years_sql = "SELECT DISTINCT school_year FROM hs_reports ORDER BY school_year DESC";
$years_result = $conn->query($years_sql);
$school_years = $years_result ? $years_result->fetch_all(MYSQLI_ASSOC) : [];

// Get selected school year (default to most recent)
$selected_year = $_GET['year'] ?? ($school_years[0]['school_year'] ?? date('Y') . '-' . (date('Y') + 1));

// Get highschool data
$hs_sql = "
    SELECT applicant_id, first_name, middle_name, last_name, school, strand, year_level, semester, 
           address, phone_number, email, status, school_year, archived_at
    FROM hs_reports 
    WHERE school_year = ? 
    ORDER BY school ASC, first_name ASC
";

$hs_stmt = $conn->prepare($hs_sql);
$hs_stmt->bind_param("s", $selected_year);
$hs_stmt->execute();
$hs_data = $hs_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$hs_stmt->close();

// Group highschool data by school
$hs_by_school = [];
$strand_count = [];
foreach ($hs_data as $row) {
    $school = $row['school'] ?? 'Unspecified';
    $strand = $row['strand'] ?? 'Unspecified';
    
    if (!isset($hs_by_school[$school])) {
        $hs_by_school[$school] = [];
    }
    $hs_by_school[$school][] = $row;
    
    if (!isset($strand_count[$strand])) {
        $strand_count[$strand] = 0;
    }
    $strand_count[$strand]++;
}

$selected_school = $_GET['school'] ?? 'all';

// Filter data by selected school
if ($selected_school !== 'all') {
    $hs_by_school = array_filter($hs_by_school, function($key) use ($selected_school) {
        return $key === $selected_school;
    }, ARRAY_FILTER_USE_KEY);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Highschool Archive - KCEAP</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="../style/kceapadmin.css" rel="stylesheet">
    <style>
        .filter-section { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .school-section { margin-bottom: 1.5rem; }
        .school-header { background: linear-gradient(135deg, #20c997, #17a2b8); color: white; padding: 1rem; border-radius: 8px; cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s; }
        .school-header:hover { background: linear-gradient(135deg, #17a2b8, #138496); box-shadow: 0 4px 12px rgba(32, 201, 151, 0.3); }
        .school-header h5 { margin: 0; font-weight: 600; }
        .school-data { margin-top: 0.5rem; }
        .table { font-size: 0.9rem; }
        .table thead { background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); color: white; }
        .table th { font-weight: 700; color: white; border-bottom: none; padding: 1rem 0.75rem; }
        .table tbody tr { border-bottom: 1px solid #eee; }
        .table tbody tr:hover { background-color: #f8fffe; }
        .table td { padding: 1rem 0.75rem; vertical-align: middle; }
        .badge-count { background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); color: white; padding: 0.5rem 1.5rem; border-radius: 25px; font-size: 0.95rem; font-weight: 600; box-shadow: 0 2px 8px rgba(32, 201, 151, 0.2); }
        .status-badge { font-size: 0.75rem; padding: 0.35rem 0.75rem; font-weight: 600; border-radius: 6px; }
        .stats { display: flex; gap: 2rem; margin-top: 2rem; flex-wrap: wrap; }
        .stat-item { display: flex; flex-direction: column; background: linear-gradient(135deg, #f0fdf9 0%, #f5fffd 100%); padding: 1.5rem 2rem; border-radius: 12px; border-left: 4px solid #20c997; box-shadow: 0 2px 8px rgba(0,0,0,0.04); flex: 1; min-width: 150px; }
        .stat-label { font-size: 0.85rem; color: #666; margin-bottom: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 2.2rem; font-weight: 700; color: #20c997; }
        .empty-state { text-align: center; padding: 4rem 1rem; color: #999; }
        .empty-state-icon { font-size: 4rem; margin-bottom: 1rem; color: #ccc; }
        .main-content .container-fluid { padding-left: 2rem; padding-right: 2rem; }
        .strand-badge { display: inline-block; margin: 0.35rem; padding: 0.6rem 1rem; background: linear-gradient(135deg, #e7f3ff 0%, #f0f9ff 100%); color: #0066cc; border-radius: 20px; font-size: 0.9rem; font-weight: 600; border: 1px solid #cce5ff; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem; }
        .navbar { background-color: white !important; border-bottom: 2px solid #f0f0f0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .navbar .navbar-brand { color: #20c997 !important; font-weight: 700; }
        .navbar .btn { color: #666; }
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
                <a class="navbar-brand" href="#">
                    <span class="material-symbols-outlined align-middle me-2">school</span>
                    Highschool Archive
                </a>
                <div class="ms-auto">
                    <a href="archive.php" class="btn btn-sm btn-outline-secondary me-2">
                        <span class="material-symbols-outlined align-middle me-1">arrow_back</span>Back to Archives
                    </a>
                </div>
            </div>
        </nav><br><br><br>

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <!-- Filter Section -->
            <div class="filter-section">
                <h5 class="mb-3"><span class="material-symbols-outlined align-middle me-2">filter_list</span>Filter Archives</h5>
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <label for="schoolSelect" class="form-label fw-semibold">School</label>
                        <select class="form-select" id="schoolSelect" name="school" onchange="this.form.submit()">
                            <option value="all" <?= ($selected_school === 'all') ? 'selected' : '' ?>>All Schools</option>
                            <?php foreach (array_keys($hs_by_school) as $school): ?>
                                <option value="<?= htmlspecialchars($school) ?>" <?= ($school === $selected_school) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($school) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <?php if (!empty($hs_data)): ?>
                <!-- Page Header with Stats -->
                <div class="page-header">
                    <div>
                        <h5 class="mb-3">
                            <span class="material-symbols-outlined align-middle me-2">school</span>
                            Highschool Graduates - <?= htmlspecialchars($selected_year) ?>
                        </h5>
                    </div>
                    <span class="badge-count"><?= count($hs_data) ?> Total Records</span>
                </div>



                <!-- Statistics -->
                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Students</span>
                        <span class="stat-value"><?= count($hs_data) ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Schools</span>
                        <span class="stat-value"><?= count($hs_by_school) ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Strands</span>
                        <span class="stat-value"><?= count($strand_count) ?></span>
                    </div>
                </div>

                <!-- Strand Distribution -->
                <?php if (!empty($strand_count)): ?>
                    <div style="margin-top: 2rem; background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h6 class="mb-3"><span class="material-symbols-outlined align-middle me-2">category</span>Strand Distribution</h6>
                        <div>
                            <?php foreach ($strand_count as $strand => $count): ?>
                                <div class="strand-badge"><?= htmlspecialchars($strand) ?> (<?= $count ?>)</div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Schools Data -->
                <div class="mt-4">
                    <?php foreach ($hs_by_school as $school => $students): ?>
                        <div class="school-section">
                            <div class="school-header" data-bs-toggle="collapse" data-bs-target="#hs-<?= md5($school) ?>">
                                <div>
                                    <h5><?= htmlspecialchars($school) ?></h5>
                                    <small><?= count($students) ?> student(s)</small>
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
                                                    <th>Strand</th>
                                                    <th>Year Level</th>
                                                    <th>Semester</th>
                                                    <th>Address</th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                    <th>Archived</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $idx = 1; foreach ($students as $student): ?>
                                                    <tr>
                                                        <td><?= $idx++ ?></td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? substr($student['middle_name'], 0, 1) . '. ' : '') . $student['last_name']) ?></strong>
                                                        </td>
                                                        <td><?= htmlspecialchars($student['strand'] ?? 'N/A') ?></td>
                                                        <td><?= htmlspecialchars($student['year_level'] ?? 'N/A') ?></td>
                                                        <td><?= htmlspecialchars($student['semester'] ?? 'N/A') ?></td>
                                                        <td><small><?= htmlspecialchars($student['address'] ?? 'N/A') ?></small></td>
                                                        <td><?= htmlspecialchars($student['phone_number'] ?? 'N/A') ?></td>
                                                        <td><small><?= htmlspecialchars($student['email'] ?? 'N/A') ?></small></td>
                                                        <td><span class="badge bg-success status-badge"><?= htmlspecialchars($student['status'] ?? 'graduated') ?></span></td>
                                                        <td><?= !empty($student['archived_at']) ? date('M d, Y', strtotime($student['archived_at'])) : 'N/A' ?></td>
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
                        <span class="material-symbols-outlined">folder_open</span>
                    </div>
                    <p>No highschool graduate records found for <?= htmlspecialchars($selected_year) ?></p>
                    <small class="text-muted">Archive records will appear here after graduates complete their scholarships</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
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

</body>
</html>
