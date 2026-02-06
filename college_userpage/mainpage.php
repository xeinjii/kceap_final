<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Check if welcome message should be shown (only on first visit after login)
$show_welcome = $_SESSION['show_welcome'] ?? false;
if ($show_welcome) {
  unset($_SESSION['show_welcome']); // Remove flag so it doesn't show again
}

// Get user email for display
$userEmail = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KCEAP - Dashboard</title>
  <link rel="icon" href="../img/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />


  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0d6efd, #0dcaf0);
      min-height: 100vh;
      overflow-x: hidden;
      margin: 0;
      padding-bottom: 70px;
      /* reserve space for footer */
    }

    /* Navbar */
    .navbar {
      background: #0d6efd !important;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }

    /* Dashboard card */
    .dashboard-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 3rem 1rem;
    }

    .dashboard-card {
      background: #fff;
      border-radius: 1.5rem;
      box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
      padding: 2.5rem;
      max-width: 650px;
      width: 100%;
      text-align: center;
    }

    .btn-action {
      font-weight: 500;
      border-radius: 0.6rem;
      padding: 0.8rem 1.4rem;
      font-size: 1rem;
      transition: transform 0.2s ease;
    }

    .btn-action:hover {
      transform: scale(1.07);
    }

    .btn-apply {
      background: #0d6efd;
      color: #fff;
    }

    .btn-renew {
      background: #198754;
      color: #fff;
    }

    /* Footer */
    .footer-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: #fff;
      border-top: 1px solid #ddd;
      display: flex;
      justify-content: space-around;
      align-items: center;
      padding: 0.5rem 0;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
      z-index: 100;
      flex-wrap: wrap;
      /* wrap icons if needed on small screens */
    }

    .footer-nav a {
      color: #0d6efd;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      font-size: 0.85rem;
      padding: 0.2rem 0.5rem;
    }

    .footer-nav a .material-symbols-outlined {
      font-size: 1.5rem;
      margin-bottom: 2px;
    }

    /* Welcome Message Animation */
    .welcome-toast {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: linear-gradient(135deg, #0d6efd, #0dcaf0);
      color: white;
      padding: 2.5rem 3rem;
      border-radius: 1.5rem;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
      z-index: 9999;
      text-align: center;
      font-size: 1.5rem;
      font-weight: 600;
      animation: slideInUp 0.6s ease-out, slideOutDown 0.6s ease-in 4.4s forwards;
      max-width: 400px;
      width: 90%;
    }

    .welcome-toast .user-name {
      font-size: 2rem;
      font-weight: 700;
      color: #fff;
      margin-top: 0.5rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translate(-50%, 50px);
      }
      to {
        opacity: 1;
        transform: translate(-50%, -50%);
      }
    }

    @keyframes slideOutDown {
      from {
        opacity: 1;
        transform: translate(-50%, -50%);
      }
      to {
        opacity: 0;
        transform: translate(-50%, 50px);
      }
    }
  </style>
</head>

<body>

  <!-- Welcome Message Toast (shown only once after login) -->
  <?php if ($show_welcome): ?>
  <div class="welcome-toast" id="welcomeToast">
    <div>Welcome!👋</div>
    <div class="user-name"><?= htmlspecialchars($_SESSION['first_name'] ?? 'Guest') ?></div>
  </div>
  <?php endif; ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">

      <!-- Logo / Brand -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="../img/logo.png" alt="KCEAP Logo" width="42" class="me-2">
        <span class="fw-bold">KCEAP</span>
      </a>

      <div class="d-flex align-items-center">

       

        <!-- User Profile Menu -->
        <div class="dropdown">
          <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center" type="button" id="userDropdown"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="material-symbols-outlined me-1">account_circle</span>
          </button>

          <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-item-text fw-semibold">
                <?= htmlspecialchars($userEmail) ?>
              </span></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
        </div>

      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="dashboard-container">
    <div class="dashboard-card">
      <h2 class="mb-4" style="color: #0d6efd; font-weight: 700;">Welcome to KCEAP</h2>
      
      <!-- User Info Section -->
      <div class="card mb-4 border-light shadow-sm">
        <div class="card-body">
          <h5 class="card-title text-start mb-3">Your Profile</h5>
          <div class="row text-start">
            <div class="col-md-6 mb-3">
              <small class="text-muted">Full Name</small>
              <p class="mb-0 fw-semibold"><?= htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['middle_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted">Email</small>
              <p class="mb-0 fw-semibold"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted">Course</small>
              <p class="mb-0 fw-semibold"><?= htmlspecialchars($_SESSION['course'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted">Year Level</small>
              <p class="mb-0 fw-semibold"><?= htmlspecialchars($_SESSION['year_level'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted">Phone Number</small>
              <p class="mb-0 fw-semibold"><?= htmlspecialchars($_SESSION['phone_number'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted">Status</small>
              <p class="mb-0"><span class="badge bg-info"><?= htmlspecialchars($_SESSION['status'] ?? 'Active') ?></span></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions Section -->
      <div class="mb-4">
        <h5 class="text-start mb-3">Quick Actions</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <a href="profile.php" class="btn btn-action btn-outline-primary w-100">
              <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle;">person</span>
              View Profile
            </a>
          </div>
          <div class="col-md-6">
            <a href="schedule.php" class="btn btn-action btn-outline-primary w-100">
              <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle;">calendar_month</span>
              View Schedule
            </a>
          </div>
          <div class="col-md-6">
            <a href="upload_docs.php" class="btn btn-action btn-apply w-100">
              <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle;">upload</span>
              Upload Documents
            </a>
          </div>
          <div class="col-md-6">
            <a href="renew.php" class="btn btn-action btn-renew w-100">
              <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle;">refresh</span>
              Renew Application
            </a>
          </div>
        </div>
      </div>

      <!-- Important Notice -->
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <span class="material-symbols-outlined" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.5rem;">info</span>
        <strong>Important!</strong> Make sure to review and update your documents regularly to keep your application current.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <!-- Information Cards -->
      <div class="row g-3 mt-2">
        <div class="col-md-6">
          <div class="card text-center border-light shadow-sm">
            <div class="card-body">
              <span class="material-symbols-outlined" style="font-size: 2.5rem; color: #0d6efd;">assignment</span>
              <h6 class="card-title mt-3">Applications</h6>
              <p class="card-text text-muted small">Manage your applications</p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card text-center border-light shadow-sm">
            <div class="card-body">
              <span class="material-symbols-outlined" style="font-size: 2.5rem; color: #198754;">description</span>
              <h6 class="card-title mt-3">Documents</h6>
              <p class="card-text text-muted small">Upload your documents</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer navigation for mobile -->
  <div class="footer-nav">
    <a href="profile.php">
      <span class="material-symbols-outlined">account_circle</span>
      Profile
    </a>
    <a href="schedule.php">
      <span class="material-symbols-outlined">calendar_month</span>
      Schedule
    </a>
    <a href="upload_docs.php">
      <span class="material-symbols-outlined">upload</span>
      Upload
    </a>
    <a href="renew.php">
      <span class="material-symbols-outlined">refresh</span>
      Renew
    </a>
  </div>


  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to logout?
        </div>
        <div class="modal-footer">
          <form action="logout.php" method="post">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Welcome Toast Auto-Dismiss
    document.addEventListener('DOMContentLoaded', function() {
      const welcomeToast = document.getElementById('welcomeToast');
      if (welcomeToast) {
        setTimeout(function() {
          welcomeToast.style.display = 'none';
        }, 5000);
      }
    });

    // Attach modal to logout link
    const logoutLink = document.querySelectorAll('a[href="logout.php"]');
    logoutLink.forEach(link => {
      link.setAttribute('data-bs-toggle', 'modal');
      link.setAttribute('data-bs-target', '#logoutModal');
      link.setAttribute('href', '#');
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>