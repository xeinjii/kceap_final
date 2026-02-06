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
    foreach ($_FILES['documents']['name'] as $i => $name) {
        $tmp_name = $_FILES['documents']['tmp_name'][$i];
        $type = $_FILES['documents']['type'][$i];
        $size = $_FILES['documents']['size'][$i];
        if (!in_array($type, $allowed_types)) {
            $error_msgs[] = "$name: Invalid file type.";
            continue;
        }
        if ($size > $max_size) {
            $error_msgs[] = "$name: File too large.";
            continue;
        }
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $new_name = uniqid('doc_', true) . '.' . $ext;
        $dest = $upload_dir . $new_name;
        if (move_uploaded_file($tmp_name, $dest)) {
            $stmt = $conn->prepare("INSERT INTO college_documents (account_id, file_name, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $applicant_id, $name, $dest);
            $stmt->execute();
            $stmt->close();
            $success_count++;
        } else {
            $error_msgs[] = "$name: Upload failed.";
        }
    }
    if ($success_count > 0) {
        $_SESSION['message'] = "$success_count file(s) uploaded successfully.";
        $_SESSION['message_type'] = 'success';
    }
    if (!empty($error_msgs)) {
        $_SESSION['message'] = implode(' ', $error_msgs);
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = 'No files selected.';
    $_SESSION['message_type'] = 'danger';
}
header('Location: upload_docs.php');
exit;
?>