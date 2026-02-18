<?php
session_start();
include 'header.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Get the admin fullname from session
$adminName = $_SESSION['admin_name'];
?>

<body>

    <div class="d-flex">

        <?php include 'aside.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand navbar-light mb-4">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">KCEAP Admin Dashboard</a>

                    <!-- Display admin fullname on the right -->
                    <div class="ms-auto">
                        <span class="fw-bold">Welcome,
                            <?php echo htmlspecialchars($adminName); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->


            <?php
            // Summary counts
            $collegeTotal = $collegePending = $collegeActive = $collegeRenewals = $collegeIncomplete = 0;
            $hsTotal = $hsPending = $hsActive = $hsRenewals = $hsIncomplete = 0;

            // College counts
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM college_account");
            if ($res) {
                $collegeTotal = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM college_account WHERE LOWER(status) = 'pending'");
            if ($res) {
                $collegePending = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM college_account WHERE LOWER(status) = 'active'");
            if ($res) {
                $collegeActive = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM college_account WHERE LOWER(status) = 'incomplete'");
            if ($res) {
                $collegeIncomplete = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(DISTINCT account_id) AS cnt FROM college_renew_documents");
            if ($res) {
                $collegeRenewals = (int) $res->fetch_assoc()['cnt'];
            }

            // Highschool counts
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM highschool_account");
            if ($res) {
                $hsTotal = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM highschool_account WHERE LOWER(status) = 'pending'");
            if ($res) {
                $hsPending = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM highschool_account WHERE LOWER(status) = 'active'");
            if ($res) {
                $hsActive = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(*) AS cnt FROM highschool_account WHERE LOWER(status) = 'incomplete'");
            if ($res) {
                $hsIncomplete = (int) $res->fetch_assoc()['cnt'];
            }
            $res = $conn->query("SELECT COUNT(DISTINCT account_id) AS cnt FROM highschool_renew_documents");
            if ($res) {
                $hsRenewals = (int) $res->fetch_assoc()['cnt'];
            }
            ?>

            <div class="container py-3">
                <div class="row g-4">
                    <!-- College Cards -->
                    <div class="col-md-6">
                        <h4 class="mb-3">College Overview</h4>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-primary">🏛️</div>
                                        <h3 class="mb-0"><?= $collegeTotal ?></h3>
                                        <small class="text-muted">Total Accounts</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-warning">⏳</div>
                                        <h3 class="mb-0 text-warning"><?= $collegePending ?></h3>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-success">✅</div>
                                        <h3 class="mb-0 text-success"><?= $collegeActive ?></h3>
                                        <small class="text-muted">Active</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-secondary">📄</div>
                                        <h3 class="mb-0 text-secondary"><?= $collegeIncomplete ?></h3>
                                        <small class="text-muted">Incomplete</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <a href="COLLEGE/college_records.php" class="btn btn-outline-primary w-100">View College
                                    Records</a>
                            </div>
                        </div>
                    </div>

                    <!-- Highschool Cards -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Highschool Overview</h4>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-primary">🏫</div>
                                        <h3 class="mb-0"><?= $hsTotal ?></h3>
                                        <small class="text-muted">Total Accounts</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-warning">⏳</div>
                                        <h3 class="mb-0 text-warning"><?= $hsPending ?></h3>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-success">✅</div>
                                        <h3 class="mb-0 text-success"><?= $hsActive ?></h3>
                                        <small class="text-muted">Active</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="mb-2 display-6 text-secondary">📄</div>
                                        <h3 class="mb-0 text-secondary"><?= $hsIncomplete ?></h3>
                                        <small class="text-muted">Incomplete</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <a href="HIGHSCHOOL/highschool_records.php" class="btn btn-outline-primary w-100">View
                                    Highschool Records</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <?php
            // Prepare status distribution for college and highschool
            $collegeStatusData = ['pending' => 0, 'active' => 0, 'incomplete' => 0];
            $res = $conn->query("SELECT LOWER(status) AS s, COUNT(*) AS c FROM college_account GROUP BY LOWER(status)");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $k = $r['s'] ?: 'incomplete';
                    $collegeStatusData[$k] = (int) $r['c'];
                }
            }

            $hsStatusData = ['pending' => 0, 'active' => 0, 'incomplete' => 0];
            $res = $conn->query("SELECT LOWER(status) AS s, COUNT(*) AS c FROM highschool_account GROUP BY LOWER(status)");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $k = $r['s'] ?: 'incomplete';
                    $hsStatusData[$k] = (int) $r['c'];
                }
            }

            // Monthly registrations (last 6 months) - try to use `created_at` or `schedule_date` fallback
            $months = [];
            $monthLabels = [];
            for ($i = 5; $i >= 0; $i--) {
                $dt = new DateTime("first day of -$i month");
                $key = $dt->format('Y-m');
                $months[] = $key;
                $monthLabels[] = $dt->format('M Y');
            }

            $collegeMonthly = array_fill(0, 6, 0);
            $hsMonthly = array_fill(0, 6, 0);

            // Try created_at field, else applicant insertion via schedule_date or NULL
            $res = $conn->query("SELECT DATE_FORMAT(COALESCE(created_at, schedule_date), '%Y-%m') AS ym, COUNT(*) AS c FROM college_account GROUP BY ym");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $idx = array_search($r['ym'], $months);
                    if ($idx !== false)
                        $collegeMonthly[$idx] = (int) $r['c'];
                }
            }
            $res = $conn->query("SELECT DATE_FORMAT(COALESCE(created_at, schedule_date), '%Y-%m') AS ym, COUNT(*) AS c FROM highschool_account GROUP BY ym");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $idx = array_search($r['ym'], $months);
                    if ($idx !== false)
                        $hsMonthly[$idx] = (int) $r['c'];
                }
            }
            ?>

            <div class="container py-3">
                <h4 class="mb-3">Insights & Analytics</h4>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card p-3 h-100">
                            <h6>College Status Distribution</h6>
                            <canvas id="collegeStatusChart" height="200"></canvas>
                            <div class="mt-2 small text-muted">Pending: <?= $collegeStatusData['pending'] ?> · Active:
                                <?= $collegeStatusData['active'] ?> · Incomplete:
                                <?= $collegeStatusData['incomplete'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card p-3 h-100">
                            <h6>Highschool Status Distribution</h6>
                            <canvas id="hsStatusChart" height="200"></canvas>
                            <div class="mt-2 small text-muted">Pending: <?= $hsStatusData['pending'] ?> · Active:
                                <?= $hsStatusData['active'] ?> · Incomplete: <?= $hsStatusData['incomplete'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card p-3 h-100">
                            <h6>Monthly Registrations (Last 6 months)</h6>
                            <canvas id="monthlyChart" height="200"></canvas>
                            <div class="mt-2 small text-muted">College vs Highschool</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const monthLabels = <?= json_encode($monthLabels) ?>;
                const collegeStatusData = <?= json_encode(array_values($collegeStatusData)) ?>;
                const hsStatusData = <?= json_encode(array_values($hsStatusData)) ?>;
                const collegeMonthly = <?= json_encode(array_values($collegeMonthly)) ?>;
                const hsMonthly = <?= json_encode(array_values($hsMonthly)) ?>;

                // College status pie
                new Chart(document.getElementById('collegeStatusChart'), {
                    type: 'pie',
                    data: {
                        labels: ['Pending', 'Active', 'Incomplete'],
                        datasets: [{ data: collegeStatusData, backgroundColor: ['#ffc107', '#198754', '#6c757d'] }]
                    }
                });

                // Highschool status pie
                new Chart(document.getElementById('hsStatusChart'), {
                    type: 'pie',
                    data: {
                        labels: ['Pending', 'Active', 'Incomplete'],
                        datasets: [{ data: hsStatusData, backgroundColor: ['#ffc107', '#198754', '#6c757d'] }]
                    }
                });

                // Monthly registrations bar
                new Chart(document.getElementById('monthlyChart'), {
                    type: 'bar',
                    data: {
                        labels: monthLabels,
                        datasets: [
                            { label: 'College', data: collegeMonthly, backgroundColor: '#0d6efd' },
                            { label: 'Highschool', data: hsMonthly, backgroundColor: '#6f42c1' }
                        ]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } }
                });
            </script>
        </div>
    </div>

    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="../script/bootstrap.bundle.min.js"></script>
</body>

</html>