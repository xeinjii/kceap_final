<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);

    // Validate input
    if (empty($username)) {
        $_SESSION['message'] = 'Invalid username.';
        $_SESSION['message_type'] = 'danger';
        header('Location: add_admin.php');
        exit;
    }

    // Optional: prevent admin from deleting themselves
    if (isset($_SESSION['username']) && $username === $_SESSION['username']) {
        $_SESSION['message'] = 'You cannot delete your own account.';
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
        $_SESSION['message'] = 'Failed to delete admin account: ' . $stmt->error;
        $_SESSION['message_type'] = 'danger';
    }

    $stmt->close();
    header('Location: add_admin.php');
    exit;
}

header('Location: add_admin.php');
exit;
?>
