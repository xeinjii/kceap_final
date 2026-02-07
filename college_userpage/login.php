<?php
require_once '../config/config.php';
session_start();

// 🚫 If already logged in, NEVER show login page
if (isset($_SESSION['user_id'])) {
    header("Location: mainpage.php");
    exit;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>College Login - KCEAP</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" href="../img/logo.png" type="image/png">
    <link rel="stylesheet" href="../style/bootstrap.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-form {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-width: 420px;
            width: 100%;
            margin: auto;
            text-align: center;
        }
        .login-form img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto 1rem;
        }
        .login-form h2 {
            color: #333;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .password-toggle {
            cursor: pointer;
            user-select: none;
            font-size: 0.875rem;
            color: #6c757d;
        }
        .msg {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-form">
        <img src="../img/logo.png" alt="KCEAP Logo">
        <h2>College Account Login</h2>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="msg alert <?php echo ($_SESSION['message_type'] ?? '') === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message'], $_SESSION['message_type']); 
                ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="post" autocomplete="off" novalidate>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                <label for="email">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <div class="mb-3 text-start">
                <div class="form-check password-toggle">
                    <input type="checkbox" class="form-check-input" id="showPassword">
                    <label class="form-check-label" for="showPassword">Show Password</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                Login
            </button>
        </form>

        <hr class="my-3">

<script src="../script/bootstrap.bundle.min.js"></script>
<script>
    const showPassword = document.getElementById('showPassword');
    if (showPassword) {
        showPassword.addEventListener('change', function() {
            const password = document.getElementById('password');
            if (password) password.type = this.checked ? 'text' : 'password';
        });
    }
</script>

<script>
  history.pushState(null, null, location.href);

  window.addEventListener("popstate", function () {
    history.pushState(null, null, location.href);
  });
</script>

</body>
</html>
