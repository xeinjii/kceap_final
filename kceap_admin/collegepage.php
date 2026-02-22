<?php
session_start();
include 'header.php';
require_once '../config/config.php';
// make sure getMailer() is defined

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
$admin_id = $_SESSION['admin_id'];
?>

<body>
    <div class="d-flex">
        <?php include 'aside.php'; ?>
        <div class="main-content flex-grow-1">
            <!-- Header Section -->
            <div class="container-fluid bg-light py-3 mb-4 border-bottom">
                <h2 class="mb-0">College Management</h2>
                <p class="text-muted mb-0">Manage college applicants, schedules, and records</p>
            </div>
            <div class="container py-2">
                <div class="row g-4">

                    <div class="col-md-3">
                        <a href="../kceap_admin/COLLEGE/pending.php" class="text-decoration-none text-reset">
                            <div class="card folder-card text-center shadow-sm h-100">
                                <div class="card-body">
                                    <span class="material-icons fs-1 text-warning">pending</span>
                                    <h5 class="card-title mt-2">Pending</h5>
                                    <p class="text-muted small">View all pending applicants</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a href="../kceap_admin/COLLEGE/set_schedule.php" class="text-decoration-none text-reset">
                            <div class="card folder-card text-center shadow-sm h-100">
                                <div class="card-body">
                                    <span class="material-icons fs-1 text-success">folder</span>
                                    <h5 class="card-title mt-2">New Applicants</h5>
                                    <p class="text-muted small">View all new applicants</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../kceap_admin/COLLEGE/exam_list.php" class="text-decoration-none text-reset">
                            <div class="card folder-card text-center shadow-sm h-100">
                                <div class="card-body">
                                    <span class="material-icons fs-1 text-info">list_alt</span>
                                    <h5 class="card-title mt-2">Exam List</h5>
                                    <p class="text-muted small">View all scheduled examine applicants</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- New Records Folder Card -->
                    <div class="col-md-3">
                        <a href="../kceap_admin/COLLEGE/college_records.php" class="text-decoration-none text-reset">
                            <div class="card folder-card text-center shadow-sm h-100">
                                <div class="card-body">
                                    <span class="material-icons fs-1 text-secondary">folder_special</span>
                                    <h5 class="card-title mt-2">Records</h5>
                                    <p class="text-muted small">View all applicant records</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Expired Applicants Card -->
                    <div class="col-md-3">
                        <a href="../kceap_admin/COLLEGE/expired.php" class="text-decoration-none text-reset">
                            <div class="card folder-card text-center shadow-sm h-100">
                                <div class="card-body">
                                    <span class="material-icons fs-1 text-danger">report_problem</span>
                                    <h5 class="card-title mt-2">Expired Applicants</h5>
                                    <p class="text-muted small">View applicants with expired deadlines</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
    <script src="../script/bootstrap.bundle.min.js"></script>
</body>

</html>