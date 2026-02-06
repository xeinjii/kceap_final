<?php
require_once '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: pending.php');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($id <= 0 || $action !== 'accept') {
    $_SESSION['message'] = 'Invalid applicant ID or action.';
    $_SESSION['message_type'] = 'danger';
    header('Location: pending.php');
    exit;
}

// Fetch applicant info
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM college_account WHERE applicant_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$applicant = $res->fetch_assoc();
$stmt->close();

if (!$applicant) {
    $_SESSION['message'] = 'Applicant not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: pending.php');
    exit;
}

// Begin transaction
$conn->begin_transaction();
try {
    // Update status to active
    $upd = $conn->prepare("UPDATE college_account SET status = 'active' WHERE applicant_id = ?");
    $upd->bind_param('i', $id);
    $upd->execute();
    $upd->close();

    // Get documents to delete files
    $docStmt = $conn->prepare("SELECT id, file_path FROM college_documents WHERE account_id = ?");
    $docStmt->bind_param('i', $id);
    $docStmt->execute();
    $docs = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $docStmt->close();

    // Delete document records
    $delDocs = $conn->prepare("DELETE FROM college_documents WHERE account_id = ?");
    $delDocs->bind_param('i', $id);
    $delDocs->execute();
    $delDocs->close();

    // Commit DB changes before file operations
    $conn->commit();

    // Delete physical files from sc_documents
    foreach ($docs as $d) {
        $filename = basename($d['file_path']);
        $filepath = __DIR__ . '/../../sc_documents/' . $filename;
        if (file_exists($filepath)) {
            @unlink($filepath);
        }
    }

    // Send email notification
    try {
        $mail = getMailer();
        $mail->addAddress($applicant['email'], $applicant['first_name'] . ' ' . $applicant['last_name']);
        $mail->Subject = 'KCEAP Application Accepted';

        $body = '<p>Dear ' . htmlspecialchars($applicant['first_name']) . ' ' . htmlspecialchars($applicant['last_name']) . ',</p>';
        $body .= '<p>Congratulations — your application has been accepted and your account is now active. You may now log in to the KCEAP portal to continue.</p>';
        $body .= '<p>Regards,<br/>KCEAP Admissions</p>';

        $mail->Body = $body;
        $mail->AltBody = "Dear {$applicant['first_name']} {$applicant['last_name']},\n\nYour application has been accepted and your account is now active.";

        $mail->send();
        $_SESSION['message'] = 'Applicant accepted, email sent, and documents removed.';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        // Mail failed but process succeeded
        $_SESSION['message'] = 'Applicant accepted. Email could not be sent.';
        $_SESSION['message_type'] = 'warning';
    }

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = 'Failed to accept applicant: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header('Location: pending.php');
exit;

?>
<?php
require_once '../../config/config.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pending_applicants.php");
    exit;
}

$applicant_id = intval($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($applicant_id <= 0) {
    $_SESSION['message'] = "Invalid applicant selected.";
    $_SESSION['message_type'] = "danger";
    header("Location: pending_applicants.php");
    exit;
}

if ($action === 'accept') {

    if (acceptApplicant($conn, $applicant_id)) {
        // After successful activation, try to notify applicant via email
        if (notifyApplicant($conn, $applicant_id)) {
            $_SESSION['message'] = "Applicant has been ACCEPTED, activated, and notified by email.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Applicant has been ACCEPTED and activated. Email notification failed.";
            $_SESSION['message_type'] = "warning";
        }
    } else {
        $_SESSION['message'] = "Failed to accept applicant.";
        $_SESSION['message_type'] = "danger";
    }
}

header("Location: pending_applicants.php");
exit;


/* =========================
   FUNCTION
========================= */
function acceptApplicant($conn, $applicant_id)
{
    $sql = "UPDATE college_account 
            SET status = 'Active'
            WHERE applicant_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    return $stmt->execute();
}

/**
 * Fetch applicant contact info and send acceptance email.
 * Returns true if mail was successfully sent (or at least mail() returned true).
 */
function notifyApplicant($conn, $applicant_id)
{
    $sql = "SELECT email, first_name, last_name FROM college_account WHERE applicant_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return false;
    $stmt->bind_param("i", $applicant_id);
    if (!$stmt->execute())
        return false;
    $res = $stmt->get_result();
    if ($res->num_rows === 0)
        return false;
    $row = $res->fetch_assoc();
    $to = filter_var($row['email'], FILTER_SANITIZE_EMAIL);
    $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));

    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $subject = "Application Accepted - KCEAP";
    $message = "Dear " . (!empty($name) ? $name : "Applicant") . ",\n\n";
    $message .= "Congratulations — your application has been accepted. Your account is now active.\n\n";
    $message .= "You may now log in to your account to continue the process.\n\n";
    $message .= "Regards,\nKCEAP Team";

    $headers = "From: noreply@kceap.local\r\n" .
        "Reply-To: noreply@kceap.local\r\n" .
        "X-Mailer: PHP/" . phpversion();

    // Use PHP mail() for simplicity; if you have PHPMailer configured you can replace this.
    return mail($to, $subject, $message, $headers);
}
