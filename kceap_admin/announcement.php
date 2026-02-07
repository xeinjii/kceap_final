<?php
session_start();
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>
<body>
    <div class="d-flex">
        <?php include 'aside.php'; ?>

        <div class="main-content flex-grow-1">
            <nav class="navbar navbar-expand navbar-light sticky-top mb-4">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Send Announcement</a>
                </div>
            </nav>

            <div class="container py-4">
                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-<?= htmlspecialchars($_SESSION['message_type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Compose Announcement</h5>
                        <form id="announceForm" action="announcement_process.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" rows="6" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label class="form-label">Audience</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="audience[]" value="college" id="audCollege" checked>
                                        <label class="form-check-label" for="audCollege">College</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="audience[]" value="highschool" id="audHS" checked>
                                        <label class="form-check-label" for="audHS">Highschool</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Attach File (optional)</label>
                                    <input type="file" name="attachment" class="form-control">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" id="previewBtn" class="btn btn-outline-secondary">Preview</button>
                                <button type="submit" class="btn btn-primary">Send Announcement</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Announcements</h5>
                        <div id="recentList">
                            <?php
                            // Load announcements from JSON if present
                            $file = __DIR__ . '/announcements.json';
                            if (file_exists($file)) {
                                $data = json_decode(file_get_contents($file), true) ?: [];
                                foreach (array_reverse($data) as $ann) {
                                    echo '<div class="mb-3 border-bottom pb-2">';
                                    echo '<strong>' . htmlspecialchars($ann['title']) . '</strong> <br>';
                                    echo '<small class="text-muted">' . htmlspecialchars($ann['created_at']) . '</small>';
                                    echo '<p>' . nl2br(htmlspecialchars($ann['message'])) . '</p>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">No announcements yet.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Announcement Preview</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <h4 id="pvTitle"></h4>
            <p id="pvMessage"></p>
            <div id="pvMeta" class="text-muted small"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('previewBtn').addEventListener('click', function(){
        const form = document.getElementById('announceForm');
        const title = form.title.value;
        const message = form.message.value;
        const aud = Array.from(form.querySelectorAll('input[name="audience[]"]:checked')).map(i=>i.value).join(', ');
        document.getElementById('pvTitle').textContent = title;
        document.getElementById('pvMessage').innerHTML = message.replace(/\n/g, '<br>');
        document.getElementById('pvMeta').textContent = 'Audience: ' + aud;
        var modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });
    </script>
</body>

</html>
