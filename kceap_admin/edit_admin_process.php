<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trim inputs
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $newUsername = trim($_POST['new_username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // --- VALIDATION ---

    // Full Name: letters, spaces, hyphens, apostrophes
    if (empty($fullname) || !preg_match("/^[a-zA-Z-' ]+$/", $fullname)) {
        $_SESSION['message'] = 'Full Name is required and can only contain letters, spaces, hyphens, and apostrophes.';
        $_SESSION['message_type'] = 'danger';
        header('Location: add_admin.php');
        exit;
    }

    // Username: letters, numbers, underscores, 3-20 chars
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $_SESSION['message'] = 'Current Username is required and can only contain letters, numbers, and underscores (3-20 chars).';
        $_SESSION['message_type'] = 'danger';
        header('Location: add_admin.php');
        exit;
    }

    // New Username validation
    if (!empty($newUsername)) {
        if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $newUsername)) {
            $_SESSION['message'] = 'New Username can only contain letters, numbers, and underscores (3-20 chars).';
            $_SESSION['message_type'] = 'danger';
            header('Location: add_admin.php');
            exit;
        }

        // Check if new username exists
        $checkStmt = $conn->prepare("SELECT username FROM admin WHERE username = ?");
        $checkStmt->bind_param('s', $newUsername);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $_SESSION['message'] = 'The new username is already taken.';
            $_SESSION['message_type'] = 'danger';
            $checkStmt->close();
            header('Location: add_admin.php');
            exit;
        }
        $checkStmt->close();
    }

    // --- BUILD UPDATE QUERY ---
    $query = "UPDATE admin SET fullname = ?";
    $params = [$fullname];
    $types = "s";

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params[] = $hashedPassword;
        $types .= "s";
    }

    if (!empty($newUsername)) {
        $query .= ", username = ?";
        $params[] = $newUsername;
        $types .= "s";
    }

    $query .= " WHERE username = ?";
    $params[] = $username;
    $types .= "s";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Admin account updated successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to update admin account: ' . $stmt->error;
        $_SESSION['message_type'] = 'danger';
    }

    $stmt->close();
    header('Location: add_admin.php');
    exit;
}

// fallback redirect
header('Location: add_admin.php');
exit;
?>
