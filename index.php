<?php
session_start();
require_once __DIR__ . '/config/config.php';
include 'check_deadline.php';

// Check limits for notes
$collegeLimitReached = false;
$hsLimitReached = false;
if (file_exists(__DIR__ . '/kceap_admin/deadline.json')) {
    $settings = json_decode(file_get_contents(__DIR__ . '/kceap_admin/deadline.json'), true);
    $collegeLimit = $settings['college']['limit'] ?? 0;
    $hsLimit = $settings['highschool']['limit'] ?? 0;
    if ($collegeLimit > 0) {
        $collegeCount = $conn->query("SELECT COUNT(*) AS total FROM college_schedule")->fetch_assoc()['total'];
        if ($collegeCount >= $collegeLimit) {
            $collegeLimitReached = true;
        }
    }
    if ($hsLimit > 0) {
        $hsCount = $conn->query("SELECT COUNT(*) AS total FROM highschool_schedule")->fetch_assoc()['total'];
        if ($hsCount >= $hsLimit) {
            $hsLimitReached = true;
        }
    }
}

// Check if deadlines are reached
$collegeActive = !$settings['college']['disabled'];
$hsActive = !$settings['highschool']['disabled'];

$collegeDeadlineNote = '';
$hsDeadlineNote = '';

if (!$collegeActive && !empty($settings['college']['deadline'])) {
    $collegeDeadlineNote = ' (Deadline passed on ' . date('F j, Y, g:i A', strtotime($settings['college']['deadline'])) . ')';
}

if (!$hsActive && !empty($settings['highschool']['deadline'])) {
    $hsDeadlineNote = ' (Deadline passed on ' . date('F j, Y, g:i A', strtotime($settings['highschool']['deadline'])) . ')';
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pap+Zc91Y4v0+k1HuTgX+Fg5Y+r4Yug3oXvF/HHGzGdF1w3LxOjLkT5y85XoZSmk5bHoYJYhZ9vT0xQF0qKXnQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./style/index.css">

    <title>KCEAP Scholarships - Fund Your Education</title>

    <style>
        .top-notice {
            font-size: 0.775rem;
            z-index: 1035;
            background-color: #ffc107;
            /* example yellow notice */
            color: #212529;
            text-align: center;
            padding: 2px 0;
        }

        .navbar {
            min-height: 64px;
            z-index: 1030;
        }

        /* Prevent nav links wrapping */
        .navbar-nav .nav-link {
            white-space: nowrap;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8rem 0 4rem;
            /* default top padding */
            position: relative;
            transition: padding-top 0.3s ease;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
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
    </style>

</head>

<body>




    <?php if (isset($_SESSION['applysuccess'])): ?>
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-success">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel">Application Submitted</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <span class="material-symbols-outlined fs-1 text-success mb-2">check_circle</span>
                        <?= $_SESSION['applysuccess'] ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        </script>
        <?php unset($_SESSION['applysuccess']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['applyfailed'])): ?>
        <div class="modal fade" id="failedModal" tabindex="-1" aria-labelledby="failedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="failedModalLabel">Application Failed</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <span class="material-symbols-outlined fs-1 text-danger mb-2">error</span>
                        <?= $_SESSION['applyfailed'] ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var failedModal = new bootstrap.Modal(document.getElementById('failedModal'));
                failedModal.show();
            });
        </script>
        <?php unset($_SESSION['applyfailed']); ?>
    <?php endif; ?>

    <!-- Top Notice -->
    <?php if ($collegeLimitReached || !$collegeActive || $hsLimitReached || !$hsActive): ?>
        <div class="top-notice alert alert-warning mb-0 text-center w-100 py-2 position-fixed top-0 start-0 zindex-1030">
            <?php if ($collegeLimitReached): ?>
                <strong>College:</strong> Applications have reached the limit.
            <?php elseif (!$collegeActive && $collegeDeadlineNote): ?>
                <strong>College:</strong> <?= $collegeDeadlineNote ?>
            <?php endif; ?>

            <?php if (($collegeLimitReached || !$collegeActive) && ($hsLimitReached || !$hsActive)): ?>
                <span class="mx-2">|</span>
            <?php endif; ?>

            <?php if ($hsLimitReached): ?>
                <strong>Highschool:</strong> Applications have reached the limit.
            <?php elseif (!$hsActive && $hsDeadlineNote): ?>
                <strong>Highschool:</strong> <?= $hsDeadlineNote ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm " style="top: <?php echo ($collegeLimitReached || !$collegeActive || $hsLimitReached || !$hsActive) ? '40px' : '0'; ?>;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="./img/logo.png" alt="KCEAP Logo" width="40" height="auto" class="d-inline-block align-text-top">
                <span class="brand-text ms-2">KCEAP Scholarships</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Key features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#eligibility">Eligibility</a></li>
                    <li class="nav-item">
                        <a class="nav-link <?php if (!$collegeActive || $collegeLimitReached) echo 'disabled'; ?>"
                            href="<?php echo ($collegeActive && !$collegeLimitReached) ? 'collegeapply.php' : '#'; ?>"
                            <?php if (!$collegeActive || $collegeLimitReached) echo 'aria-disabled="true"'; ?>>
                            <?php echo $collegeLimitReached ? 'College (Limit Reached)' : 'College'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if (!$hsActive || $hsLimitReached) echo 'disabled'; ?>"
                            href="<?php echo ($hsActive && !$hsLimitReached) ? 'highschoolapply.php' : '#'; ?>"
                            <?php if (!$hsActive || $hsLimitReached) echo 'aria-disabled="true"'; ?>>
                            <?php echo $hsLimitReached ? 'Highschool (Limit Reached)' : 'Highschool'; ?>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="announcement.php">Announcement</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="hero-title">Unlock Your Academic Potential with KCEAP Scholarships</h1>
                    <p class="hero-subtitle">Financial support for deserving students to achieve their educational
                        dreams</p>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="https://img.freepik.com/free-photo/scholarship-application-form-foundation-concept_53876-132191.jpg?t=st=1770550299~exp=1770553899~hmac=ffe2c731e3cc6531b6c9189af0740807fe1fee0c95bab982a7ee6012420a0fde"
                        alt="Students using computers for scholarship applications"
                        class="img-fluid rounded-4 shadow"
                        width="100%">
                </div>
            </div>
        </div>
    </section>



    <!-- Key Features Section -->
    <section class="py-5 bg-light" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Key Features of the Scholarship Information & Management System</h2>
                <p class="section-subtitle">Tools and automation to simplify your scholarship workflow</p>
            </div>
            <div class="row g-4">

                <!-- User Management -->
                <div class="col-md-4">
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
                                        class="material-symbols-outlined text-success me-2">check_circle</span>Schedules</li>
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
            <div class="text-center mb-5">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">A simple 4-step process to apply for a scholarship</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <span class="material-symbols-outlined text-primary fs-1">event</span>
                        <h5 class="mt-3">Set Schedule</h5>
                        <p class="small">Schedule your preferred date and time for taking the scholarship examination.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <span class="material-symbols-outlined text-primary fs-1">description</span>
                        <h5 class="mt-3">Submit Application</h5>
                        <p class="small">Fill out the scholarship form and upload required documents.</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <span class="material-symbols-outlined text-primary fs-1">hourglass_top</span>
                        <h5 class="mt-3">Wait for Review</h5>
                        <p class="small">Your application will be reviewed by the admin team.</p>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <span class="material-symbols-outlined text-primary fs-1">check_circle</span>
                        <h5 class="mt-3">Receive Decision</h5>
                        <p class="small">Get notified about your application status via email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Eligibility Criteria -->
    <section class="py-5" id="eligibility">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80"
                        alt="Diverse students studying" class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title">Eligibility Requirements</h2>
                    <p class="section-subtitle">Who can apply for KCEAP scholarships?</p>

                    <div class="eligibility-item d-flex mb-3">
                        <span class="material-symbols-outlined text-primary me-3">task_alt</span>
                        <div>
                            <p class="mb-0">A Filipino citizen and bonafide resident of Kabankalan City</p>
                        </div>
                    </div>

                    <div class="eligibility-item d-flex mb-3">
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

                    <div class="eligibility-item d-flex mb-3">
                        <span class="material-symbols-outlined text-primary me-3">task_alt</span>
                        <div>
                            <p class="mb-0">With parents or guardian having a combined gross monthly income of not more
                                than
                                Thirty-Thousand pesos(P30,000.00).</p>
                        </div>
                    </div>

                    <div class="eligibility-item d-flex">
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
    <section id="about-us" class="d-flex align-items-center" style="min-height: 70vh; background: linear-gradient(135deg, #0d6efd, #6610f2); color: white;">
        <div class="container">
            <div class="row align-items-center g-5">
                <!-- Image Section -->
                <div class="col-lg-5 text-center text-lg-start">
                    <img src="img/logo.png" alt="KCEAP Scholarship Info System"
                        class="img-fluid rounded-circle shadow-lg about-img" style="max-width: 300px;">
                </div>
                <!-- Text Section -->
                <div class="col-lg-7">
                    <h2 class="fw-bold mb-3">About <span style="color:#ffc107;">KCEAP Scholarships</span></h2>
                    <p class="lead mb-3 about-text">
                        The <strong>KCEAP Scholarship Information and Management System</strong> empowers students by providing a centralized platform to efficiently manage scholarship applications, deadlines, and schedules.
                        Since 2020, it has supported students in achieving their educational goals through financial aid, academic resources, and structured guidance.
                    </p>
                    <p class="about-text" style="color: rgba(255,255,255,0.8); font-size: 0.95rem;">
                        Our mission is to make scholarships more accessible, transparent, and manageable for every aspiring student, ensuring that no opportunity is missed.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a class="d-flex align-items-center mb-3 text-decoration-none" href="#">
                        <img src="img/logo.png" alt="KCEAP Logo" width="40" class="d-inline-block align-text-top me-2">
                        <span class="brand-text fs-5">kceap scholarships</span>
                    </a>
                    <p class="text-white-50">empowering students through financial support and educational opportunities since 2020.</p>

                    <!-- Developer photos -->
                    <p><strong>Researchers</strong></p>
                    <div class="social-icons d-flex mt-3">
                        <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#devModal" data-name="Matt Andrei Belano" data-role="Programmer" data-img="img/d11.jpeg">
                            <img src="img/d11.jpeg" alt="developer 1" class="rounded-circle" width="30" height="30">
                        </a>
                        <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#devModal" data-name="Rose Ann Bernabe" data-role="Documentation I" data-img="img/d2.jpeg">
                            <img src="img/d2.jpeg" alt="developer 2" class="rounded-circle" width="30" height="30">
                        </a>
                        <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#devModal" data-name="Christian Goza" data-role="Designer" data-img="img/d3.jpg">
                            <img src="img/d3.jpg" alt="developer 3" class="rounded-circle" width="30" height="30">
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#devModal" data-name="Mercy Ann Cabunag" data-role="Documentation II" data-img="img/d4.jpg">
                            <img src="img/d4.jpg" alt="developer 4" class="rounded-circle" width="30" height="30">
                        </a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="mb-3 text-uppercase">quick links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none">home</a></li>
                        <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none">key features</a></li>
                        <li class="mb-2"><a href="#eligibility" class="text-white-50 text-decoration-none">eligibility</a></li>
                        <li class="mb-2"><a href="announcement.php" class="text-white-50 text-decoration-none">announcement</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h6 class="mb-3 text-uppercase">contact us</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2 d-flex align-items-start">
                            <span class="material-symbols-outlined text-primary me-2">location_on</span>
                            old city hall, kabankalan city, negros occidental.
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
                    <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">

                    <!-- Enhanced Image Wrapper -->
                    <div class="profile-img-wrapper mb-4">
                        <img src=""
                            id="devModalImg"
                            alt="Developer Photo"
                            class="profile-img">
                    </div>

                    <h6 id="devModalRole" class="dev-role mb-2"></h6>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS (only once, after body content) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const devModal = document.getElementById('devModal');
        devModal.addEventListener('show.bs.modal', function(event) {
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

</body>

</html>