<?php
include '../kceap_admin/header.php';
include '../config/config.php';
session_start();
?>

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
            max-width: 400px;
            width: 100%;
            margin: auto;
        }
        .login-form img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto 1.5rem;
        }
        .login-form h2 {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .password-toggle {
            cursor: pointer;
            user-select: none;
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>

<body>
    
<div class="container">
    <div class="login-form">
        <img src="../img/logo.png" alt="KCEAP Logo">
        <h2>ADMIN LOGIN</h2>
        <form action="login_process.php" method="POST">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <div class="mb-3">
                <div class="form-check password-toggle">
                    <input type="checkbox" class="form-check-input" id="showPassword">
                    <label class="form-check-label" for="showPassword">Show Password</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">
                <span class="material-symbols-outlined align-middle me-2">login</span>
                Login
            </button>
        </form>
    </div>
</div>


<!-- Password Toggle Script -->
<script>
    document.getElementById('showPassword').addEventListener('change', function() {
        const password = document.getElementById('password');
        password.type = this.checked ? 'text' : 'password';
    });
</script>
 <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>

<script src="../script/bootstrap.bundle.min.js"></script>

</body>
</html>