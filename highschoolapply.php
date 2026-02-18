<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High School Application Form</title>
    <link rel="stylesheet" href="./style/bootstrap.min.css">
    <link rel="stylesheet" href="./style/index.css">
    <link rel="icon" href="./img/logo.png" type="image/png">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }


        .form-card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 2rem;
        }

        @media (max-width: 576px) {
            .form-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container d-flex align-items-center">
            <!-- Brand + Back Button Inline -->
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="./img/logo.png" alt="Logo" width="30" class="d-inline-block align-text-top me-2">
                    High School Portal
                </a>
                <a href="index.php" class="btn btn-sm btn-light">← Back</a>
            </div>
        </div>
    </nav>


    <div class="container my-5">
        <!-- Session Messages -->
        <?php if (isset($_SESSION['highschool_apply_success'])): ?>
            <div id="alertMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['highschool_apply_success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['highschool_apply_success']); ?>
        <?php elseif (isset($_SESSION['highschool_apply_error'])): ?>
            <div id="alertMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['highschool_apply_error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['highschool_apply_error']); ?>
        <?php endif; ?>

        <script>
            setTimeout(() => {
                const alertBox = document.getElementById("alertMessage");
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 3000); // fade out after 3 seconds
        </script>

        <!-- Form Card -->
        <div class="form-card mx-auto">
            <div class="form-header d-flex justify-content-between align-items-center flex-wrap">
                <h2 class="mb-3 mb-md-0">Scholarship Application Form</h2>
            </div>

            <form action="highschool_apply_process.php" method="POST">
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
                        <select name="school" id="school" class="form-select" required>
                            <option value="" selected disabled>Select school...</option>
                            <option value="SOUTHLAND-HS">Southland College of Kabankalan City, Inc.</option>
                            <option value="KCC-HS">Kabankalan Catholic College</option>
                            <option value="FORTRESS">Fortress College</option>
                            <option value="FBC-HS">Fellowship Baptist College</option>
                            <option value="MCHS">Magballo Catholic High School, Inc.</option>
                            <option value="SNAA">Southern Negros Adventist Academy</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="strand" class="form-label">Strand</label>
                        <select class="form-select" name="strand" id="strand" required>
                            <option value="" selected disabled>Select strand...</option>
                            <option value="STEM">STEM</option>
                            <option value="ABM">ABM</option>
                            <option value="HUMSS">HUMSS</option>
                            <option value="GAS">GAS</option>
                            <option value="TVL">TVL</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="yearLevel" class="form-label">Year Level</label>
                        <select class="form-select" name="yearLevel" id="yearLevel" required>
                            <option value="" selected disabled>Select year level...</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="address" class="form-label">Barangay</label>
                        <select class="form-select" name="address" id="address" required>
                            <option disabled selected>Select barangay...</option>
                            <option value="BARANGAY 1">BARANGAY 1</option>
                            <option value="BARANGAY 2">BARANGAY 2</option>
                            <option value="BARANGAY 3">BARANGAY 3</option>
                            <option value="BARANGAY 4">BARANGAY 4</option>
                            <option value="BARANGAY 5">BARANGAY 5</option>
                            <option value="BARANGAY 6">BARANGAY 6</option>
                            <option value="BARANGAY 7">BARANGAY 7</option>
                            <option value="BARANGAY 8">BARANGAY 8</option>
                            <option value="BARANGAY 9">BARANGAY 9</option>
                            <option value="BANTAYAN">BARANGAY BANTAYAN</option>
                            <option value="BINICUIL">BARANGAY BINICUIL</option>
                            <option value="CAMANSI">BARANGAY CAMANSI</option>
                            <option value="CAMINGAWAN">BARANGAY CAMINGAWAN</option>
                            <option value="CAMUGAO">BARANGAY CAMUGAO</option>
                            <option value="CAROL-AN">BARANGAY CAROL-AN</option>
                            <option value="DAAN BANUA">BARANGAY DAAN BANUA</option>
                            <option value="HILAMONAN">BARANGAY HILAMONAN</option>
                            <option value="INAPOY">BARANGAY INAPOY</option>
                            <option value="LINAO">BARANGAY LINAO</option>
                            <option value="LOCOTAN">BARANGAY LOCOTAN</option>
                            <option value="MAGBALLO">BARANGAY MAGBALLO</option>
                            <option value="ORINGAO">BARANGAY ORINGAO</option>
                            <option value="ORONG">BARANGAY ORONG</option>
                            <option value="PINAGUINPINAN">BARANGAY PINAGUINPINAN</option>
                            <option value="SALONG">BARANGAY SALONG</option>
                            <option value="TABUGON">BARANGAY TABUGON</option>
                            <option value="TAGOC">BARANGAY TAGOC</option>
                            <option value="TAGOC">BARANGAY TAGUKON</option>
                            <option value="TALUBANGI">BARANGAY TALUBANGI</option>
                            <option value="TAMPALON">BARANGAY TAMPALON</option>
                            <option value="TAN-AWAN">BARANGAY TAN-AWAN</option>
                            <option value="TAPI">BARANGAY TAPI</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" maxlength="11"
                            required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="emailAddress" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="emailAddress" name="emailAddress" required>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Auto Uppercase for text inputs -->
    <script>
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(input => input.addEventListener('input', () => input.value = input.value.toUpperCase()));
    </script>

    <script src="./script/bootstrap.bundle.min.js"></script>
</body>

</html>