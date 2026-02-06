<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Application Form</title>
    <link rel="stylesheet" href="./style/bootstrap.min.css">
    <link rel="stylesheet" href="./style/index.css">
    <link rel="icon" href="./img/logo.png" type="image/png">
</head>

<body>
    <div class="container mt-5">

        <?php
        session_start();
        if (isset($_SESSION['college_apply_success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['college_apply_success'] . '</div>';
            unset($_SESSION['college_apply_success']);
        }
        if (isset($_SESSION['college_apply_error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['college_apply_error'] . '</div>';
            unset($_SESSION['college_apply_error']);
        }
        ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>College Scholarship Application Form</h2>
            <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
        <form action="college_apply_process.php" method="POST">
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
                        <option disabled selected>Select your school...</option>
                        <!-- Add school options here (copy from index.php) -->
                        <!-- Carlos Hilado Memorial State University (CHMSU) -->
                        <option value="CHMSU-TALISAY">CHMSU – Talisay (Main Campus)</option>
                        <option value="CHMSU-ALIJIS">CHMSU – Alijis Campus</option>
                        <option value="CHMSU-FT">CHMSU – Fortune Towne Campus</option>
                        <option value="CHMSU-BINALBAGAN">CHMSU – Binalbagan Campus</option>

                        <!-- Central Philippines State University (CPSU) -->
                        <option value="CPSU-KABANKALAN">CPSU – Kabankalan (Main Campus)</option>
                        <option value="CPSU-CAUAYAN">CPSU – Cauayan Campus</option>
                        <option value="CPSU-SIPALAY">CPSU – Sipalay Campus</option>
                        <option value="CPSU-ILOG">CPSU – Ilog Campus</option>
                        <option value="CPSU-HINIGARAN">CPSU – Hinigaran Campus</option>
                        <option value="CPSU-MOISES">CPSU – Moises Padilla Campus</option>
                        <option value="CPSU-SAN-CARLOS">CPSU – San Carlos Campus</option>
                        <option value="CPSU-VALLADOLID">CPSU – Valladolid Campus</option>
                        <option value="CPSU-LA-CASTELLANA">CPSU – La Castellana Campus</option>
                        <option value="CPSU-CANDONI">CPSU – Candoni Campus</option>

                        <!-- State University of Northern Negros (SUNN) -->
                        <option value="SUNN-SAGAY">SUNN – Sagay (Main Campus)</option>
                        <option value="SUNN-CADIZ">SUNN – Cadiz Campus</option>

                        <!-- West Visayas State University (WVSU) -->
                        <option value="WVSU-HIMAMAYLAN">WVSU – Himamaylan Campus</option>

                        <!-- Negros Oriental State University (NORSU) -->
                        <option value="NORSU-DUMAGUETE">NORSU – Dumaguete (Main Campus I & II)</option>
                        <option value="NORSU-BAIS1">NORSU – Bais Campus I</option>
                        <option value="NORSU-BAIS2">NORSU – Bais Campus II</option>
                        <option value="NORSU-GUIHULNGAN">NORSU – Guihulngan Campus</option>
                        <option value="NORSU-MABINAY">NORSU – Mabinay Campus</option>
                        <option value="NORSU-SIATON">NORSU – Siaton Campus</option>
                        <option value="NORSU-BAYAWAN">NORSU – Bayawan-Sta. Catalina Campus</option>
                        <option value="NORSU-PAMPLONA">NORSU – Pamplona Campus</option>

                        <!-- Private Colleges & Universities -->
                        <option value="USLS">University of St. La Salle – Bacolod</option>
                        <option value="UNO-R">University of Negros Occidental – Recoletos</option>
                        <option value="CSA-B">Colegio San Agustin – Bacolod</option>
                        <option value="LCC-BACOLOD">La Consolacion College – Bacolod</option>
                        <option value="STI-WNU">STI West Negros University</option>
                        <option value="RIVERSIDE">Riverside College, Inc.</option>
                        <option value="VMA">VMA Global College</option>
                        <option value="FBC">Fellowship Baptist College – Kabankalan</option>
                        <option value="KCC">Kabankalan Catholic College</option>
                        <option value="BCC">Bacolod City College</option>
                        <option value="BAGO-CC">Bago City College</option>
                        <option value="BCC-BINAL">Binalbagan Catholic College</option>
                        <option value="MCCE">Mount Carmel College of Escalante</option>
                        <option value="SLC">Southland College</option>
                        <option value="JBLCF">John B. Lacson Colleges Foundation – Bacolod</option>
                        <option value="CPAC">Central Philippine Adventist College</option>
                        <option value="LCCC">La Carlota City College</option>
                        <option value="AMA-BACOLOD">AMA Computer College – Bacolod</option>
                        <option value="ABE-BACOLOD">ABE International Business College – Bacolod
                        </option>
                        <option value="ACA-BACOLOD">Asian College of Aeronautics – Bacolod</option>
                        <option value="OLM-BACOLOD">Our Lady of Mercy College – Bacolod</option>
                        <option value="FAST">FAST Aviation Academy</option>
                        <option value="LASALTECH">LaSalTech</option>
                        <option value="SHS-BACOLOD">Sacred Heart Seminary – Bacolod</option>
                        <option value="NOLITC">Negros Occidental Language & IT Center</option>
                        <option value="CBBC">Convention Baptist Bible College</option>
                        <option value="SU">Silliman University – Dumaguete</option>
                        <option value="SPUD">St. Paul University – Dumaguete</option>
                        <option value="FU">Foundation University – Dumaguete</option>
                        <option value="COSCA">Colegio de Santa Catalina de Alejandria</option>
                        <option value="MAXINO">Maxino College – Dumaguete</option>
                        <option value="MDC">Metro Dumaguete College</option>
                        <option value="LCC-BAIS">La Consolacion College – Bais</option>
                        <option value="VC">Villaflores College</option>
                        <option value="DIAZ">Diaz College</option>
                        <option value="SFC-GUI">Saint Francis College – Guihulngan</option>
                        <option value="SJC-CAN">Saint Joseph College – Canlaon</option>
                        <option value="NMCF">Negros Maritime College Foundation</option>
                        <option value="NCI">Negros College Inc. – Ayungon</option>
                        <option value="BC">Bayawan College</option>
                        <option value="PTC">Presbyterian Theological College</option>
                        <option value="STC-BAYAWAN">Southern Tech College – Bayawan</option>
                        <option value="AMA-DUMAGUETE">AMA Computer College – Dumaguete</option>
                        <option value="ACSAT-DUMAGUETE">Asian College of Science & Tech – Dumaguete
                        </option>
                        <option value="STI-DUMAGUETE">STI College – Dumaguete</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="course" class="form-label">Course</label>
                    <input type="text" class="form-control" id="course" name="course" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="yearLevel" class="form-label">Year Level</label>
                    <select class="form-select" name="yearLevel" id="yearLevel" required>
                        <option value="" selected disabled>Select year level...</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="address" class="form-label">Address</label>
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
                        inputmode="numeric" pattern="\d{11}" title="Phone number must be 11 digits" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                </div>
                <div class="col-12 col-md-6">
                    <label for="emailAddress" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="emailAddress" name="emailAddress" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</body>

</html>