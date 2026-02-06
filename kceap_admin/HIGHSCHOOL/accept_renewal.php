<?php
require_once '../../config/config.php';
session_start();

$applicant_id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (empty($applicant_id) || empty($action)) {
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'danger';
    header('Location: renewals.php');
    exit;
}

if ($action === 'accept') {

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Update college account status to ACTIVE
        $update_sql = "UPDATE highschool_account SET status = 'active' WHERE applicant_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $applicant_id);
        $update_stmt->execute();
        $update_stmt->close();

        // 2. Delete renewal documents
        $delete_docs_sql = "DELETE FROM highschool_renew_documents WHERE account_id = ?";
        $delete_docs_stmt = $conn->prepare($delete_docs_sql);
        $delete_docs_stmt->bind_param("i", $applicant_id);
        $delete_docs_stmt->execute();
        $delete_docs_stmt->close();

        // 3. OPTIONAL: Delete renewal record itself (if you have a renewal table)
        // Uncomment ONLY if you want the renewal entry gone
        /*
        $delete_renewal_sql = "DELETE FROM college_renewals WHERE applicant_id = ?";
        $delete_renewal_stmt = $conn->prepare($delete_renewal_sql);
        $delete_renewal_stmt->bind_param("i", $applicant_id);
        $delete_renewal_stmt->execute();
        $delete_renewal_stmt->close();
        */

        // Commit changes
        $conn->commit();

        // Prepare success message and attempt to send notification email
        $baseMessage = 'Renewal applicant accepted and renewal data deleted.';
        $emailNotice = '';

        // fetch applicant email and name
        $infoStmt = $conn->prepare("SELECT email, first_name, last_name FROM highschool_account WHERE applicant_id = ? LIMIT 1");
        if ($infoStmt) {
            $infoStmt->bind_param('i', $applicant_id);
            $infoStmt->execute();
            $infoStmt->bind_result($applicant_email, $applicant_first, $applicant_last);
            if ($infoStmt->fetch()) {
                // send email
                try {
                    $mail = getMailer();
                    if (!empty($applicant_email)) {
                        $mail->addAddress($applicant_email, trim($applicant_first . ' ' . $applicant_last));
                        $mail->Subject = 'KCEAP - Renewal Accepted';
                        $mail->Body = "Dear " . htmlspecialchars($applicant_first) . ",<br><br>Your application renewal has been accepted. Your account status is now <strong>active</strong>.<br><br>Thank you,<br>KCEAP Team";
                        $mail->isHTML(true);
                        $mail->send();
                        $emailNotice = ' Notification email sent to applicant.';
                    }
                } catch (Exception $me) {
                    $emailNotice = ' Notification email failed: ' . ($mail->ErrorInfo ?? $me->getMessage());
                }
            }
            $infoStmt->close();
        }

        $_SESSION['message'] = $baseMessage . $emailNotice;
        $_SESSION['message_type'] = empty($emailNotice) || strpos($emailNotice, 'sent') !== false ? 'success' : 'warning';

    } catch (Exception $e) {
        // Rollback if something fails
        $conn->rollback();

        $_SESSION['message'] = 'Error processing renewal.';
        $_SESSION['message_type'] = 'danger';
    }
}

header('Location: renewals.php');
exit;
?>
