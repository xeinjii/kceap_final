<?php
session_start();
include './config/config.php'; // Include your DB connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Apply for scholarships to fund your education with KCEAP">
    <link rel="icon" href="./img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./style/index.css">
    <link rel="stylesheet" href="./style/bootstrap.min.css">

    <title>KCEAP Scholarships - Fund Your Education</title>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="./img/logo.png" alt="KCEAP Logo" width="40" height="auto"
                    class="d-inline-block align-text-top">
                <span class="brand-text ms-2">KCEAP Scholarships</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="material-symbols-outlined text-white">menu</span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#scholarships">Scholarships</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Key features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#eligibility">Eligibility</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light" href="#apply-now" data-bs-toggle="modal"
                            data-bs-target="#applyModal">
                            <span class="material-symbols-outlined align-middle me-1">edit_square</span>
                            Apply Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Unlock Your Academic Potential with KCEAP Scholarships</h1>
                    <p class="hero-subtitle">Financial support for deserving students to achieve their educational
                        dreams</p>
                    <div class="d-flex gap-3 mt-4">
                        <!-- Button triggers modal -->
                        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                            data-bs-target="#applyModal">
                            <span class="material-symbols-outlined align-middle me-1">school</span>
                            Apply Now
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                        alt="Students celebrating graduation" class="img-fluid rounded-4 shadow">
                </div>
            </div>
        </div>
    </section>

<!-- Apply Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="applyForm" action="apply_process.php" method="POST">
        <div class="modal-header sticky-top bg-white">
          <h5 class="modal-title" id="applyModalLabel">Scholarship Application Form</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container-fluid px-2">
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
              </div>
              <div class="col-12 col-md-6">
                <label for="middleName" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middleName" name="middleName">
              </div>
              <div class="col-12 col-md-6">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
              </div>
              <div class="col-12 col-md-6">
                <label for="school" class="form-label">School</label>
                <input type="text" class="form-control" id="school" name="school" required>
              </div>
               <div class="col-12 col-md-6">
                <label for="level" class="form-label">School level</label>
                <select class="form-select" name="level" id="level" required>  
                    <option value="" selected disabled>Select level</option>
                    <option value="shs">Senior High School level</option>
                    <option value="college">College level</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
              </div>
              <div class="col-12 col-md-6">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required pattern="[0-9]{11}" maxlength="11">
                <div class="form-text">Enter digits only, 11 characters</div>
              </div>
              <div class="col-12 col-md-6">
                <label for="emailAddress" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" required>
              </div>

            </div>
          </div>
        </div>
        <div class="modal-footer sticky-bottom bg-white">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Application</button>
        </div>
      </form>
    </div>
  </div>
</div>


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
                                    Role-based dashboards</li>
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span> Profile
                                    verification</li>
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
                                        class="material-symbols-outlined text-success me-2">check_circle</span> Status
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
                                    Real-time dashboards</li>
                                <li class="mb-2"><span
                                        class="material-symbols-outlined text-success me-2">check_circle</span> Export
                                    to Excel/PDF</li>
                                <li><span class="material-symbols-outlined text-success me-2">check_circle</span> Visual
                                    performance graphs</li>
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
                        <p class="small">Get notified about your application status via system notification or email.
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


    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white" id="apply-now">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-3">Ready to Transform Your Future?</h2>
                    <p class="lead mb-4">Join thousands of students who have achieved their academic dreams with KCEAP
                        scholarships.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="#" class="btn btn-light btn-lg px-4" data-bs-toggle="modal"
                            data-bs-target="#applyModal">
                            <span class="material-symbols-outlined align-middle me-2">edit_square</span>
                            Apply Now
                        </a>
                    </div>
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
                        <img src="./img/logo.png" alt="KCEAP Logo" width="40" height="auto"
                            class="d-inline-block align-text-top me-2">
                        <span class="brand-text fs-5">KCEAP Scholarships</span>
                    </a>
                    <p>Empowering students through financial support and educational opportunities since 2003.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="#scholarships"
                                class="text-white-50 text-decoration-none">Scholarships</a></li>
                        <li class="mb-2"><a href="#eligibility" class="text-white-50 text-decoration-none">Key
                                features</a></li>
                        <li class="mb-2"><a href="#features" class="text-white-50 text-decoration-none">Eligibility</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5 class="mb-3">Contact Us</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2 d-flex align-items-start">
                            <span class="material-symbols-outlined text-primary me-2">location_on</span>
                            123 Education Ave, Suite 200<br>Academic City, AC 12345
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <span class="material-symbols-outlined text-primary me-2">mail</span>
                            scholarships@kceap.org
                        </li>
                        <li class="d-flex align-items-center">
                            <span class="material-symbols-outlined text-primary me-2">call</span>
                            (555) 123-4567
                        </li>
                    </ul>
                </div>
                <!-- optional -->
                <div class="col-lg-3 col-md-4">
                    <h5 class="mb-3">Newsletter</h5>
                    <p class="text-white-50">Subscribe to receive updates on new scholarship opportunities.</p>
                    <form class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email">
                            <button class="btn btn-primary" type="submit">
                                <span class="material-symbols-outlined">send</span>
                            </button>
                        </div>
                    </form>
                    <small class="text-white-50">We respect your privacy.</small>
                </div>

            </div>

            <hr class="my-4 border-secondary">

            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; 2023 KCEAP Scholarships. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="./script/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>

</html>