<?php
session_start();
require_once __DIR__ . '/config/config.php';

$announcements = [];
$filter = $_GET['filter'] ?? 'all'; // values: all, table (saved only), sent (sent via email)

if (isset($conn)) {
    $allowed = ['all', 'table', 'sent'];
    if (!in_array($filter, $allowed))
        $filter = 'all';

    $sql = "SELECT title, message, created_at, sent FROM announcement";
    if ($filter === 'table') {
        $sql .= " WHERE sent = 0";
    } elseif ($filter === 'sent') {
        $sql .= " WHERE sent = 1";
    }
    $sql .= " ORDER BY created_at DESC";

    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $announcements[] = [
                'title' => $row['title'],
                'content' => $row['message'],
                'date' => date('F j, Y g:i A', strtotime($row['created_at'])),
                'sent' => (int) $row['sent']
            ];
        }
        $result->free();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>KCEAP Scholarships - Announcements</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Latest news and updates for KCEAP Scholarships">
    <link rel="icon" href="./img/logo.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd 0%, #fff 100%);
            min-height: 100vh;
        }

        .brand-text {
            font-weight: 600;
            font-size: 1.3rem;
            color: #1976d2;
        }

        .announcement-card {
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(25, 118, 210, 0.08);
        }

        .material-symbols-outlined {
            vertical-align: middle;
            font-size: 2rem;
            color: #1976d2;
        }

        .announcement-date {
            font-size: 0.95rem;
            color: #1976d2;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="./img/logo.png" alt="KCEAP Logo" width="40" height="auto" class="me-2">
                <span class="brand-text text-white">KCEAP Scholarships</span>
            </a>
        </div>
    </nav>


    <!-- Announcement Section -->
    <section class="py-5" id="announcements">
        <div class="container">
            <div class="text-center mb-4">
                <span class="material-symbols-outlined fs-1 mb-2">campaign</span>
                <h2 class="section-title mb-2">Latest Announcements</h2>
                <p class="section-subtitle text-muted">Stay updated with the latest news and updates from KCEAP
                    Scholarships</p>

                <!-- Filter radios -->
                <form id="ann-filter-form" method="get" class="d-inline-block mt-3">
                    <div class="btn-group" role="radiogroup" aria-label="announcement filters">
                        <input type="radio" class="btn-check" name="filter" id="filter-all" value="all"
                            autocomplete="off" <?= $filter === 'all' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="filter-all">All</label>

                    </div>
                </form>
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach ($announcements as $a): ?>
                    <div class="col-md-8">
                        <div class="card announcement-card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="material-symbols-outlined me-2">notifications_active</span>
                                    <h4 class="mb-0"><?= htmlspecialchars($a['title']) ?></h4>
                                </div>
                                <div class="announcement-date mb-2">
                                    <span class="material-symbols-outlined align-middle me-1"
                                        style="font-size:1.2rem;">calendar_month</span>
                                    <?= htmlspecialchars($a['date']) ?>
                                </div>
                                <p class="mb-0"><?= htmlspecialchars($a['content']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a class="d-flex align-items-center mb-3 text-decoration-none" href="#">
                        <img src="./img/logo.png" alt="KCEAP Logo" width="40" height="auto" class="me-2">
                        <span class="brand-text fs-5">KCEAP Scholarships</span>
                    </a>
                    <p>Empowering students through financial support and educational opportunities since 2003.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php#home" class="text-white-50 text-decoration-none">Home</a>
                        </li>
                        <li class="mb-2"><a href="announcement.php"
                                class="text-white-50 text-decoration-none">Announcement</a></li>
                        <li class="mb-2"><a href="index.php#features" class="text-white-50 text-decoration-none">Key
                                features</a></li>
                        <li class="mb-2"><a href="index.php#eligibility"
                                class="text-white-50 text-decoration-none">Eligibility</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="mb-3">Contact Us</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2 d-flex align-items-start">
                            <span class="material-symbols-outlined text-primary me-2">location_on</span>
                            Old City Hall, Kabankalan City, Negros Occidental.
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <span class="material-symbols-outlined text-primary me-2">mail</span>
                            scholarships@kceap.org
                        </li>
                        <li class="d-flex align-items-center">
                            <span class="material-symbols-outlined text-primary me-2">call</span>
                            09766448484
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; 2023 KCEAP Scholarships. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>