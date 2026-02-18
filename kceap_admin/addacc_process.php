<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trim and sanitize inputs
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // --- VALIDATION ---

    // Fullname: only letters, spaces, and optionally hyphens/apostrophes
    if (!preg_match("/^[a-zA-Z-' ]+$/", $fullname)) {
        $_SESSION['error'] = "Full Name can only contain letters, spaces, hyphens, and apostrophes.";
        header("Location: add_admin.php");
        exit();
    }

    // Username: alphanumeric and underscores only, 3-20 characters
    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $_SESSION['error'] = "Username must be 3-20 characters and can only contain letters, numbers, and underscores.";
        header("Location: add_admin.php");
        exit();
    }

    // Check if username already exists
    $check = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = "Username already exists!";
        header("Location: add_admin.php");
        exit();
    }

    // Password: hash securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Safe insert using prepared statement
    $stmt = $conn->prepare("INSERT INTO admin (fullname, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Account added successfully!";
    } else {
        $_SESSION['error'] = "Error adding account: " . $stmt->error;
    }

    $stmt->close();
    header("Location: add_admin.php");
    exit();
}
?>
