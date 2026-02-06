<?php
require_once '../../config/config.php';
session_start();

// Fetch all applicants from the college_schedule table
$sql = "SELECT * FROM college_schedule ORDER BY id DESC";
$result = $conn->query($sql);

// Get limit from deadline.json
$deadlineFile = __DIR__ . '/../deadline.json';
$limit = 0;
$disabled = false;
if (file_exists($deadlineFile)) {
    $settings = json_decode(file_get_contents($deadlineFile), true);
    $limit = $settings['college']['limit'] ?? 0;
    $disabled = $settings['college']['disabled'] ?? false;
}
$appliedCount = $result ? $result->num_rows : 0;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>College Applicants - KCEAP</title>

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
        .table td:nth-child(8),  /* Address */
        .table td:nth-child(10) { /* Email Address */
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
                    College Applicants
                </h3>
                <div class="text-end">
                    <span class="badge bg-info fs-6">Applied: <?= $appliedCount ?> / Limit: <?= $limit ?: 'Unlimited' ?></span>
                </div>
            </div>

            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['message_type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle">
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td title="<?= htmlspecialchars($row['firstName']) ?>"><?= htmlspecialchars($row['firstName']) ?></td>
                                    <td title="<?= htmlspecialchars($row['middleName']) ?>"><?= htmlspecialchars($row['middleName']) ?></td>
                                    <td title="<?= htmlspecialchars($row['lastName']) ?>"><?= htmlspecialchars($row['lastName']) ?></td>
                                    <td title="<?= htmlspecialchars($row['school']) ?>"><?= htmlspecialchars($row['school']) ?></td>
                                    <td title="<?= htmlspecialchars($row['course']) ?>"><?= htmlspecialchars($row['course']) ?></td>
                                    <td title="<?= htmlspecialchars($row['yearLevel']) ?>"><?= htmlspecialchars($row['yearLevel']) ?></td>
                                    <td title="<?= htmlspecialchars($row['address']) ?>"><?= htmlspecialchars($row['address']) ?></td>
                                    <td title="<?= htmlspecialchars($row['phoneNumber']) ?>"><?= htmlspecialchars($row['phoneNumber']) ?></td>
                                    <td title="<?= htmlspecialchars($row['emailAddress']) ?>"><?= htmlspecialchars($row['emailAddress']) ?></td>
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
                                            data-course="<?= htmlspecialchars($row['course']) ?>"
                                            data-year="<?= htmlspecialchars($row['yearLevel']) ?>"
                                            data-address="<?= htmlspecialchars($row['address']) ?>"
                                            data-phone="<?= htmlspecialchars($row['phoneNumber']) ?>"
                                            data-email="<?= htmlspecialchars($row['emailAddress']) ?>"
                                        >Set</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted">No applicants found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Applicant Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Display Info -->
                <div class="mb-3">
                    <strong>Name:</strong> <span id="displayFullName"></span><br>
                    <strong>Email:</strong> <span id="displayEmail"></span><br>
                    <strong>Phone:</strong> <span id="displayPhone"></span><br>
                    <strong>School:</strong> <span id="displaySchool"></span><br>
                    <strong>Course:</strong> <span id="displayCourse"></span><br>
                    <strong>Year Level:</strong> <span id="displayYear"></span><br>
                    <strong>Address:</strong> <span id="displayAddress"></span>
                </div>
                <form action="set_schedule_process.php" method="POST">
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="id" id="modalApplicantId">
                    <input type="hidden" name="firstName" id="modalFirstName">
                    <input type="hidden" name="middleName" id="modalMiddleName">
                    <input type="hidden" name="lastName" id="modalLastName">
                    <input type="hidden" name="school" id="modalSchoolInput">
                    <input type="hidden" name="course" id="modalCourseInput">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const scheduleModal = document.getElementById('scheduleModal');
    scheduleModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const firstName = button.getAttribute('data-firstname');
        const middleName = button.getAttribute('data-middlename');
        const lastName = button.getAttribute('data-lastname');
        const school = button.getAttribute('data-school');
        const course = button.getAttribute('data-course');
        const year = button.getAttribute('data-year');
        const address = button.getAttribute('data-address');
        const phone = button.getAttribute('data-phone');
        const email = button.getAttribute('data-email');
        document.getElementById('modalApplicantId').value = id;
        document.getElementById('modalFirstName').value = firstName;
        document.getElementById('modalMiddleName').value = middleName;
        document.getElementById('modalLastName').value = lastName;
        document.getElementById('modalSchoolInput').value = school;
        document.getElementById('modalCourseInput').value = course;
        document.getElementById('modalYearInput').value = year;
        document.getElementById('modalAddressInput').value = address;
        document.getElementById('modalPhoneInput').value = phone;
        document.getElementById('modalEmailInput').value = email;
        document.getElementById('displayFullName').textContent = firstName + ' ' + middleName + ' ' + lastName;
        document.getElementById('displayEmail').textContent = email;
        document.getElementById('displayPhone').textContent = phone;
        document.getElementById('displaySchool').textContent = school;
        document.getElementById('displayCourse').textContent = course;
        document.getElementById('displayYear').textContent = year;
        document.getElementById('displayAddress').textContent = address;
    });
</script>
</body>
</html>