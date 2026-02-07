<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch all applicants from the highschool_schedule table
$sql = "SELECT * FROM highschool_schedule ORDER BY id DESC";
$result = $conn->query($sql);

// Get limit from deadline.json
$deadlineFile = __DIR__ . '/../deadline.json';
$limit = 0;
$disabled = false;
if (file_exists($deadlineFile)) {
    $settings = json_decode(file_get_contents($deadlineFile), true);
    $limit = $settings['highschool']['limit'] ?? 0;
    $disabled = $settings['highschool']['disabled'] ?? false;
}
$appliedCount = $result ? $result->num_rows : 0;

// Update the deadline settings to check the radio button if the limit is reached
if ($limit > 0 && $appliedCount >= $limit) {
    $deadlineFile = __DIR__ . '/../deadline.json';
    if (file_exists($deadlineFile)) {
        $settings = json_decode(file_get_contents($deadlineFile), true);
        $settings['highschool']['disabled'] = true;
        $settings['highschool']['limit'] = $appliedCount; // Record the current applied count
        file_put_contents($deadlineFile, json_encode($settings, JSON_PRETTY_PRINT));
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>High School Applicants - KCEAP</title>

    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="./style/index.css">
    <link rel="stylesheet" href="./style/bootstrap.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .brand-text {
            font-weight: 600;
            font-size: 1.2rem;
        }
        .table th, .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            max-width: 200px;
            font-size: 0.85rem;
        }
        .table td:nth-child(8), .table td:nth-child(10) {
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
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <span class="material-symbols-outlined align-middle me-1 text-primary">school</span>
                    High School Applicants
                </h3>
                <div class="text-end">
                    <span class="badge bg-info fs-6">Applied: <?= $appliedCount ?> / Limit: <?= $limit ?: 'Unlimited' ?></span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>School</th>
                            <th>Strand</th>
                            <th>Grade Level</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th>Email Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['firstName']) ?></td>
                                    <td><?= htmlspecialchars($row['middleName']) ?></td>
                                    <td><?= htmlspecialchars($row['lastName']) ?></td>
                                    <td><?= htmlspecialchars($row['school']) ?></td>
                                    <td><?= htmlspecialchars($row['strand']) ?></td>
                                    <td><?= htmlspecialchars($row['yearLevel']) ?></td>
                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                    <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                                    <td><?= htmlspecialchars($row['emailAddress']) ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#scheduleModal"
                                            data-id="<?= $row['id'] ?>"
                                            data-firstname="<?= htmlspecialchars($row['firstName']) ?>"
                                            data-middlename="<?= htmlspecialchars($row['middleName']) ?>"
                                            data-lastname="<?= htmlspecialchars($row['lastName']) ?>"
                                            data-school="<?= htmlspecialchars($row['school']) ?>"
                                            data-strand="<?= htmlspecialchars($row['strand']) ?>"
                                            data-year="<?= htmlspecialchars($row['yearLevel']) ?>"
                                            data-address="<?= htmlspecialchars($row['address']) ?>"
                                            data-phone="<?= htmlspecialchars($row['phoneNumber']) ?>"
                                            data-email="<?= htmlspecialchars($row['emailAddress']) ?>"
                                        >Set</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="11" class="text-center text-muted">No applicants found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Applicant Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Name:</strong> <span id="displayFullName"></span><br>
                    <strong>Email:</strong> <span id="displayEmail"></span><br>
                    <strong>Phone:</strong> <span id="displayPhone"></span><br>
                    <strong>School:</strong> <span id="displaySchool"></span><br>
                    <strong>Strand:</strong> <span id="displayStrand"></span><br>
                    <strong>Year Level:</strong> <span id="displayYear"></span><br>
                    <strong>Address:</strong> <span id="displayAddress"></span>
                </div>

                <form action="set_schedule_process.php" method="POST">
                    <!-- Hidden inputs -->
                    <input type="hidden" name="id" id="modalApplicantId">
                    <input type="hidden" name="firstName" id="modalFirstName">
                    <input type="hidden" name="middleName" id="modalMiddleName">
                    <input type="hidden" name="lastName" id="modalLastName">
                    <input type="hidden" name="school" id="modalSchoolInput">
                    <input type="hidden" name="strand" id="modalStrandInput">
                    <input type="hidden" name="yearLevel" id="modalYearInput">
                    <input type="hidden" name="address" id="modalAddressInput">
                    <input type="hidden" name="phoneNumber" id="modalPhoneInput">
                    <input type="hidden" name="emailAddress" id="modalEmailInput">

                    <div class="mb-3">
                        <label for="date" class="form-label">Select Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Select Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <div class="text-end">
                        <button name="submit" type="submit" class="btn btn-primary">Set Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const scheduleModal = document.getElementById('scheduleModal');
    scheduleModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const data = {
            id: button.getAttribute('data-id'),
            firstName: button.getAttribute('data-firstname'),
            middleName: button.getAttribute('data-middlename'),
            lastName: button.getAttribute('data-lastname'),
            school: button.getAttribute('data-school'),
            strand: button.getAttribute('data-strand'),
            year: button.getAttribute('data-year'),
            address: button.getAttribute('data-address'),
            phone: button.getAttribute('data-phone'),
            email: button.getAttribute('data-email')
        };

        // Set hidden inputs
        document.getElementById('modalApplicantId').value = data.id;
        document.getElementById('modalFirstName').value = data.firstName;
        document.getElementById('modalMiddleName').value = data.middleName;
        document.getElementById('modalLastName').value = data.lastName;
        document.getElementById('modalSchoolInput').value = data.school;
        document.getElementById('modalStrandInput').value = data.strand;
        document.getElementById('modalYearInput').value = data.year;
        document.getElementById('modalAddressInput').value = data.address;
        document.getElementById('modalPhoneInput').value = data.phone;
        document.getElementById('modalEmailInput').value = data.email;

        // Set visible fields
        document.getElementById('displayFullName').textContent = `${data.firstName} ${data.middleName} ${data.lastName}`;
        document.getElementById('displayEmail').textContent = data.email;
        document.getElementById('displayPhone').textContent = data.phone;
        document.getElementById('displaySchool').textContent = data.school;
        document.getElementById('displayStrand').textContent = data.strand;
        document.getElementById('displayYear').textContent = data.year;
        document.getElementById('displayAddress').textContent = data.address;
    });
</script>

</body>
</html>
