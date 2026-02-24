<?php
session_start();
require_once '../config/config.php';

$applicant_id = $_POST['applicant_id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (empty($applicant_id)) {
    $_SESSION['message'] = 'Account not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: upload_docs.php');
    exit;
}

$upload_dir = '../sc_documents/';
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf', 'image/jpg'];
$max_size = 5 * 1024 * 1024; // 5MB

$success_count = 0;
$error_msgs = [];

if (!empty($_FILES['documents']['name'][0])) {
    foreach ($_FILES['documents']['name'] as $i => $original_name) {
        $tmp_name = $_FILES['documents']['tmp_name'][$i];
        $type = $_FILES['documents']['type'][$i];
        $size = $_FILES['documents']['size'][$i];

        // Validate type and size
        if (!in_array($type, $allowed_types)) {
            $error_msgs[] = "$original_name: Invalid file type.";
            continue;
        }

        if ($size > $max_size) {
            $error_msgs[] = "$original_name: File too large.";
            continue;
        }

        // Generate unique filename
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_name = uniqid('doc_', true) . '.' . $ext;
        $dest = $upload_dir . $new_name;

        // Move file
        if (move_uploaded_file($tmp_name, $dest)) {
            $stmt = $conn->prepare("INSERT INTO college_documents (account_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $applicant_id, $original_name, $dest); // store original name
            $stmt->execute();
            $stmt->close();
            $success_count++;
        } else {
            $error_msgs[] = "$original_name: Upload failed.";
        }
    }

    // Clear upload deadline if at least one file uploaded
    if ($success_count > 0) {
        $account_id = $_SESSION['id'] ?? null;
        if ($account_id) {
            $upd = $conn->prepare("UPDATE college_account SET upload_deadline = NULL WHERE id = ?");
            $upd->bind_param("i", $account_id);
            $upd->execute();
            $upd->close();

            $_SESSION['message'] = "$success_count file(s) uploaded successfully. Upload deadline cleared.";
            $_SESSION['message_type'] = 'success';
        }
    }

    // Append error messages if any
    if (!empty($error_msgs)) {
        $errors_text = implode(' ', $error_msgs);
        if ($success_count > 0) {
            $_SESSION['message'] .= " However, some files failed: $errors_text";
            $_SESSION['message_type'] = 'warning';
        } else {
            $_SESSION['message'] = $errors_text;
            $_SESSION['message_type'] = 'danger';
        }
    }

} else {
    $_SESSION['message'] = 'No files selected.';
    $_SESSION['message_type'] = 'danger';
}

header('Location: upload_docs.php');
exit;
?>