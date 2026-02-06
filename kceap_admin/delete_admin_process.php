<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'])) {
    $username = trim($_GET['username']);

    // Validate input
    if (empty($username)) {
        $_SESSION['message'] = 'Invalid username.';
        $_SESSION['message_type'] = 'danger';
        header('Location: add_admin.php');
        exit;
    }

    // Delete query
    $stmt = $conn->prepare("DELETE FROM admin WHERE username = ?");
    $stmt->bind_param('s', $username);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Admin account deleted successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to delete admin account.';
        $_SESSION['message_type'] = 'danger';
    }

    $stmt->close();
    header('Location: add_admin.php');
    exit;
}

header('Location: add_admin.php');
exit;