<?php
session_start();
include '../config/config.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM admin ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
$count = 1;
?>

<body>
    <div class="d-flex">

        <?php include '../kceap_admin/aside.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">

            <!-- Top Navbar (Menu Removed) -->
            <nav class="navbar navbar-expand navbar-light sticky-top mb-4 shadow-sm bg-white px-3">
                <div class="container-fluid">
                    <a class="navbar-brand fw-semibold fs-4" href="#">Admin Accounts</a>

                    <button class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Add Account
                    </button>
                </div>
            </nav></br>


            <!-- Admin Accounts Table -->
            <div class="container px-4 pb-5">
                <div class="card p-4 border-0 shadow-sm rounded-4">

                    <!-- Session Messages -->
                    <?php
                    $session_messages = [
                        'message' => 'success',
                        'error' => 'danger'
                    ];
                    foreach ($session_messages as $key => $type):
                        if (isset($_SESSION[$key])):
                            ?>
                            <div class="alert alert-<?= $type ?> alert-dismissible fade show mx-4 session-message">
                                <?= htmlspecialchars($_SESSION[$key]) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php
                            unset($_SESSION[$key]);
                        endif;
                    endforeach;
                    ?>

                    <script>
                        setTimeout(() => {
                            document.querySelectorAll('.session-message').forEach(message => {
                                message.classList.remove('show');
                                message.classList.add('fade');
                                setTimeout(() => message.remove(), 500);
                            });
                        }, 2000);
                    </script>


                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-primary">
                            <span class="material-symbols-outlined align-middle me-1">
                                admin_panel_settings
                            </span>
                            List of Admin Accounts
                        </h5>
                    </div>

                    <?php $rowCount = mysqli_num_rows($result); ?>

                    <div class="table-responsive"
                        style="<?= $rowCount > 7 ? 'max-height: 400px; overflow-y: auto;' : '' ?>">
                        <table class="table table-bordered table-hover table-striped align-middle">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th style="width:20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rowCount > 0): ?>
                                    <?php $count = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning me-2 editBtn"
                                                    data-fullname="<?= htmlspecialchars($row['fullname']) ?>"
                                                    data-username="<?= htmlspecialchars($row['username']) ?>">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>

                                                <button class="btn btn-sm btn-danger deleteBtn"
                                                    data-username="<?= htmlspecialchars($row['username']) ?>">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No admin accounts found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Add Account Modal -->
            <div class="modal fade" id="addAccountModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="addacc_process.php" method="post" class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Add New Admin</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="fullname" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" id="pass" name="password" required
                                    minlength="8" oninput="checkPasswordStrength(this.value)">

                                <div class="progress mt-2" style="height:6px;">
                                    <div id="passwordStrengthBar" class="progress-bar"></div>
                                </div>
                                <small id="passwordStrengthText"></small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" id="saveAccountBtn" class="btn btn-success" disabled>
                                Save Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Account Modal -->
            <div class="modal fade" id="editAccountModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form id="editForm" method="post" class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Edit Admin Account</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" id="editFullName" name="fullname" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" id="editUsername" name="username" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" id="editPassword" name="password" class="form-control"
                                    placeholder="Leave blank to keep current password">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="deleteModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="delete_admin_process.php" class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p id="deleteMessage"></p>
                            <input type="hidden" name="username" id="deleteUsername">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            document.querySelectorAll('.editBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const fullName = this.dataset.fullname;
                    const username = this.dataset.username;

                    document.getElementById('editFullName').value = fullName;
                    document.getElementById('editUsername').value = username;
                    document.getElementById('editPassword').value = '';
                    document.getElementById('editForm').action =
                        `edit_admin_process.php?username=${username}`;

                    new bootstrap.Modal(document.getElementById('editAccountModal')).show();
                });
            });

            document.querySelectorAll('.deleteBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const username = this.dataset.username;

                    document.getElementById('deleteUsername').value = username;
                    document.getElementById('deleteMessage').innerText =
                        `Are you sure you want to delete the admin account for "${username}"?`;

                    new bootstrap.Modal(document.getElementById('deleteModal')).show();
                });
            });

        });
    </script>

    <script>
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[@$!%*?&]/.test(password)) strength++;

            const bar = document.getElementById("passwordStrengthBar");
            const text = document.getElementById("passwordStrengthText");
            const saveBtn = document.getElementById("saveAccountBtn");

            const levels = ["Very weak", "Weak", "Medium", "Strong", "Very strong"];
            const colors = ["danger", "warning", "info", "primary", "success"];

            const index = Math.min(strength - 1, 4);

            if (strength < 3) saveBtn.disabled = true;
            else saveBtn.disabled = false;

            bar.style.width = (strength * 20) + "%";
            bar.className = "progress-bar bg-" + colors[index];
            text.innerText = levels[index] || "";
            text.className = "form-text text-" + colors[index];
        }
    </script>

    <script>
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function () {
                const username = this.dataset.username;
                document.getElementById('deleteUsername').value = username;
                document.getElementById('deleteMessage').innerText =
                    `Are you sure you want to delete the admin account for "${username}"? This action cannot be undone.`;

                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });
    </script>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>