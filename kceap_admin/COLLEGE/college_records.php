<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}
$admin_id = $_SESSION['admin_id'];
// Fetch all college accounts with active status only
$sql = "SELECT * FROM college_account WHERE status = 'active' ORDER BY applicant_id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>College Scholars Records - KCEAP</title>
    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .brand-text {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .table th,
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            max-width: 200px;
            font-size: 0.85rem;
        }

        .table td:nth-child(8),
        .table td:nth-child(10) {
            max-width: 200px;
        }

        .table thead {
            background-color: #0d6efd;
            color: white;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .navbar {
            background-color: #0d6efd;
        }

        .navbar .nav-link,
        .navbar .navbar-brand {
            color: white;
        }

        .navbar .nav-link:hover {
            color: #ffc107;
        }

        footer {
            background-color: #212529;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="navbar-brand d-flex align-items-center">
                <img src="../../img/logo.png" alt="KCEAP Logo" width="40" class="me-2">
                <span class="brand-text">KCEAP Scholarships</span>
            </div>
            <a href="../../kceap_admin/collegepage.php" class="btn btn-outline-light btn-sm">Back to Mainpage</a>
        </div>
    </nav>

    <?php if (isset($_SESSION['message'])): ?>
        <div id="autoAlert" class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show"
            role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <script>
            // Auto hide alert after 2 seconds
            setTimeout(() => {
                const alertElement = document.getElementById('autoAlert');
                if (alertElement) {
                    // Bootstrap 5 fade out
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, 2000);
        </script>

        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <section class="py-4">
        <div class="container">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-0">
                            <span class="material-symbols-outlined align-middle me-1 text-primary">school</span>
                            College Scholars Records
                        </h3>
                        <small class="text-muted">School Year:
                            <?php echo date('Y') . ' - ' . (date('Y') + 1); ?></small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#addRecordModal">
                            <span class="material-symbols-outlined me-1" style="font-size: 1.2rem;">add</span>
                            Add Records
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#resetModal">
                            Reset All to Pending
                        </button>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-striped align-middle" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Email Address</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php $i = 1;
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td title="<?= htmlspecialchars($row['first_name']) ?>">
                                            <?= htmlspecialchars($row['first_name']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['middle_name']) ?>">
                                            <?= htmlspecialchars($row['middle_name']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['last_name']) ?>">
                                            <?= htmlspecialchars($row['last_name']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['school']) ?>">
                                            <?= htmlspecialchars($row['school']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['course']) ?>">
                                            <?= htmlspecialchars($row['course']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['year_level']) ?>">
                                            <?= htmlspecialchars($row['year_level']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['address']) ?>">
                                            <?= htmlspecialchars($row['address']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['phone_number']) ?>">
                                            <?= htmlspecialchars($row['phone_number']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['email']) ?>">
                                            <?= htmlspecialchars($row['email']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['semester']) ?>">
                                            <?= htmlspecialchars($row['semester']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning me-1 edit-btn" title="Edit"
                                                data-id="<?= $row['applicant_id'] ?>"
                                                data-first="<?= htmlspecialchars($row['first_name']) ?>"
                                                data-middle="<?= htmlspecialchars($row['middle_name']) ?>"
                                                data-last="<?= htmlspecialchars($row['last_name']) ?>"
                                                data-school="<?= htmlspecialchars($row['school']) ?>"
                                                data-course="<?= htmlspecialchars($row['course']) ?>"
                                                data-year="<?= htmlspecialchars($row['year_level']) ?>"
                                                data-address="<?= htmlspecialchars($row['address']) ?>"
                                                data-phone="<?= htmlspecialchars($row['phone_number']) ?>"
                                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                                data-status="<?= htmlspecialchars($row['status']) ?>"
                                                data-semester="<?= htmlspecialchars($row['semester']) ?>">
                                                <span class="material-symbols-outlined" style="font-size: 1rem;">edit</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete"
                                                data-id="<?= $row['applicant_id'] ?>"
                                                data-first="<?= htmlspecialchars($row['first_name']) ?>" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal">
                                                <span class="material-symbols-outlined" style="font-size: 1rem;">delete</span>
                                            </button>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="text-center text-muted">No college records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>

    <!-- Add Record Modal -->
    <div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addRecordModalLabel">Add New Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="save_records.php" method="POST">
                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- First Name -->
                            <div class="col-md-4">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>

                            <!-- Middle Name -->
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control">
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>

                            <!-- School Dropdown -->
                            <div class="col-md-6">
                                <label class="form-label">School</label>
                                <select name="school" class="form-select" required>
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
                                    <option value="ABE-BACOLOD">ABE International Business College – Bacolod </option>
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

                            <!-- Course -->
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <input type="text" name="course" class="form-control">
                            </div>

                            <!-- Year Level Dropdown -->
                            <div class="col-md-4">
                                <label class="form-label">Year Level</label>
                                <select name="year_level" class="form-select" required>
                                    <option value="" selected disabled>Select Year Level</option>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>

                            <!-- Semester Dropdown -->
                            <div class="col-md-4">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-select" required>
                                    <option value="" selected disabled>Select Semester</option>
                                    <option value="1st semester">1st Semester</option>
                                    <option value="2nd semester">2nd Semester</option>
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="incomplete">Incomplete</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>

                            <!-- Address Dropdown -->
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <select name="address" class="form-select" required>
                                    <option value="" selected disabled>Select Address</option>
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


                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number" id="add-phone"
                                    pattern="[0-9]{11}" maxlength="11" placeholder="Enter 11-digit number" required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="form-text">Phone number must be exactly 11 digits.</div>
                            </div>


                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Record</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form id="editForm" method="POST" action="action_process.php">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Edit Scholar Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">

                        <!-- First, Middle, Last Name -->
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="edit-first" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="edit-middle">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="edit-last" required>
                        </div>

                        <!-- School Dropdown -->
                        <div class="col-md-6">
                            <label class="form-label">School</label>
                            <select name="school" id="edit-school" class="form-select" required>
                                <option value="" disabled>Select School</option>
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
                                <option value="ABE-BACOLOD">ABE International Business College – Bacolod </option>
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

                        <!-- Course -->
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" name="course" id="edit-course">
                        </div>

                        <!-- Year Level -->
                        <div class="col-md-4">
                            <label class="form-label">Year Level</label>
                            <select name="year_level" id="edit-year" class="form-select" required>
                                <option value="" disabled>Select Year Level</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>

                        <!-- Semester -->
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select name="semester" id="edit-semester" class="form-select" required>
                                <option value="" disabled>Select Semester</option>
                                <option value="1st semester">1st Semester</option>
                                <option value="2nd semester">2nd Semester</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit-status" class="form-select" required>
                                <option value="" disabled>Select Status</option>
                                <option value="active">Active</option>
                                <option value="incomplete">Incomplete</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <select name="address" id="edit-address" class="form-select" required>
                                <option value="" disabled>Select Address</option>
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
                                <option value="TAGUKON">BARANGAY TAGUKON</option>
                                <option value="TALUBANGI">BARANGAY TALUBANGI</option>
                                <option value="TAMPALON">BARANGAY TAMPALON</option>
                                <option value="TAN-AWAN">BARANGAY TAN-AWAN</option>
                                <option value="TAPI">BARANGAY TAPI</option>
                            </select>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" id="edit-phone"
                                pattern="[0-9]{11}" maxlength="11" placeholder="Enter 11-digit number" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <div class="form-text">Phone number must be exactly 11 digits.</div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="edit-email">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="deleteForm" method="POST" action="action_process.php" class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the record for <strong id="deleteName"></strong>? This action
                        cannot be undone.</p>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>



    <!-- Reset Confirmation Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="reset_statuses.php" class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="resetModalLabel">Confirm Reset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reset <strong>all applicant statuses</strong> to
                        <strong>pending</strong>? This action cannot be undone.
                    </p>
                    <div class="mt-3">
                        <label class="form-label">Set upload deadline date (optional)</label>
                        <input type="date" name="upload_deadline_date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                        <div class="form-text">Admin may set a deadline date; time defaults to 23:59.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Reset All</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-btn');
            const editModalElement = document.getElementById('editModal');
            const editModal = new bootstrap.Modal(editModalElement);

            editButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Populate form fields with data from button attributes
                    document.getElementById('edit-id').value = this.dataset.id || '';
                    document.getElementById('edit-first').value = this.dataset.first || '';
                    document.getElementById('edit-middle').value = this.dataset.middle || '';
                    document.getElementById('edit-last').value = this.dataset.last || '';
                    document.getElementById('edit-school').value = this.dataset.school || '';
                    document.getElementById('edit-course').value = this.dataset.course || '';
                    document.getElementById('edit-year').value = this.dataset.year || '';
                    document.getElementById('edit-address').value = this.dataset.address || '';
                    document.getElementById('edit-phone').value = this.dataset.phone || '';
                    document.getElementById('edit-email').value = this.dataset.email || '';
                    document.getElementById('edit-status').value = this.dataset.status || '';
                    document.getElementById('edit-semester').value = this.dataset.semester || '';

                    // Show the modal
                    editModal.show();
                });
            });
        });
    </script>

    <script>
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const firstName = btn.dataset.first;

                document.getElementById('delete-id').value = id;
                document.getElementById('deleteName').textContent = firstName;
                deleteModal.show();
            });
        });
    </script>

</body>

</html>