<?php
session_start();

if (isset($_SESSION['error'])) {
    echo "<script>alert('{$_SESSION['error']}');</script>";
    unset($_SESSION['error']);
}

require_once './config/config.php'; // assumes $conn is your mysqli connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in both fields.";
        header("Location: login.php");
        exit;
    }

    // Query account by email
    $stmt = $conn->prepare("SELECT id, email, password FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result  = $stmt->get_result();
    $account = $result->fetch_assoc();

    // Verify credentials
    if ($account) {
        // If passwords are stored as HASH
        if (password_verify($password, $account['password'])) {
            $_SESSION['admin_id'] = $account['id'];
            $_SESSION['email']   = $account['email'];

            header("Location: ./userpage/index.php");
            exit;
        }

        // If passwords are stored as PLAIN TEXT (not recommended ⚠️)
        /*
        if ($password === $account['password']) {
            $_SESSION['user_id'] = $account['id'];
            $_SESSION['email']   = $account['email'];

            header("Location: dashboard.php");
            exit;
        }
        */
    }

    // If failed
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: login.php");
    exit;
}
