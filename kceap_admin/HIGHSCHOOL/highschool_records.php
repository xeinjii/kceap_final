<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch active high school records only
$sql = "SELECT * FROM highschool_account WHERE status = 'active' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>High School Scholars Records - KCEAP</title>
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

            <a href="../../kceap_admin/highschoolpage.php" class="btn btn-outline-light btn-sm">Back to Mainpage</a>
        </div>
    </nav>
    <?php if (isset($_SESSION['message'])): ?>
        <div id="sessionAlert" class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show"
            role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        // Optionally, unset here or after JS hides it
        unset($_SESSION['message'], $_SESSION['message_type']);
    endif; ?>

    <script>
        // Auto-dismiss session alert after 2 seconds
        const sessionAlert = document.getElementById('sessionAlert');
        if (sessionAlert) {
            setTimeout(() => {
                const alert = bootstrap.Alert.getOrCreateInstance(sessionAlert);
                alert.close();
            }, 2000); // 2000ms = 2 seconds
        }

    </script>

    <section class="py-4">
        <div class="container">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-0">
                            <span class="material-symbols-outlined align-middle me-1 text-primary">school</span>
                            High School Scholars Records
                        </h3>
                        <small class="text-muted">School Year:
                            <?php echo date('Y') . ' - ' . (date('Y') + 1); ?></small>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- Add New Record Button -->
                        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#addHighSchoolModal">
                            <span class="material-symbols-outlined me-1" style="font-size: 1.2rem;">add</span>
                            Add New Record
                        </button>

                        <!-- Trigger Reset Modal -->
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#resetModal">
                            Reset All to Pending
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead style="position: sticky; top: 0; z-index: 2;">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>School</th>
                                <th>Year Level</th>
                                <th>Strand</th>
                                <th>Semester</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Email Address</th>
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
                                        <td title="<?= htmlspecialchars($row['year_level']) ?>">
                                            <?= htmlspecialchars($row['year_level']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['strand']) ?>">
                                            <?= htmlspecialchars($row['strand']) ?>
                                        </td>
                                        <td title="<?= htmlspecialchars($row['semester'] ?? 'N/A') ?>">
                                            <?= htmlspecialchars($row['semester'] ?? 'N/A') ?>
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
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning me-1 edit-btn" title="Edit"
                                                data-id="<?= $row['id'] ?>"
                                                data-first="<?= htmlspecialchars($row['first_name']) ?>"
                                                data-middle="<?= htmlspecialchars($row['middle_name']) ?>"
                                                data-last="<?= htmlspecialchars($row['last_name']) ?>"
                                                data-school="<?= htmlspecialchars($row['school']) ?>"
                                                data-year="<?= htmlspecialchars($row['year_level']) ?>"
                                                data-strand="<?= htmlspecialchars($row['strand']) ?>"
                                                data-semester="<?= htmlspecialchars($row['semester'] ?? '') ?>"
                                                data-address="<?= htmlspecialchars($row['address']) ?>"
                                                data-phone="<?= htmlspecialchars($row['phone_number']) ?>"
                                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                                data-status="<?= htmlspecialchars($row['status']) ?>">
                                                <span class="material-symbols-outlined" style="font-size: 1rem;">edit</span>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-btn" title="Delete"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>">
                                                <span class="material-symbols-outlined" style="font-size: 1rem;">delete</span>
                                            </button>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No high school records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Add High School Record Modal -->
    <div class="modal fade" id="addHighSchoolModal" tabindex="-1" aria-labelledby="addHighSchoolModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="save_highschool.php" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addHighSchoolModalLabel">Add New Scholar Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="action" value="add">

                    <!-- Names -->
                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>

                    <!-- School -->
                    <div class="col-md-6">
                        <label class="form-label">School</label>
                        <select class="form-select" name="school" required>
                            <option value="" selected disabled>Select School</option>
                            <option value="SOUTHLAND-HS">Southland College of Kabankalan City, Inc.</option>
                            <option value="KCC-HS">Kabankalan Catholic College</option>
                            <option value="FORTRESS">Fortress College</option>
                            <option value="FBC-HS">Fellowship Baptist College</option>
                            <option value="MCHS">Magballo Catholic High School, Inc.</option>
                            <option value="SNAA">Southern Negros Adventist Academy</option>
                        </select>
                    </div>

                    <!-- Year Level -->
                    <div class="col-md-3">
                        <label class="form-label">Year Level</label>
                        <select class="form-select" name="year_level" required>
                            <option value="" selected disabled>Select Year Level</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>

                    <!-- Strand -->
                    <div class="col-md-3">
                        <label class="form-label">Strand</label>
                        <select class="form-select" name="strand" required>
                            <option value="" selected disabled>Select Strand</option>
                            <option value="STEM">STEM</option>
                            <option value="ABM">ABM</option>
                            <option value="HUMSS">HUMSS</option>
                            <option value="GAS">GAS</option>
                            <option value="TVL">TVL</option>
                        </select>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select class="form-select" name="semester" required>
                            <option value="" selected disabled>Select Semester</option>
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="col-md-5">
                        <label class="form-label">Address</label>
                        <select class="form-select" name="address" required>
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
                            <option value="TAGUKON">BARANGAY TAGUKON</option>
                            <option value="TALUBANGI">BARANGAY TALUBANGI</option>
                            <option value="TAMPALON">BARANGAY TAMPALON</option>
                            <option value="TAN-AWAN">BARANGAY TAN-AWAN</option>
                            <option value="TAPI">BARANGAY TAPI</option>
                        </select>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-4">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone_number" pattern="[0-9]{11}" maxlength="11"
                            placeholder="Enter 11-digit number" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <div class="form-text">Phone number must be exactly 11 digits.</div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="" selected disabled>Select Status</option>
                            <option value="active">Active</option>
                            <option value="incomplete">Incomplete</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Record</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editForm" method="POST" action="action_process.php" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit Scholar Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit-id">

                    <!-- Names -->
                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="edit-first" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" id="edit-middle" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="edit-last" required>
                    </div>

                    <!-- School -->
                    <div class="col-md-6">
                        <label class="form-label">School</label>
                        <select class="form-select" name="school" id="edit-school" required>
                            <option value="" disabled>Select School</option>
                            <option value="SOUTHLAND-HS">Southland College of Kabankalan City, Inc.</option>
                            <option value="KCC-HS">Kabankalan Catholic College</option>
                            <option value="FORTRESS">Fortress College</option>
                            <option value="FBC-HS">Fellowship Baptist College</option>
                            <option value="MCHS">Magballo Catholic High School, Inc.</option>
                            <option value="SNAA">Southern Negros Adventist Academy</option>
                        </select>
                    </div>

                    <!-- Year Level -->
                    <div class="col-md-3">
                        <label class="form-label">Year Level</label>
                        <select class="form-select" name="year_level" id="edit-year" required>
                            <option value="" disabled>Select Year Level</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>

                    <!-- Strand -->
                    <div class="col-md-3">
                        <label class="form-label">Strand</label>
                        <select class="form-select" name="strand" id="edit-strand" required>
                            <option value="" disabled>Select Strand</option>
                            <option value="STEM">STEM</option>
                            <option value="ABM">ABM</option>
                            <option value="HUMSS">HUMSS</option>
                            <option value="GAS">GAS</option>
                            <option value="TVL">TVL</option>
                        </select>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select class="form-select" name="semester" id="edit-semester" required>
                            <option value="" disabled>Select Semester</option>
                            <option value="1st semester">1st Semester</option>
                            <option value="2nd semester">2nd Semester</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="col-md-5">
                        <label class="form-label">Address</label>
                        <select class="form-select" name="address" id="edit-address" required>
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

                    <!-- Phone -->
                    <div class="col-md-4">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone_number" id="edit-phone" pattern="[0-9]{11}"
                            maxlength="11" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <div class="form-text">Phone number must be exactly 11 digits.</div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="edit-email" required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit-status" required>
                            <option value="" disabled>Select Status</option>
                            <option value="active">Active</option>
                            <option value="incomplete">Incomplete</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
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

                    <!-- Optional Upload Deadline -->
                    <div class="alert alert-info mt-3">
                        <strong>Optional:</strong> Set an upload deadline for all applicants
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Upload Deadline Date (Optional)</label>
                        <input type="date" name="expire_date" class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>">
                        <small class="form-text text-muted">Leave blank to skip deadline. Date will default to 23:59 as submission time.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Reset All</button>
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
                    <p>Are you sure you want to delete the record of <strong id="deleteName"></strong>?</p>
                </div>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>



    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit-id').value = button.dataset.id;
                document.getElementById('edit-first').value = button.dataset.first;
                document.getElementById('edit-middle').value = button.dataset.middle;
                document.getElementById('edit-last').value = button.dataset.last;

                // Select fields
                document.getElementById('edit-school').value = button.dataset.school;
                document.getElementById('edit-year').value = button.dataset.year;
                document.getElementById('edit-strand').value = button.dataset.strand;
                document.getElementById('edit-semester').value = button.dataset.semester;
                document.getElementById('edit-address').value = button.dataset.address;
                document.getElementById('edit-status').value = button.dataset.status;

                // Inputs
                document.getElementById('edit-phone').value = button.dataset.phone;
                document.getElementById('edit-email').value = button.dataset.email;

                editModal.show();
            });
        });

    </script>

    <script>
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModalEl = document.getElementById('deleteModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                const name = button.dataset.name;

                document.getElementById('delete-id').value = id;
                document.getElementById('deleteName').textContent = name;

                deleteModal.show();
            });
        });

    </script>
</body>

</html>