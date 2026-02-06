<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($fullname) || empty($username)) {
        $_SESSION['message'] = 'Full Name and Username are required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: add_admin.php');
        exit;
    }

    // Update query
    $query = "UPDATE admin SET fullname = ?";
    $params = [$fullname];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params[] = $hashedPassword;
    }

    // Ensure the new username is validated and not empty
    $newUsername = trim($_POST['new_username']);
    if (!empty($newUsername)) {
        // Check if the new username already exists
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

        // Update the username in the query
        $query .= ", username = ?";
        $params[] = $newUsername;
    }

    $query .= " WHERE username = ?";
    $params[] = $username;

    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Admin account updated successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to update admin account.';
        $_SESSION['message_type'] = 'danger';
    }

    $stmt->close();
    header('Location: add_admin.php');
    exit;
}

header('Location: add_admin.php');
exit;