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
// Capture semester and year_level from POST
$semester = $_POST['semester'] ?? null;
$year_level = $_POST['year_level'] ?? null;

// Update college_account with semester and year_level
if (!empty($semester) || !empty($year_level)) {
    $updates = [];
    $params = [];
    $types = '';
    
    if (!empty($semester)) {
        $updates[] = "semester = ?";
        $params[] = $semester;
        $types .= 's';
    }
    
    if (!empty($year_level)) {
        $updates[] = "year_level = ?";
        $params[] = $year_level;
        $types .= 's';
    }
    
    if (!empty($updates)) {
        $params[] = $applicant_id;
        $types .= 'i';
        
        $update_sql = "UPDATE college_account SET " . implode(", ", $updates) . " WHERE applicant_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param($types, ...$params);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
}
$upload_dir = '../sc_documents/';
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf', 'image/jpg'];
$max_size = 5 * 1024 * 1024; // 5MB
$success_count = 0;
$error_msgs = [];

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Support both `renewal_documents[]` (renew form) and `documents[]` (upload form)
$files_key = null;
if (!empty($_FILES['renewal_documents']['name'][0])) {
    $files_key = 'renewal_documents';
} elseif (!empty($_FILES['documents']['name'][0])) {
    $files_key = 'documents';
}

if ($files_key) {
    foreach ($_FILES[$files_key]['name'] as $i => $name) {
        $tmp_name = $_FILES[$files_key]['tmp_name'][$i];
        $type = $_FILES[$files_key]['type'][$i];
        $size = $_FILES[$files_key]['size'][$i];
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
            $stmt = $conn->prepare("INSERT INTO college_renew_documents (account_id, file_name, file_path) VALUES (?, ?, ?)");
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
        // Remove upload_deadline after successful submission
        if (!empty($applicant_id)) {
            $u = $conn->prepare("UPDATE college_account SET upload_deadline = NULL WHERE applicant_id = ?");
            if ($u) {
                $u->bind_param('i', $applicant_id);
                $u->execute();
                $u->close();
            }
        }
    }
    if (!empty($error_msgs)) {
        $_SESSION['message'] = implode(' ', $error_msgs);
        $_SESSION['message_type'] = 'warning';
    }
} else {
    $_SESSION['message'] = 'No files selected.';
    $_SESSION['message_type'] = 'danger';
}

// Return to renew page where the user uploaded the files
header('Location: renew.php');
exit;
?>