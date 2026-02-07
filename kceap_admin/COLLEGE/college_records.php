<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch all college accounts
$sql = "SELECT * FROM college_account ORDER BY applicant_id ASC";
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

        .table {
            width: 400px;
        }

        .table th,
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            max-width: 300px;
            font-size: 0.85rem;
        }

        .table td:nth-child(8),
        .table td:nth-child(10) {
            max-width: 300px;
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
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <section class="py-5">
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
                    <!-- Trigger Reset Modal -->
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#resetModal">
                        Reset All to Pending
                    </button>

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
                                            <form action="action_process.php" method="POST" style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $row['applicant_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <span class="material-symbols-outlined"
                                                        style="font-size: 1rem;">delete</span>
                                                </button>
                                            </form>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editForm" method="POST" action="action_process.php">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Edit Scholar Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="edit-first">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" id="edit-middle">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="edit-last">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">School</label>
                            <input type="text" class="form-control" name="school" id="edit-school">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" name="course" id="edit-course">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year Level</label>
                            <input type="text" class="form-control" name="year_level" id="edit-year">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" id="edit-address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" id="edit-phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" id="edit-email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semester</label>
                            <input type="text" class="form-control" name="semester" id="edit-semester">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" name="status" id="edit-status">
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

    <!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="reset_statuses.php" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="resetModalLabel">Confirm Reset</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to reset <strong>all applicant statuses</strong> to <strong>pending</strong>? This action cannot be undone.</p>
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
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit-id').value = button.dataset.id;
                document.getElementById('edit-first').value = button.dataset.first;
                document.getElementById('edit-middle').value = button.dataset.middle;
                document.getElementById('edit-last').value = button.dataset.last;
                document.getElementById('edit-school').value = button.dataset.school;
                document.getElementById('edit-course').value = button.dataset.course;
                document.getElementById('edit-year').value = button.dataset.year;
                document.getElementById('edit-address').value = button.dataset.address;
                document.getElementById('edit-phone').value = button.dataset.phone;
                document.getElementById('edit-email').value = button.dataset.email;
                document.getElementById('edit-status').value = button.dataset.status;
                document.getElementById('edit-semester').value = button.dataset.semester || '';

                editModal.show();
            });
        });
    </script>
</body>

</html>