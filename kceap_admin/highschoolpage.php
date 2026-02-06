<?php
include 'header.php';
?>
<body>
    <div class="d-flex">
        <?php include 'aside.php'; ?>
        <div class="main-content flex-grow-1">
            <!-- Header Section -->
            <div class="container-fluid bg-light py-3 mb-4 border-bottom">
                <h2 class="mb-0">High School Management</h2>
                <p class="text-muted mb-0">Manage high school applicants, schedules, and records</p>
            </div>
            <div class="container py-5"> 
                <div class="row g-4">
                     
                    <div class="col-md-3">
                        <a href="../kceap_admin/HIGHSCHOOL/pending.php" class="text-decoration-none text-reset">
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
                        <a href="../kceap_admin/HIGHSCHOOL/set_schedule.php" class="text-decoration-none text-reset"
                            data-bs-toggle="modal" data-bs-target="#scheduleModal">
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
                        <a href="../kceap_admin/HIGHSCHOOL/exam_list.php" class="text-decoration-none text-reset">
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
                        <a href="../kceap_admin/HIGHSCHOOL/highschool_records.php" class="text-decoration-none text-reset">
                            <div class="card folder-card text-center shadow-sm h-100">
                                <div class="card-body">
                                    <span class="material-icons fs-1 text-secondary">folder_special</span>
                                    <h5 class="card-title mt-2">Records</h5>
                                    <p class="text-muted small">View all applicant records</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../script/bootstrap.bundle.min.js"></script>
</body>
</html>