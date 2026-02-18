<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Get user from DB
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {

            // 🔐 Prevent session fixation
            session_regenerate_id(true);

            // Set session
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['fullname'];

            // ✅ Redirect (login page removed from history)
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Incorrect password.";
           header("Location: index.php");
        }
    } else {
        $_SESSION['error'] = "Username not found.";
        header("Location: index.php");
    }
}
?>
