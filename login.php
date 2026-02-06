<?php
session_start();
include './config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCEAP - User Login</title>
    <link rel="icon" href="./img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

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

        .alert {
            margin-bottom: 1.5rem;
        }

        .back-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-form">
            <img src="./img/logo.png" alt="KCEAP Logo">
            <h2>USER LOGIN</h2>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['login_error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form action="login_process.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email address"
                        required>
                    <label for="email">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                        required>
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

            <div class="back-link">
                <a href="../index.php">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.getElementById('showPassword').addEventListener('change', function () {
            const password = document.getElementById('password');
            password.type = this.checked ? 'text' : 'password';
        });
    </script>

   

    <script src="../script/bootstrap.bundle.min.js"></script>
</body>

</html>