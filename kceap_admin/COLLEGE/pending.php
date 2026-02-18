<?php
require_once '../../config/config.php';
session_start();

// Fetch pending applicants with their documents
$sql = "
SELECT 
    ca.applicant_id,
    ca.first_name,
    ca.last_name,
    ca.email,
    ca.status,
    ca.created_at,
    COUNT(cd.id) AS doc_count,
    MIN(cd.file_path) AS preview_image
FROM college_account ca
LEFT JOIN college_documents cd 
    ON ca.applicant_id = cd.account_id
WHERE ca.status = 'incomplete'
GROUP BY ca.applicant_id
ORDER BY ca.created_at DESC
";

$result = $conn->query($sql);
$pending_applicants = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pending Applicants - KCEAP</title>

    <link rel="icon" href="../../img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="./style/index.css">
    <link rel="stylesheet" href="./style/bootstrap.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .brand-text { font-weight: 600; font-size: 1.2rem; }
        .table th, .table td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; max-width: 200px; font-size: 0.9rem; }
        .table thead { background-color: #0d6efd; color: white; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .modal-img { max-width: 100%; max-height: 80vh; }
        .doc-thumbnail { width: 100%; height: auto; border-radius: 4px; cursor: pointer; }
        .file-icon { background-color: #f0f0f0; padding: 10px; border-radius: 4px; display:flex; align-items:center; justify-content:center; font-weight:600; }
        /* Navbar color and link styles (match exam_list) */
        .navbar { background-color: #0d6efd; }
        .navbar .nav-link, .navbar .navbar-brand { color: white; }
        .navbar .nav-link:hover { color: #ffc107; }
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
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.style.display='none';"></button>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
<?php endif; ?>

<section class="py-4">
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><span class="material-symbols-outlined align-middle me-1 text-primary">pending</span> Pending College Applicants</h3>
                <div>
                    <select id="viewSelect" class="form-select form-select-sm" style="width:170px;">
                        <option value="pending" selected>New Applicants</option>
                        <option value="renewals">Renewal</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead style="position: sticky; top: 0; z-index: 2;">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Documents</th>
                            <th>Applied On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($pending_applicants)): ?>
                        <?php $i = 1; foreach ($pending_applicants as $applicant): ?>
                            <?php
                                // Fetch regular uploaded documents for this applicant
                                $doc_sql = "SELECT id, file_name, file_path FROM college_documents WHERE account_id = ? ORDER BY id DESC";
                                $doc_stmt = $conn->prepare($doc_sql);
                                $doc_stmt->bind_param("i", $applicant['applicant_id']);
                                $doc_stmt->execute();
                                $docs = $doc_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                $doc_stmt->close();
                                $renewals = [];
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td title="<?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?>"><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></td>
                                <td title="<?= htmlspecialchars($applicant['email']) ?>"><?= htmlspecialchars($applicant['email']) ?></td>
                                <td>
                                    <?php if (!empty($docs)): ?>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#docsModal" onclick='showDocuments(<?= htmlspecialchars(json_encode($docs)); ?>)'>
                                            <strong><?= count($docs) ?></strong> files
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-warning">No docs</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($applicant['created_at']))) ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm me-1" onclick="confirmAccept(<?= $applicant['applicant_id'] ?>)">Accept</button>
                                    <form method="POST" action="reject_pending.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($applicant['applicant_id']) ?>">
                                        <input type="hidden" name="action" value="reject">
                                    </form>
                                    <button type="button" class="btn btn-sm btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#viewModal" onclick='viewApplicant(<?= htmlspecialchars(json_encode($applicant)); ?>, <?= htmlspecialchars(json_encode($docs ?? [])); ?>)'>View</button>
                                    <button type="button" class="btn btn-sm btn-warning ms-1" data-bs-toggle="modal" data-bs-target="#messageModal" onclick='openMessageModal(<?= htmlspecialchars(json_encode($applicant)); ?>)'>Message</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No pending applicants.</td>
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

<!-- View Applicant Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Applicant Details</h5>
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

    function viewApplicant(applicant, docs) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-6">
                    
                    <p><strong>First Name:</strong> ${applicant.first_name}</p>
                    <p><strong>Last Name:</strong> ${applicant.last_name}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> ${applicant.email}</p>
                    <p><strong>Upload On:</strong> ${new Date(applicant.created_at).toLocaleDateString()}</p>
                </div>
            </div>
        `;
        if (docs && docs.length > 0) {
            html += '<hr><h6><strong>Uploaded Documents:</strong></h6>';
            html += '<div class="row">';
            docs.forEach(doc => {
                const fileExt = doc.file_path.split('.').pop().toLowerCase();
                const isImage = ['jpg','jpeg','png','gif','webp'].includes(fileExt);
                const fileUrl = '../../sc_documents/' + doc.file_path.split('/').pop();
                html += '<div class="col-md-4 mb-3 text-center">';
                if (isImage) html += `<img src="${fileUrl}" alt="${doc.file_name}" style="max-width:100%;height:auto;border-radius:4px;margin-bottom:10px;cursor:pointer;" onclick="showImage('${fileUrl}','${doc.file_name}')" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#imageModal">`;
                else html += `<div class="file-icon" style="width:100%;height:120px;margin-bottom:10px;">${fileExt.toUpperCase()}</div>`;
                html += `<p><small>${doc.file_name}</small></p>`;
                html += '</div>';
            });
            html += '</div>';
        } else {
            html += '<hr><p class="text-muted"><small>No documents uploaded yet.</small></p>';
        }
        document.getElementById('viewModalBody').innerHTML = html;
    }

    function showImage(imageSrc, imageName) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModalLabel').textContent = 'View: ' + imageName;
    }

    function confirmAccept(id) {
        if (!confirm('Accept this applicant?')) return;
        // create and submit form to accept_pending.php
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = 'accept_pending.php';
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

    // handle view dropdown
    document.addEventListener('DOMContentLoaded', function(){
        var sel = document.getElementById('viewSelect');
        if (sel) {
            sel.addEventListener('change', function(){
                if (this.value === 'renewals') {
                    window.location.href = 'renewals.php';
                } else {
                    window.location.href = 'pending.php';
                }
            });
        }
    });
</script>
</body>
</html>
