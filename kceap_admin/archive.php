<?php
session_start();
include 'header.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get stats for both archives
$hs_count_sql = "SELECT COUNT(*) as total FROM hs_reports";
$college_count_sql = "SELECT COUNT(*) as total FROM college_reports";

$hs_count = $conn->query($hs_count_sql)->fetch_assoc()['total'] ?? 0;
$college_count = $conn->query($college_count_sql)->fetch_assoc()['total'] ?? 0;

// Get school years
$hs_years_sql = "SELECT DISTINCT school_year FROM hs_reports ORDER BY school_year DESC LIMIT 5";
$college_years_sql = "SELECT DISTINCT school_year FROM college_reports ORDER BY school_year DESC LIMIT 5";

$hs_years = $conn->query($hs_years_sql)->fetch_all(MYSQLI_ASSOC) ?? [];
$college_years = $conn->query($college_years_sql)->fetch_all(MYSQLI_ASSOC) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Archives - KCEAP</title>
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="../style/kceapadmin.css" rel="stylesheet">
    <style>
        .archive-card { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }
        .archive-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 30px rgba(0,0,0,0.12); 
        }
        .archive-header { 
            padding: 2rem; 
            color: white; 
            display: flex; 
            align-items: center; 
            gap: 1.5rem; 
        }
        .archive-header.hs { background: linear-gradient(135deg, #20c997, #17a2b8); }
        .archive-header.college { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
        .archive-icon { font-size: 3rem; }
        .archive-title { margin: 0; font-weight: 700; font-size: 1.5rem; }
        .archive-body { padding: 2rem; }
        .stat-box { 
            background: #f8f9fa; 
            padding: 1.5rem; 
            border-radius: 8px; 
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }
        .stat-box.hs { border-left-color: #20c997; }
        .stat-box.college { border-left-color: #0d6efd; }
        .stat-label { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-value.hs { color: #20c997; }
        .stat-value.college { color: #0d6efd; }
        .action-buttons { display: flex; gap: 0.75rem; flex-direction: column; }
        .action-btn { 
            padding: 0.75rem 1.5rem; 
            border-radius: 8px; 
            border: none; 
            font-weight: 600; 
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }
        .action-btn.hs { 
            background: #20c997; 
            color: white; 
        }
        .action-btn.hs:hover { 
            background: #17a2b8; 
            color: white; 
        }
        .action-btn.college { 
            background: #0d6efd; 
            color: white; 
        }
        .action-btn.college:hover { 
            background: #0a58ca; 
            color: white; 
        }
        .main-content .container-fluid { padding-left: 2rem; padding-right: 2rem; }
        .page-header { margin-bottom: 3rem; }
        .page-header h2 { font-weight: 700; color: #333; }
        .navbar { background-color: white !important; border-bottom: 2px solid #f0f0f0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .navbar .navbar-brand { color: #333 !important; font-weight: 700; }
        .navbar .btn { color: #666; }
        .year-list { 
            list-style: none; 
            padding: 0; 
            margin: 1rem 0 0 0; 
        }
        .year-list li { 
            padding: 0.5rem 0; 
            font-size: 0.9rem; 
            color: #666;
        }
        .year-list li:before { 
            content: "→ "; 
            margin-right: 0.5rem; 
            color: #ccc; 
        }
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
                    <span class="material-symbols-outlined align-middle me-2">archive</span>
                    KCEAP Archives
                </a>
            </div>
        </nav><br><br><br>

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="page-header">
                <h2 class="mb-2">
                    <span class="material-symbols-outlined align-middle me-2">archive</span>
                    Graduate Archives
                </h2>
                <p class="text-muted">Access and manage archived records for highschool and college graduates</p>
            </div>

            <!-- Archive Cards -->
            <div class="row g-4">
                <!-- Highschool Archive -->
                <div class="col-lg-6">
                    <div class="archive-card">
                        <div class="archive-header hs">
                            <span class="material-symbols-outlined archive-icon">school</span>
                            <h4 class="archive-title">Highschool Archives</h4>
                        </div>
                        <div class="archive-body">
                            <div class="stat-box hs">
                                <div class="stat-label">Total Graduates</div>
                                <div class="stat-value hs"><?= number_format($hs_count) ?></div>
                            </div>

                            <?php if (!empty($hs_years)): ?>
                                <div>
                                    <label class="form-label fw-semibold">Recent Years</label>
                                    <ul class="year-list">
                                        <?php foreach ($hs_years as $year): ?>
                                            <li>
                                                <a href="archive_highschool.php?year=<?= urlencode($year['school_year']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($year['school_year']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="action-buttons mt-4">
                                <a href="archive_highschool.php" class="action-btn hs">
                                    <span class="material-symbols-outlined">open_in_new</span>
                                    View All Records
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- College Archive -->
                <div class="col-lg-6">
                    <div class="archive-card">
                        <div class="archive-header college">
                            <span class="material-symbols-outlined archive-icon">school</span>
                            <h4 class="archive-title">College Archives</h4>
                        </div>
                        <div class="archive-body">
                            <div class="stat-box college">
                                <div class="stat-label">Total Graduates</div>
                                <div class="stat-value college"><?= number_format($college_count) ?></div>
                            </div>

                            <?php if (!empty($college_years)): ?>
                                <div>
                                    <label class="form-label fw-semibold">Recent Years</label>
                                    <ul class="year-list">
                                        <?php foreach ($college_years as $year): ?>
                                            <li>
                                                <a href="archive_college.php?year=<?= urlencode($year['school_year']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($year['school_year']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="action-buttons mt-4">
                                <a href="archive_college.php" class="action-btn college">
                                    <span class="material-symbols-outlined">open_in_new</span>
                                    View All Records
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-5 p-4 bg-light border-start border-4 border-info rounded">
                <h6 class="mb-2">
                    <span class="material-symbols-outlined align-middle me-2">info</span>
                    About Archives
                </h6>
                <p class="mb-0 text-muted">
                    Archive records contain information about graduates who have successfully completed their scholarship programs. 
                    Each archive includes detailed student information, academic records, and completion dates. 
                    You can filter by school year, school, and export data to CSV format for further analysis.
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Archives - KCEAP</title>
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
                <a class="navbar-brand" href="#">
                    <span class="material-symbols-outlined align-middle me-2">archive</span>
                    KCEAP Archives
                </a>
            </div>
        </nav><br><br><br>

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="page-header">
                <h2 class="mb-2">
                    <span class="material-symbols-outlined align-middle me-2">archive</span>
                    Graduate Archives
                </h2>
                <p class="text-muted">Access and manage archived records for highschool and college graduates</p>
            </div>

            <!-- Archive Cards -->
            <div class="row g-4">
                <!-- Highschool Archive -->
                <div class="col-lg-6">
                    <div class="archive-card">
                        <div class="archive-header hs">
                            <span class="material-symbols-outlined archive-icon">school</span>
                            <h4 class="archive-title">Highschool Archives</h4>
                        </div>
                        <div class="archive-body">
                            <div class="stat-box hs">
                                <div class="stat-label">Total Graduates</div>
                                <div class="stat-value hs"><?= number_format($hs_count) ?></div>
                            </div>

                            <?php if (!empty($hs_years)): ?>
                                <div>
                                    <label class="form-label fw-semibold">Recent Years</label>
                                    <ul class="year-list">
                                        <?php foreach ($hs_years as $year): ?>
                                            <li>
                                                <a href="archive_highschool.php?year=<?= urlencode($year['school_year']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($year['school_year']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="action-buttons mt-4">
                                <a href="archive_highschool.php" class="action-btn hs">
                                    <span class="material-symbols-outlined">open_in_new</span>
                                    View All Records
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- College Archive -->
                <div class="col-lg-6">
                    <div class="archive-card">
                        <div class="archive-header college">
                            <span class="material-symbols-outlined archive-icon">school</span>
                            <h4 class="archive-title">College Archives</h4>
                        </div>
                        <div class="archive-body">
                            <div class="stat-box college">
                                <div class="stat-label">Total Graduates</div>
                                <div class="stat-value college"><?= number_format($college_count) ?></div>
                            </div>

                            <?php if (!empty($college_years)): ?>
                                <div>
                                    <label class="form-label fw-semibold">Recent Years</label>
                                    <ul class="year-list">
                                        <?php foreach ($college_years as $year): ?>
                                            <li>
                                                <a href="archive_college.php?year=<?= urlencode($year['school_year']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($year['school_year']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="action-buttons mt-4">
                                <a href="archive_college.php" class="action-btn college">
                                    <span class="material-symbols-outlined">open_in_new</span>
                                    View All Records
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-5 p-4 bg-light border-start border-4 border-info rounded">
                <h6 class="mb-2">
                    <span class="material-symbols-outlined align-middle me-2">info</span>
                    About Archives
                </h6>
                <p class="mb-0 text-muted">
                    Archive records contain information about graduates who have successfully completed their scholarship programs. 
                    Each archive includes detailed student information, academic records, and completion dates. 
                    You can filter by school year, school, and export data to CSV format for further analysis.
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
