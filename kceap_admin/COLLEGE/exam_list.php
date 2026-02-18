<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch all scheduled applicants from college_schedule_list
$sql = "SELECT * FROM college_schedule_list ORDER BY schedule_date DESC, schedule_time DESC";
$result = $conn->query($sql);
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

        .table th,
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            max-width: 200px;
            font-size: 0.85rem;
        }

        /* Make specific columns a bit wider */
        .table td:nth-child(8),
        /* Address */
        .table td:nth-child(10) {
            /* Email Address */
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
        <div id="alertMessage" class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show"
            role="alert">
            <?php echo $_SESSION['message']; ?>
        </div>

        <?php
        // Unset immediately so it doesn't persist on refresh
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>

        <script>
            // Automatically close the alert after 3 seconds (3000ms)
            setTimeout(function () {
                var alertBox = document.getElementById('alertMessage');
                if (alertBox) {
                    // Bootstrap alert close with animation
                    var bsAlert = new bootstrap.Alert(alertBox);
                    bsAlert.close();
                }
            }, 3000);
        </script>
    <?php endif; ?>


    <section class="py-5">
        <div class="container">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-1 text-primary">school</span>
                        Scheduled College Applicants
                    </h3>
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
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Email Address</th>
                                <th>Date</th>
                                <th>Time</th>
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
                                        <td title="<?= htmlspecialchars($row['email_address']) ?>">
                                            <?= htmlspecialchars($row['email_address']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['schedule_date']) ?></td>
                                        <td><?= htmlspecialchars($row['schedule_time']) ?></td>
                                        <td>
                                            <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal"
                                                data-bs-target="#actionModal" data-id="<?= $row['id'] ?>" data-action="accept">
                                                Accept
                                            </button>
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#actionModal" data-id="<?= $row['id'] ?>" data-action="reject">
                                                Reject
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="text-center text-muted">No scheduled applicants found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <span id="modalAction"></span> this applicant?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmActionBtn" class="btn btn-primary">Yes</a>
                </div>
            </div>
        </div>
    </div>


   

    <script>
    var actionModal = document.getElementById('actionModal');
    actionModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var id = button.getAttribute('data-id'); 
        var action = button.getAttribute('data-action');

        // Update modal content
        var modalAction = actionModal.querySelector('#modalAction');
        modalAction.textContent = action;

        // Update confirm button link
        var confirmBtn = actionModal.querySelector('#confirmActionBtn');
        confirmBtn.href = `exam_list_process.php?id=${id}&action=${action}`;

        // Update confirm button style
        confirmBtn.className = action === 'accept' ? 'btn btn-success' : 'btn btn-danger';
    });
</script>

         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>