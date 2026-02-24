<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}

// Fetch expired college applicants
$sql = "SELECT * FROM college_account WHERE status = 'expired' ORDER BY upload_deadline DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Expired Applicants - KCEAP</title>

    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .brand-text { font-weight: 600; font-size: 1.2rem; }
        .table th, .table td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; font-size: 0.9rem; }
        .table thead { background-color: #0d6efd; color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .table-responsive.scroll-area { max-height: 60vh; overflow-y: auto; }
        .table-responsive.scroll-area thead th { position: sticky; top: 0; background: #0d6efd; color: #fff; z-index: 2; }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg py-3" style="background-color:#0d6efd;">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="navbar-brand d-flex align-items-center text-white">
                <img src="../../img/logo.png" alt="KCEAP Logo" width="40" class="me-2">
                <span class="brand-text">KCEAP Scholarships</span>
            </div>
            <a href="../collegepage.php" class="btn btn-outline-light btn-sm">Back to Mainpage</a>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-1 text-danger">report_problem</span>
                        Expired Applicants
                    </h3>
                </div>

                <div class="table-responsive scroll-area">
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
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Address</th>
                                <th>Upload Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td title="<?= htmlspecialchars($row['first_name']) ?>"><?= htmlspecialchars($row['first_name']) ?></td>
                                        <td title="<?= htmlspecialchars($row['middle_name']) ?>"><?= htmlspecialchars($row['middle_name']) ?></td>
                                        <td title="<?= htmlspecialchars($row['last_name']) ?>"><?= htmlspecialchars($row['last_name']) ?></td>
                                        <td title="<?= htmlspecialchars($row['school']) ?>"><?= htmlspecialchars($row['school']) ?></td>
                                        <td title="<?= htmlspecialchars($row['course']) ?>"><?= htmlspecialchars($row['course']) ?></td>
                                        <td title="<?= htmlspecialchars($row['year_level']) ?>"><?= htmlspecialchars($row['year_level']) ?></td>
                                        <td title="<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></td>
                                        <td title="<?= htmlspecialchars($row['phone_number']) ?>"><?= htmlspecialchars($row['phone_number']) ?></td>
                                        <td title="<?= htmlspecialchars($row['address']) ?>"><?= htmlspecialchars($row['address']) ?></td>
                                        <td><?= $row['upload_deadline'] ? date('F j, Y \a\t g:i A', strtotime($row['upload_deadline'])) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No expired applicants found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

