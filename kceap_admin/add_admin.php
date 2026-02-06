<?php
session_start();
include '../config/config.php';
include 'header.php';

$sql = "SELECT * FROM admin  ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
$count = 1;
?>

<body>
    <div class="d-flex">

        <?php include '../kceap_admin/aside.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">

            <!-- Top Navbar -->
            <nav class="navbar navbar-expand navbar-light sticky-top mb-4 shadow-sm bg-white px-3">
                <div class="container-fluid">
                    <button class="btn btn-link d-block me-3" id="sidebarCollapseBtn">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <a class="navbar-brand fw-semibold fs-4" href="#">Admin Accounts</a>
                    <button class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Add Account
                    </button>
                </div>
            </nav><br><br>

            <!-- Enhanced Admin Accounts Table -->
            <div class="container px-4">
                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h5 class="mb-0">List of Admin Accounts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?= $count++ ?></td>
                                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                                <td><?= htmlspecialchars($row['username']) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-2">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No admin accounts found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Add Account Modal -->
            <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="addacc_process.php" method="post" class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addAccountLabel">Add New Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    placeholder="Enter full name" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter username" required>
                            </div>
                            <div class="mb-3">
                                <label for="pass" class="form-label">Password</label>
                                <input type="password" class="form-control" id="pass" name="password"
                                    placeholder="Enter password" required minlength="8"
                                    oninput="checkPasswordStrength(this.value)">

                                <!-- Strength bar -->
                                <div class="progress mt-2" style="height: 6px;">
                                    <div id="passwordStrengthBar" class="progress-bar" role="progressbar"
                                        style="width: 0%;"></div>
                                </div>

                                <!-- Strength text -->
                                <small id="passwordStrengthText" class="form-text"></small>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="submit" id="saveAccountBtn" class="btn btn-success"
                                disabled>Save Account</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Account Modal -->
            <div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form id="editForm" method="post" class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="editAccountLabel">Edit Admin Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editFullName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="editFullName" name="fullname" required>
                            </div>
                            <div class="mb-3">
                                <label for="editUsername" class="form-label">Username</label>
                                <input type="text" class="form-control" id="editUsername" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="editNewUsername" class="form-label">New Username</label>
                                <input type="text" class="form-control" id="editNewUsername" name="new_username"
                                    placeholder="Enter new username">
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="editPassword" name="password"
                                    placeholder="Leave blank to keep current password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('sidebarCollapseBtn').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('collapsed');
        });

        // Handle Edit Button Click
        document.querySelectorAll('.btn-warning').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const fullName = row.children[1].textContent.trim();
                const username = row.children[2].textContent.trim();

                // Populate the modal with existing data
                document.getElementById('editFullName').value = fullName;
                document.getElementById('editUsername').value = username;
                document.getElementById('editPassword').value = '';

                // Set the form action dynamically
                document.getElementById('editForm').action = `edit_admin_process.php?username=${username}`;

                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
                editModal.show();
            });
        });

        // Handle Delete Button Click
        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const username = row.children[2].textContent.trim();

                if (confirm(`Are you sure you want to delete the admin account for ${username}?`)) {
                    window.location.href = `delete_admin_process.php?username=${username}`;
                }
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

            let strengthText = "";
            let strengthClass = "";
            let barWidth = 0;

            if (strength <= 1) {
                barWidth = 20;
                strengthText = "Very weak";
                strengthClass = "bg-danger";
                saveBtn.disabled = true;
            } else if (strength === 2) {
                barWidth = 40;
                strengthText = "Weak";
                strengthClass = "bg-warning";
                saveBtn.disabled = true;
            } else if (strength === 3) {
                barWidth = 60;
                strengthText = "Medium";
                strengthClass = "bg-info";
                saveBtn.disabled = false;
            } else if (strength === 4) {
                barWidth = 80;
                strengthText = "Strong";
                strengthClass = "bg-primary";
                saveBtn.disabled = false;
            } else if (strength === 5) {
                barWidth = 100;
                strengthText = "Very strong";
                strengthClass = "bg-success";
                saveBtn.disabled = false;
            }

            bar.style.width = barWidth + "%";
            bar.className = "progress-bar " + strengthClass;
            text.textContent = strengthText;
            text.className = "form-text text-" + strengthClass.split('-')[1]; // match text color
        }
    </script>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script/bootstrap.bundle.min.js"></script>

</body>

</html>