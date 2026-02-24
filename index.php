<?php
session_start();
require_once __DIR__ . '/config/config.php';
include 'check_deadline.php';

$collegeLimitReached = false;
$hsLimitReached = false;
$collegeRemaining = 0;
$hsRemaining = 0;

$settings = [];
$jsonPath = __DIR__ . '/kceap_admin/deadline.json';

$now = new DateTime();

if (file_exists($jsonPath)) {
    $settings = json_decode(file_get_contents($jsonPath), true);

    // -----------------------
    // COLLEGE
    // -----------------------
    $collegeLimit = $settings['college']['limit'] ?? 0;
    $collegeDisabled = $settings['college']['disabled'] ?? false;
    $collegeDeadline = !empty($settings['college']['deadline']) ? new DateTime($settings['college']['deadline']) : null;

    // Check if deadline passed
    if ($collegeDeadline && $now > $collegeDeadline) {
        $collegeDisabled = true;
    }

    // Check slot limit
    if ($collegeLimit > 0) {
        $result = $conn->query("SELECT COUNT(*) as total FROM college_schedule");
        $collegeCount = $result->fetch_assoc()['total'] ?? 0;
        $collegeRemaining = max(0, $collegeLimit - $collegeCount);
        if ($collegeRemaining <= 0) {
            $collegeLimitReached = true;
        }
    }

    // Active if not disabled AND limit not reached
    $collegeActive = !$collegeDisabled && !$collegeLimitReached;

    // -----------------------
    // HIGH SCHOOL
    // -----------------------
    $hsLimit = $settings['highschool']['limit'] ?? 0;
    $hsDisabled = $settings['highschool']['disabled'] ?? false;
    $hsDeadline = !empty($settings['highschool']['deadline']) ? new DateTime($settings['highschool']['deadline']) : null;

    // Check if deadline passed
    if ($hsDeadline && $now > $hsDeadline) {
        $hsDisabled = true;
    }

    // Check slot limit
    if ($hsLimit > 0) {
        $result = $conn->query("SELECT COUNT(*) as total FROM highschool_schedule");
        $hsCount = $result->fetch_assoc()['total'] ?? 0;
        $hsRemaining = max(0, $hsLimit - $hsCount);
        if ($hsRemaining <= 0) {
            $hsLimitReached = true;
        }
    }

    // Active if not disabled AND limit not reached
    $hsActive = !$hsDisabled && !$hsLimitReached;

} else {
    // JSON not found
    $collegeActive = false;
    $hsActive = false;
}

// Deadline notes for display
$collegeDeadlineNote = '';
$hsDeadlineNote = '';

if (!$collegeActive && $collegeDeadline) {
    $collegeDeadlineNote = ' (Deadline passed on ' . $collegeDeadline->format('F j, Y, g:i A') . ')';
}
if (!$hsActive && $hsDeadline) {
    $hsDeadlineNote = ' (Deadline passed on ' . $hsDeadline->format('F j, Y, g:i A') . ')';
}

// Format deadlines for display
$collegeDeadlineFormatted = $collegeDeadline ? $collegeDeadline->format('F j, Y - g:i A') : '';
$hsDeadlineFormatted = $hsDeadline ? $hsDeadline->format('F j, Y - g:i A') : '';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Apply for scholarships to fund your education with KCEAP">
    <link rel="icon" href="./img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Font Awesome v6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-pap+Zc91Y4v0+k1HuTgX+Fg5Y+r4Yug3oXvF/HHGzGdF1w3LxOjLkT5y85XoZSmk5bHoYJYhZ9vT0xQF0qKXnQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">



    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./style/index.css">

    <title>KCEAP Scholarships - Fund Your Education</title>

    <style>
        /* Prevent nav links wrapping */
        .navbar-nav .nav-link {
            white-space: nowrap;
        }

        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            padding-top: 120px;
            padding-bottom: 60px;
        }

        .hero-title {
            font-size: 46px;
            font-weight: 700;
            line-height: 1.3;
        }

        .hero-subtitle {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .text-gradient {
            background: linear-gradient(45deg, #ffffff, #cfe2ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-notice {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            backdrop-filter: blur(6px);
        }

        .hero-notice .divider {
            margin: 0 10px;
            opacity: 0.7;
        }

        .hero-btn {
            padding: 14px 28px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .hero-btn-primary {
            background-color: #ffffff;
            color: #0d6efd;
            border: none;
        }

        .hero-btn-primary:hover {
            background-color: #e2e6ea;
            transform: translateY(-3px);
        }

        .hero-btn-outline {
            border: 2px solid #ffffff;
            color: #ffffff;
            background: transparent;
        }

        .hero-btn-outline:hover {
            background-color: #ffffff;
            color: #0d6efd;
            transform: translateY(-3px);
        }

        /* =========================
   Responsive Message Styling
========================= */

        @media (max-width: 768px) {

            .hero-title {
                font-size: 32px;
            }

            .hero-subtitle {
                font-size: 16px;
            }

            .hero-notice {
                font-size: 12px;
                padding: 8px 14px;
                border-radius: 20px;
            }

            .hero-notice .divider {
                margin: 0 6px;
            }
        }



        .social-icons {
            padding: 10px 0;
        }

        .dev-card {
            text-decoration: none;
            color: #333;
            transition: transform 0.3s ease;
        }

        .avatar-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
        }

        .dev-name {
            display: block;
            margin-top: 8px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Hover Effects */
        .dev-card:hover {
            transform: translateY(-5px);
        }

        .dev-card:hover .avatar-wrapper {
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
            transform: scale(1.1);
        }

        /* Mobile & Small Screens */
        @media (max-width: 992px) {
            #about-us .about-img {
                max-width: 200px !important;
                margin-bottom: 1.5rem;
            }

            #about-us .about-text {
                font-size: 0.95rem !important;
            }

            #about-us h2 {
                font-size: 1.8rem;
                text-align: center;
            }

            #about-us .row {
                flex-direction: column-reverse;
                text-align: center;
            }
        }

        /* Extra Small Screens */
        @media (max-width: 576px) {
            #about-us {
                min-height: 60vh;
                padding: 3rem 1rem;
            }

            #about-us .about-img {
                max-width: 150px !important;
            }

            #about-us h2 {
                font-size: 1.5rem;
            }

            #about-us .about-text {
                font-size: 0.9rem !important;
            }
        }

        /* Glass Modal Background */
        .custom-modal {
            background: rgba(20, 20, 20, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        /* Image Wrapper with Gradient Glow */
        .profile-img-wrapper {
            width: 160px;
            height: 160px;
            margin: 0 auto;
            padding: 6px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #6610f2, #6f42c1);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: float 4s ease-in-out infinite;
        }

        /* Glow Effect */
        .profile-img-wrapper::before {
            content: "";
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.4), transparent 70%);
            z-index: -1;
            filter: blur(15px);
        }

        /* Actual Image */
        .profile-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #111;
        }

        /* Floating Animation */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Role Styling */
        .dev-role {
            font-weight: 500;
            color: #9ec5fe;
        }

        /* Description Styling */
        .dev-desc {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.7);
            padding: 0 10px;
        }

        .hero-notice {
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.2);
            color: #1e3a8a;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            display: inline-block;
            backdrop-filter: blur(8px);
        }

        /* Scroll Animation Styles */
        [data-animate] {
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-fadeInLeft {
            animation: fadeInLeft 0.6s ease-out forwards;
        }

        .animate-fadeInRight {
            animation: fadeInRight 0.6s ease-out forwards;
        }

        .animate-fadeInScale {
            animation: fadeInScale 0.6s ease-out forwards;
        }

        .animate-slideInDown {
            animation: slideInDown 0.6s ease-out forwards;
        }

        /* Staggered animations for multiple elements */
        [data-animate-delay="1"] {
            animation-delay: 0.1s;
        }

        [data-animate-delay="2"] {
            animation-delay: 0.2s;
        }

        [data-animate-delay="3"] {
            animation-delay: 0.3s;
        }

        [data-animate-delay="4"] {
            animation-delay: 0.4s;
        }

        [data-animate-delay="5"] {
            animation-delay: 0.5s;
        }

        [data-animate-delay="6"] {
            animation-delay: 0.6s;
        }
    </style>

</head>

<body>

    <?php if (isset($_SESSION['college_apply_success'])): ?>
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Application Submitted Successfully
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <span class="material-symbols-outlined fs-1 text-primary mb-3">
                            check_circle
                        </span>

                        <p class="mb-2 fw-semibold">
                            <?= $_SESSION['college_apply_success']; ?>
                        </p>

                        <small class="text-muted">
                            Please wait for further instructions via email.
                        </small>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                            Okay
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        </script>

        <?php unset($_SESSION['college_apply_success']); ?>
    <?php endif; ?>


    <?php if (isset($_SESSION['highschool_apply_success'])): ?>
        <div class="modal fade" id="hsSuccessModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            High School Application Submitted
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <span class="material-symbols-outlined fs-1 text-primary mb-3">
                            check_circle
                        </span>

                        <p class="mb-2 fw-semibold">
                            <?= $_SESSION['highschool_apply_success']; ?>
                        </p>

                        <small class="text-muted">
                            Please wait for further instructions via email.
                        </small>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                            Okay
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var hsSuccessModal = new bootstrap.Modal(document.getElementById('hsSuccessModal'));
                hsSuccessModal.show();
            });
        </script>

        <?php unset($_SESSION['highschool_apply_success']); ?>
    <?php endif; ?>



    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="#home">
                <img src="./img/logo.png" alt="KCEAP Logo" width="40" height="40" class="d-inline-block align-text-top">
                <span class="brand-text ms-2 fw-bold">KCEAP Scholarships</span>
            </a>

            <!-- Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Key Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#eligibility">Eligibility</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="announcement.php">Announcement</a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>




    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden" id="home">
        <div class="hero-bg-shape shape-1"></div>
        <div class="hero-bg-shape shape-2"></div>

        <div class="container">
            <div class="row justify-content-center min-vh-70 text-center">
                <div class="col-lg-9">

                    <?php if (!empty($messages)): ?>
                        <div class="hero-notice mb-4 text-white">
                            <?= implode('<span class="divider mx-2">|</span>', $messages); ?>
                        </div>
                    <?php endif; ?>




                    <h1 class="hero-title mb-4">
                        Unlock Your Academic Potential with
                        <span class="text-gradient">KCEAP Scholarships</span>
                    </h1>

                    <p class="hero-subtitle mb-5">
                        Supporting students in building a brighter future.
                    </p>

                    <!-- Application Buttons -->
                    <div class="d-flex flex-wrap gap-5 justify-content-center">

                        <!-- College Button -->
                        <div class="text-center">
                            <button
                                class="btn hero-btn hero-btn-primary <?= (!$collegeActive || $collegeLimitReached) ? 'opacity-50' : ''; ?>"
                                <?= (!$collegeActive || $collegeLimitReached)
                                    ? 'disabled title="Registration closed"'
                                    : 'onclick="window.location.href=\'collegeapply.php\'"'; ?>>
                                <?php
                                if ($collegeLimitReached)
                                    echo 'College Full';
                                elseif (!$collegeActive)
                                    echo 'College Closed';
                                else
                                    echo 'Apply for College';
                                ?>
                            </button>

                            <?php if ($collegeActive && !$collegeLimitReached): ?>
                                <div class="mt-2 small fw-semibold text-warning">
                                    <?= $collegeRemaining ?> / <?= $collegeLimit ?> slot(s) remaining
                                </div>
                                <div class="mt-1 small text-light">
                                    Deadline: <?= $collegeDeadlineFormatted ?>
                                </div>
                            <?php elseif (!$collegeActive && !empty($collegeDeadlineNote)): ?>
                                <div class="mt-2 small text-white fw-bold">
                                    <?= $collegeDeadlineNote ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- High School Button -->
                        <div class="text-center">
                            <button
                                class="btn hero-btn hero-btn-outline <?= (!$hsActive || $hsLimitReached) ? 'disabled opacity-50' : ''; ?>"
                                <?= (!$hsActive || $hsLimitReached) ? 'disabled title="Registration closed"' : 'onclick="window.location.href=\'highschoolapply.php\'"'; ?>>
                                <?php
                                if ($hsLimitReached)
                                    echo 'High School Full';
                                elseif (!$hsActive)
                                    echo 'High School Closed';
                                else
                                    echo 'Apply for High School';
                                ?>
                            </button>

                            <?php if ($hsActive && !$hsLimitReached): ?>
                                <div class="mt-2 small fw-semibold text-warning">
                                    <?= $hsRemaining ?> / <?= $hsLimit ?> slot(s) remaining
                                </div>
                                <div class="mt-1 small text-light">
                                    Deadline: <?= $hsDeadlineFormatted ?>
                                </div>
                            <?php elseif (!$hsActive && !empty($hsDeadlineNote)): ?>
                                <div class="mt-2 small text-white fw-bold">
                                    <?= $hsDeadlineNote ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="py-5 bg-light" id="features">
        <div class="container">
            <div class="text-center mb-5" data-animate="fadeInUp">
                <h2 class="section-title">Key Features of the Scholarship Information & Management System</h2>
                <p class="section-subtitle">Tools and automation to simplify your scholarship workflow</p>
            </div>
            <div class="row g-4">

                <!-- User Management -->
                <div class="col-md-4" data-animate="fadeInUp" data-animate-delay="1">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="material-symbols-outlined icon-large text-primary mb-3">manage_accounts</span>
                            <h3 class="card-title">User Management</h3>
                            <p class="card-text">Secure access control for students, reviewers, and admins with
                                role-based permissions.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span>
                                    Profile</li>
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span>Schedules
                                </li>
                                <li><span class="material-symbols-outlined text-success me-2">check_circle</span> Login
                                    security</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#userManagementModal">
                                Learn more about access roles
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Application Tracking -->
                <div class="col-md-4" data-animate="fadeInUp" data-animate-delay="2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="material-symbols-outlined icon-medium text-primary mb-3">assignment</span>
                            <h3 class="card-title">Application Tracking</h3>
                            <p class="card-text">Real-time tracking of scholarship applications and status updates at
                                every stage.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span> Email
                                    notifications</li>
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span> Document
                                    uploads</li>
                                <li><span class="material-symbols-outlined text-success me-2">check_circle</span>
                                    Automated deadlines</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#applicationTrackingModal">
                                Learn more about the process
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Analytics & Reports -->
                <div class="col-md-4" data-animate="fadeInUp" data-animate-delay="3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <span class="material-symbols-outlined icon-large text-primary mb-3">bar_chart</span>
                            <h3 class="card-title">Analytics & Reports</h3>
                            <p class="card-text">Generate reports for scholarship distribution, demographics, and
                                performance insights.</p>
                            <ul class="list-unstyled">

                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span>
                                    Dashboards</li>
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span>
                                    View reports</li>
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span>
                                    Analytics</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#analyticsModal">
                                Learn more about insights
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modals -->

    <!-- User Management Modal -->
    <div class="modal fade" id="userManagementModal" tabindex="-1" aria-labelledby="userManagementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userManagementModalLabel">User Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    User Management ensures secure access control by providing role-based permissions for students,
                    reviewers, and administrators. It enables differentiated dashboards and strict profile verification
                    to protect sensitive data and maintain system integrity.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Tracking Modal -->
    <div class="modal fade" id="applicationTrackingModal" tabindex="-1" aria-labelledby="applicationTrackingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applicationTrackingModalLabel">Application Tracking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Application Tracking offers real-time updates on scholarship applications, including status
                    notifications, document uploads, and automatic deadline reminders, keeping applicants and
                    administrators informed throughout the entire process.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Reports Modal -->
    <div class="modal fade" id="analyticsModal" tabindex="-1" aria-labelledby="analyticsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="analyticsModalLabel">Analytics & Reports</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Analytics & Reports empower institutions to monitor scholarship trends effectively. Real-time visual
                    dashboards and exportable reports help identify key performance indicators, track financial
                    distributions, and make strategic decisions to improve scholarship programs.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
<section class="py-5 bg-light" id="how-it-works">
    <div class="container">
        <div class="text-center mb-5" data-animate="fadeInUp">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">A simple 4-step process to apply for a scholarship</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 text-center" data-animate="fadeInUp" data-animate-delay="1">
                <div class="p-4 border rounded shadow-sm h-100">
                    <span class="material-symbols-outlined text-primary fs-1">event</span>
                    <h5 class="mt-3">Set Schedule</h5>
                    <p class="small">Schedule your preferred date and time for taking the scholarship examination.</p>
                </div>
            </div>
            <div class="col-md-3 text-center" data-animate="fadeInUp" data-animate-delay="2">
                <div class="p-4 border rounded shadow-sm h-100">
                    <span class="material-symbols-outlined text-primary fs-1">description</span>
                    <h5 class="mt-3">Submit Application</h5>
                    <p class="small">Fill out the scholarship form and upload required documents.</p>
                </div>
            </div>
            <div class="col-md-3 text-center" data-animate="fadeInUp" data-animate-delay="3">
                <div class="p-4 border rounded shadow-sm h-100">
                    <span class="material-symbols-outlined text-primary fs-1">hourglass_top</span>
                    <h5 class="mt-3">Wait for Review</h5>
                    <p class="small">Your application will be reviewed by the admin team.</p>
                </div>
            </div>
            <div class="col-md-3 text-center" data-animate="fadeInUp" data-animate-delay="4">
                <div class="p-4 border rounded shadow-sm h-100">
                    <span class="material-symbols-outlined text-primary fs-1">check_circle</span>
                    <h5 class="mt-3">Receive Decision</h5>
                    <p class="small">Get notified about your application status via email.</p>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Eligibility Criteria -->
    <section class="py-5" id="eligibility">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-animate="fadeInLeft">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80"
                        alt="Diverse students studying" class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-lg-6" data-animate="fadeInRight">
                    <h2 class="section-title">Eligibility Requirements</h2>
                    <p class="section-subtitle">Who can apply for KCEAP scholarships?</p>

                    <div class="eligibility-item d-flex mb-3" data-animate="fadeInUp" data-animate-delay="1">
                        <span class="material-symbols-outlined text-primary me-3">task_alt</span>
                        <div>
                            <p class="mb-0">A Filipino citizen and bonafide resident of Kabankalan City</p>
                        </div>
                    </div>

                    <div class="eligibility-item d-flex mb-3" data-animate="fadeInUp" data-animate-delay="2">
                        <span class="material-symbols-outlined text-primary me-3">task_alt</span>
                        <div>
                            <p class="mb-0">With good moral character and without any derogatory record: <br>
                                A. For SHS Category: A grade 10 or ALS (HIGH SCHOOL) graduate with at least eighty (GWA)
                                and no failing grade in any subject in his/her last school year in highschool. <br>
                                B. For COLLEGE Category: A K-12 or ALS (Senior High School)graduate with at least eighty
                                percent (80%) GWA and with no failing grade in any subject in his/her semester in High
                                School.</p>
                        </div>
                    </div>

                    <div class="eligibility-item d-flex mb-3" data-animate="fadeInUp" data-animate-delay="3">
                        <span class="material-symbols-outlined text-primary me-3">task_alt</span>
                        <div>
                            <p class="mb-0">With parents or guardian having a combined gross monthly income of not more
                                than
                                Thirty-Thousand pesos(P30,000.00).</p>
                        </div>
                    </div>

                    <div class="eligibility-item d-flex" data-animate="fadeInUp" data-animate-delay="4">
                        <span class="material-symbols-outlined text-primary me-3">task_alt</span>
                        <div>
                            <p class="mb-0">Presently not enjoying any other government-funded scholarship grant</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About Us Section -->
    <section id="about-us" class="d-flex align-items-center"
        style="min-height: 70vh; background: linear-gradient(135deg, #0d6efd, #6610f2); color: white;">
        <div class="container">
            <div class="row align-items-center g-5">
                <!-- Image Section -->
                <div class="col-lg-5 text-center text-lg-start" data-animate="fadeInLeft">
                    <img src="img/logo.png" alt="KCEAP Scholarship Info System"
                        class="img-fluid rounded-circle shadow-lg about-img" style="max-width: 300px;">
                </div>
                <!-- Text Section -->
                <div class="col-lg-7" data-animate="fadeInRight">
                    <h2 class="fw-bold mb-3" data-animate="slideInDown" data-animate-delay="1">About <span
                            style="color:#ffc107;">KCEAP Scholarships</span></h2>
                    <p class="lead mb-3 about-text" data-animate="fadeInUp" data-animate-delay="2">
                        The <strong>KCEAP Scholarship Information and Management System</strong> empowers students by
                        providing a centralized platform to efficiently manage scholarship applications, deadlines, and
                        schedules.
                        Since 2020, it has supported students in achieving their educational goals through financial
                        aid, academic resources, and structured guidance.
                    </p>
                    <p class="about-text" style="color: rgba(255,255,255,0.8); font-size: 0.95rem;"
                        data-animate="fadeInUp" data-animate-delay="3">
                        Our mission is to make scholarships more accessible, transparent, and manageable for every
                        aspiring student, ensuring that no opportunity is missed.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4" data-animate="fadeInUp" data-animate-delay="1">
                    <a class="d-flex align-items-center mb-3 text-decoration-none" href="#">
                        <img src="img/logo.png" alt="KCEAP Logo" width="40" class="d-inline-block align-text-top me-2">
                        <span class="brand-text fs-5">kceap scholarships</span>
                    </a>
                    <p class="text-white-50">empowering students through financial support and educational opportunities
                        since 2020.</p>

                    <!-- Developer photos -->
                    <p><strong>Researchers</strong></p>
                    <div class="social-icons d-flex mt-3">
                        <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#devModal"
                            data-name="Matt Andrei Belano" data-role="Programmer" data-img="img/d11.jpeg">
                            <img src="img/d11.jpeg" alt="developer 1" class="rounded-circle" width="30" height="30">
                        </a>
                        <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#devModal"
                            data-name="Rose Ann Bernabe" data-role="Documentation I" data-img="img/d2.jpeg">
                            <img src="img/d2.jpeg" alt="developer 2" class="rounded-circle" width="30" height="30">
                        </a>
                        <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#devModal"
                            data-name="Christian Goza" data-role="Designer" data-img="img/d3.jpg">
                            <img src="img/d3.jpg" alt="developer 3" class="rounded-circle" width="30" height="30">
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#devModal" data-name="Mercy Ann Cabunag"
                            data-role="Documentation II" data-img="img/d4.jpg">
                            <img src="img/d4.jpg" alt="developer 4" class="rounded-circle" width="30" height="30">
                        </a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4" data-animate="fadeInUp" data-animate-delay="2">
                    <h6 class="mb-3 text-uppercase">quick links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2" data-animate="fadeInLeft" data-animate-delay="1"><a href="#home"
                                class="text-white-50 text-decoration-none">home</a></li>
                        <li class="mb-2" data-animate="fadeInLeft" data-animate-delay="2"><a href="#features"
                                class="text-white-50 text-decoration-none">key features</a>
                        </li>
                        <li class="mb-2" data-animate="fadeInLeft" data-animate-delay="3"><a href="#eligibility"
                                class="text-white-50 text-decoration-none">eligibility</a></li>
                        <li class="mb-2" data-animate="fadeInLeft" data-animate-delay="4"><a href="announcement.php"
                                class="text-white-50 text-decoration-none">announcement</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4" data-animate="fadeInUp" data-animate-delay="3">
                    <h6 class="mb-3 text-uppercase">contact us</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2 d-flex align-items-start" data-animate="fadeInRight" data-animate-delay="1">
                            <span class="material-symbols-outlined text-primary me-2">location_on</span>
                            old city hall, kabankalan city, negros occidental.
                        </li>
                        <li class="mb-2 d-flex align-items-center" data-animate="fadeInRight" data-animate-delay="2">
                            <span class="material-symbols-outlined text-primary me-2">mail</span>
                            scholarships@kceap.org
                        </li>
                        <li class="d-flex align-items-center" data-animate="fadeInRight" data-animate-delay="3">
                            <span class="material-symbols-outlined text-primary me-2">call</span>
                            09766448484
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="row">
                <div class="col-md-6 text-center text-md-start" data-animate="fadeInUp" data-animate-delay="4">
                    <p class="mb-0 text-white-50">&copy; 2023 kceap scholarships. all rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Enhanced Developer Modal -->
    <div class="modal fade" id="devModal" tabindex="-1" aria-labelledby="devModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal text-white">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="devModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">

                    <!-- Enhanced Image Wrapper -->
                    <div class="profile-img-wrapper mb-4">
                        <img src="" id="devModalImg" alt="Developer Photo" class="profile-img">
                    </div>

                    <h6 id="devModalRole" class="dev-role mb-2"></h6>
                </div>
            </div>
        </div>
    </div>

    <script>
        const devModal = document.getElementById('devModal');
        devModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-name');
            const role = button.getAttribute('data-role');
            const img = button.getAttribute('data-img');

            document.getElementById('devModalLabel').textContent = name;
            document.getElementById('devModalRole').textContent = role;
            document.getElementById('devModalImg').src = img;
            document.getElementById('devModalImg').alt = name;
        });
    </script>








    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Scroll Animation Script -->
    <script>
        // Initialize scroll animations on page load
        document.addEventListener('DOMContentLoaded', function () {
            initScrollAnimations();
            // Trigger animations for already visible elements immediately
            setTimeout(() => checkElementsInView(), 100);
            window.addEventListener('scroll', handleScroll);
        });

        function initScrollAnimations() {
            // Add animation attributes to key elements
            const sections = document.querySelectorAll('section, .card, .row, .col-*');

            sections.forEach((section, index) => {
                if (!section.hasAttribute('data-animate')) {
                    // Determine animation type based on position
                    const animationTypes = ['fadeInUp', 'fadeInLeft', 'fadeInRight', 'fadeInScale'];
                    const animationType = animationTypes[index % animationTypes.length];
                    section.setAttribute('data-animate', animationType);
                }
            });

            // Add staggered animation to child elements within sections
            document.querySelectorAll('section, .container').forEach(parent => {
                const children = parent.querySelectorAll('> .row > [class*="col"], > .card, > .feature-card, > h1, > h2, > h3, > p');
                children.forEach((child, index) => {
                    if (!child.hasAttribute('data-animate')) {
                        child.setAttribute('data-animate', 'fadeInUp');
                        child.setAttribute('data-animate-delay', Math.min(index + 1, 6));
                    }
                });
            });

            // Check initial viewport visibility
            checkElementsInView();
        }

        function handleScroll() {
            checkElementsInView();
        }

        function checkElementsInView() {
            const elements = document.querySelectorAll('[data-animate]:not(.animated)');

            elements.forEach(element => {
                const rect = element.getBoundingClientRect();
                // Use lower threshold for elements to trigger animation earlier
                const isVisible = rect.top < window.innerHeight && rect.bottom > -100;

                if (isVisible) {
                    const animationType = element.getAttribute('data-animate');
                    element.classList.add(`animate-${animationType}`, 'animated');
                }
            });
        }

        // Optional: Throttle scroll event for better performance
        function throttle(func, limit) {
            let inThrottle;
            return function () {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            }
        }

        window.addEventListener('scroll', throttle(handleScroll, 200));
    </script>

    <script>
        // Parse PHP deadlines and limits to JS
        const collegeDeadline = new Date("<?= $collegeDeadline ? $collegeDeadline->format('Y-m-d H:i:s') : '' ?>");
        const hsDeadline = new Date("<?= $hsDeadline ? $hsDeadline->format('Y-m-d H:i:s') : '' ?>");

        const collegeLimit = <?= $collegeLimit ?? 0 ?>;
        const hsLimit = <?= $hsLimit ?? 0 ?>;
        let collegeRemaining = <?= $collegeRemaining ?? 0 ?>;
        let hsRemaining = <?= $hsRemaining ?? 0 ?>;

        const collegeBtn = document.querySelector(".hero-btn-primary");
        const hsBtn = document.querySelector(".hero-btn-outline");

        const collegeSlotText = collegeBtn.parentElement.querySelector(".small");
        const hsSlotText = hsBtn.parentElement.querySelector(".small");

        function updateStatus() {
            const now = new Date();

            // ---------------- College ----------------
            if (collegeDeadline && now > collegeDeadline || collegeRemaining <= 0) {
                collegeBtn.disabled = true;
                collegeBtn.classList.add("opacity-50");
                collegeBtn.title = "Registration closed";
                collegeBtn.textContent = collegeRemaining <= 0 ? "College Full" : "College Closed";
                if (collegeSlotText) collegeSlotText.textContent = collegeDeadline ?
                    `Deadline passed on ${collegeDeadline.toLocaleString()}` : '';
            }

            // ---------------- High School ----------------
            if (hsDeadline && now > hsDeadline || hsRemaining <= 0) {
                hsBtn.disabled = true;
                hsBtn.classList.add("opacity-50");
                hsBtn.title = "Registration closed";
                hsBtn.textContent = hsRemaining <= 0 ? "High School Full" : "High School Closed";
                if (hsSlotText) hsSlotText.textContent = hsDeadline ?
                    `Deadline passed on ${hsDeadline.toLocaleString()}` : '';
            }
        }

        // Initial check
        updateStatus();

        // Check every 5 seconds (adjust if needed)
        setInterval(updateStatus, 5000);
    </script>

</body>

</html>