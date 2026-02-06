<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-title d-flex align-items-center">
        <img src="../img/logo.png" alt="KCEAP Logo" width="45" class="me-1">
        <span>KCEAP Admin</span>
    </div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="archive.php">
            <span class="material-symbols-outlined">archive</span>
            <span>Archive</span>
            </a>
        </li>
        <hr>
        <li class="nav-item">
        <a class="nav-link" href="collegepage.php">
            <span class="material-symbols-outlined">school</span>
            <span>College</span>
        </a>
        </li>

        <li class="nav-item">
        <a class="nav-link" href="highschoolpage.php">
            <span class="material-symbols-outlined">school</span>
            <span>Highschool</span>
        </a>
        </li>

        <hr>
        <li class="nav-item">
            <a class="nav-link" href="announcement.php">
            <span class="material-symbols-outlined">notifications</span>
            <span>Announcement</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="add_admin.php" class="nav-link">
                <span class="material-symbols-outlined">person_add</span>
                <span>Add new admin</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="deadline.php" class="nav-link">
                <span class="material-symbols-outlined">event_note</span>
                <span>Deadlines</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <span class="material-symbols-outlined">logout</span>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

<!-- Logout confirmation modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="logout.php" class="d-inline">
          <input type="hidden" name="confirm" value="1">
          <button type="submit" class="btn btn-danger">Logout</button>
        </form>
      </div>
    </div>
  </div>
</div>