<?php
require_once '../../config/config.php';
session_start();


$sql = "
SELECT 
    ca.applicant_id,
    ca.first_name,
    ca.last_name,
    ca.email,
    ca.semester,
    ca.year_level,
    ca.status,
    ca.created_at,
    COUNT(cd.id) AS doc_count,
    MIN(cd.file_path) AS preview_image
FROM highschool_account ca
LEFT JOIN highschool_renew_documents cd 
    ON ca.applicant_id = cd.account_id
WHERE ca.status = 'pending'
GROUP BY ca.applicant_id
ORDER BY ca.created_at DESC
";


$result = $conn->query($sql);
$renewals = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Renewal Requests - KCEAP</title>
    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .brand-text { font-weight: 600; font-size: 1.2rem; }
        .table th, .table td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; max-width: 200px; font-size: 0.9rem; }
        .table thead { background-color: #0d6efd; color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .modal-img { max-width: 100%; max-height: 80vh; }
        .doc-thumbnail { width: 100%; height: auto; border-radius: 4px; cursor: pointer; }
        .file-icon { background-color: #f0f0f0; padding: 10px; border-radius: 4px; display:flex; align-items:center; justify-content:center; font-weight:600; }
        .navbar { background-color: #0d6efd; }
        .navbar .nav-link, .navbar .navbar-brand { color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="navbar-brand d-flex align-items-center">
            <img src="../../img/logo.png" alt="KCEAP Logo" width="40" class="me-2">
            <span class="brand-text">KCEAP Renewals</span>
        </div>
        <a href="pending.php" class="btn btn-outline-light btn-sm">Back to Pending</a>
    </div>
</nav>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.style.display='none';"></button>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
<?php endif; ?>

<section class="py-5">
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><span class="material-symbols-outlined align-middle me-1 text-primary">refresh</span> Renewal Requests</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead>
                        <tr>
                           <th>#</th>
                            <th>Applicant</th>
                            <th>Semester</th>
                            <th>Year Level</th>
                            <th>Documents</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($renewals)): ?>
                        <?php $i = 1; foreach ($renewals as $r): ?>
                            <?php
                                $rd_sql = "SELECT id, file_name, file_path FROM highschool_renew_documents WHERE account_id = ? ORDER BY id DESC";
                                $rd_stmt = $conn->prepare($rd_sql);
                                $rd_stmt->bind_param("i", $r['applicant_id']);
                                $rd_stmt->execute();
                                $r_docs = $rd_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                $rd_stmt->close();
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                             
                                <td><?= htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($r['semester'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($r['year_level'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (!empty($r_docs)): ?>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#docsModal" onclick='showDocuments(<?= htmlspecialchars(json_encode($r_docs)); ?>)'>
                                            <strong><?= count($r_docs) ?></strong> files
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-warning">No docs</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($r['created_at']))) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['status'] ?? 'pending') ?></span></td>
                                <td>
                                    <button class="btn btn-success btn-sm me-1" onclick="confirmAccept(<?= $r['applicant_id'] ?>)">Accept</button>
                                    <button type="button" class="btn btn-sm btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#viewModal" onclick='viewRenewal(<?= htmlspecialchars(json_encode($r)); ?>, <?= htmlspecialchars(json_encode($r_docs)); ?>)'>View</button>
                                    <button type="button" class="btn btn-sm btn-warning ms-1" data-bs-toggle="modal" data-bs-target="#messageModal" onclick='openMessageModal(<?= htmlspecialchars(json_encode($r)); ?>)'>Message</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No renewal requests.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script src="../../script/bootstrap.bundle.min.js"></script>

<!-- View Documents Modal -->
<div class="modal fade" id="docsModal" tabindex="-1" aria-labelledby="docsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="docsModalLabel">Uploaded Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="docsModalBody"></div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Renewal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody"></div>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">View Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Document" class="modal-img">
            </div>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Send Message to Applicant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="messageForm" method="POST" action="send_message.php">
                    <input type="hidden" id="applicant_id" name="applicant_id">
                    <input type="hidden" name="redirect" value="renewals.php">
                    <div class="mb-3">
                        <label for="applicant_name" class="form-label"><strong>To:</strong></label>
                        <input type="text" class="form-control" id="applicant_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="applicant_email" class="form-label"><strong>Email:</strong></label>
                        <input type="email" class="form-control" id="applicant_email" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="message_subject" class="form-label"><strong>Subject:</strong></label>
                        <input type="text" class="form-control" id="message_subject" name="subject" placeholder="Enter message subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message_body" class="form-label"><strong>Message:</strong></label>
                        <textarea class="form-control" id="message_body" name="message" rows="6" placeholder="Enter your message here..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDocuments(docs) {
        let html = '<div class="row">';
        docs.forEach(doc => {
            const fileExt = doc.file_path.split('.').pop().toLowerCase();
            const isImage = ['jpg','jpeg','png','gif','webp'].includes(fileExt);
            const fileUrl = '../../sc_documents/' + doc.file_path.split('/').pop();
            html += '<div class="col-md-6 mb-3">';
            if (isImage) {
                html += `<img src="${fileUrl}" alt="${doc.file_name}" class="doc-thumbnail" onclick="showImage('${fileUrl}','${doc.file_name}')" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#imageModal">`;
            } else {
                html += `<div class="file-icon">${fileExt.toUpperCase()}</div>`;
            }
            html += `<p class="mt-2"><small>${doc.file_name}</small></p>`;
            html += `<a href="${fileUrl}" class="btn btn-sm btn-info" target="_blank">Download</a>`;
            html += '</div>';
        });
        html += '</div>';
        document.getElementById('docsModalBody').innerHTML = html;
    }

    function viewRenewal(renewal, docs) {
        let html = '<div>';
        html += '<h6><strong>Applicant Information:</strong></h6>';
        html += '<p><strong>Name:</strong> ' + (renewal.first_name || '') + ' ' + (renewal.last_name || '') + '</p>';
        html += '<p><strong>Email:</strong> ' + (renewal.email || '') + '</p>';
        html += '<p><strong>Requested On:</strong> ' + (renewal.created_at || '') + '</p>';
        html += '<p><strong>Status:</strong> <span class="badge bg-info text-dark">' + (renewal.status || 'pending') + '</span></p>';
        
        if (docs && docs.length > 0) {
            html += '<hr><h6><strong>Uploaded Documents/Pictures:</strong></h6>';
            html += '<div class="row">';
            docs.forEach(doc => {
                const fileExt = doc.file_path.split('.').pop().toLowerCase();
                const isImage = ['jpg','jpeg','png','gif','webp'].includes(fileExt);
                const fileUrl = '../../sc_documents/' + doc.file_path.split('/').pop();
                html += '<div class="col-md-4 mb-3 text-center">';
                if (isImage) html += `<img src="${fileUrl}" alt="${doc.file_name}" style="max-width:100%;height:auto;border-radius:4px;margin-bottom:10px;cursor:pointer;border:1px solid #ccc;" onclick="showImage('${fileUrl}','${doc.file_name}')" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#imageModal">`;
                else html += `<div class="file-icon" style="width:100%;height:120px;margin-bottom:10px;border:1px solid #ccc;">${fileExt.toUpperCase()}</div>`;
                html += `<p><small>${doc.file_name}</small></p>`;
                html += `<a href="${fileUrl}" class="btn btn-sm btn-info" target="_blank">View/Download</a>`;
                html += '</div>';
            });
            html += '</div>';
        } else {
            html += '<hr><p class="text-muted"><small>No documents uploaded.</small></p>';
        }
        html += '</div>';
        document.getElementById('viewModalBody').innerHTML = html;
    }

    function showImage(imageSrc, imageName) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModalLabel').textContent = 'View: ' + imageName;
    }

    function confirmAccept(id) {
        if (!confirm('Accept this renewal applicant?')) return;
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = 'accept_renewal.php';
        const i = document.createElement('input'); i.type='hidden'; i.name='id'; i.value = id; f.appendChild(i);
        const a = document.createElement('input'); a.type='hidden'; a.name='action'; a.value = 'accept'; f.appendChild(a);
        document.body.appendChild(f); f.submit();
    }

    function openMessageModal(applicant) {
        document.getElementById('applicant_id').value = applicant.applicant_id;
        document.getElementById('applicant_name').value = applicant.first_name + ' ' + applicant.last_name;
        document.getElementById('applicant_email').value = applicant.email;
        document.getElementById('message_subject').value = '';
        document.getElementById('message_body').value = '';
    }
</script>
</body>
</html>
