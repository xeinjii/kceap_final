<?php
session_start();
include '../kceap_admin/header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>

<body>
    <div class="d-flex">

        <?php include '../kceap_admin/aside.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand navbar-light sticky-top mb-4">
                <div class="container-fluid">
                    <button class="btn btn-link d-block me-3" id="sidebarCollapseBtn">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <a class="navbar-brand" href="#">PENDING</a>
                </div>
            </nav>

          
        </div>
    </div>

    <script>
        // Sidebar collapse/expand
        document.getElementById('sidebarCollapseBtn').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('collapsed');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="../script/bootstrap.bundle.min.js"></script>
</body>

</html>