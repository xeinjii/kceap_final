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
                                $total = count($data);
                                foreach (array_reverse($data, true) as $index => $ann) {
                                    echo '<div class="mb-3 border-bottom pb-2 announcement-item" data-index="' . $index . '">';
                                    echo '<div class="d-flex justify-content-between align-items-start">';
                                    echo '<div>';
                                    echo '<strong>' . htmlspecialchars($ann['title']) . '</strong> <br>';
                                    echo '<small class="text-muted">' . htmlspecialchars($ann['created_at']) . '</small>';
                                    echo '</div>';
                                    echo '<button type="button" class="btn btn-sm btn-danger delete-announcement" data-index="' . $index . '" title="Delete announcement">Delete</button>';
                                    echo '</div>';
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

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="previewModalLabel">
              <span class="material-symbols-outlined align-middle me-2">preview</span>
              Announcement Preview
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h4 id="pvTitle" class="mb-3"></h4>
            <div id="pvMessage" class="border-start border-4 border-primary ps-3 mb-3"></div>
            <div id="pvMeta" class="text-muted small"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deleteModalLabel">
              <span class="material-symbols-outlined align-middle me-2">delete</span>
              Delete Announcement
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the announcement <strong id="deleteTitle"></strong>?</p>
            <p class="text-muted mb-0"><small>This action cannot be undone.</small></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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

    // Delete announcement function
    let currentDeleteIndex = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.querySelectorAll('.delete-announcement').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentDeleteIndex = this.getAttribute('data-index');
            const title = this.closest('.announcement-item').querySelector('strong').textContent;
            
            document.getElementById('deleteTitle').textContent = '"' + title + '"';
            deleteModal.show();
        });
    });
    
    // Handle delete confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentDeleteIndex !== null) {
            fetch('announcement_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete&index=' + encodeURIComponent(currentDeleteIndex)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deleteModal.hide();
                    location.reload();
                } else {
                    deleteModal.hide();
                    alert('Error deleting announcement: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                deleteModal.hide();
                alert('Error deleting announcement');
            });
        }
    });
    </script>
</body>

</html>
