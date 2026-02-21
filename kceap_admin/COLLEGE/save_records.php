<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        /* ===============================
           1. GET & VALIDATE FORM DATA
        =============================== */
        $first_name   = trim($_POST['first_name']);
        $middle_name  = trim($_POST['middle_name']);
        $last_name    = trim($_POST['last_name']);
        $school       = trim($_POST['school']);
        $course       = trim($_POST['course']);
        $year_level   = trim($_POST['year_level']);
        $address      = trim($_POST['address']);
        $phone_number = trim($_POST['phone_number']);
        $email        = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $semester     = trim($_POST['semester']);
        $status       = trim($_POST['status']);
        $created_at   = date("Y-m-d H:i:s");

        if (!$email) {
            throw new Exception("Invalid email format.");
        }

        $fullName = trim("$first_name $middle_name $last_name");

        /* ===============================
           2. CHECK DUPLICATE EMAIL
        =============================== */
        $check = $conn->prepare("SELECT id FROM college_account WHERE email = ? LIMIT 1");
        if (!$check) throw new Exception($conn->error);

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "Email already exists. Account not created.";
            $_SESSION['message_type'] = "danger";
            header("Location: college_records.php");
            exit();
        }
        $check->close();

        /* ===============================
           3. AUTO-GENERATE PASSWORD
        =============================== */
        $plain_password = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghijkmnopqrstuvwxyz'), 0, 8);
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        /* ===============================
           4. INSERT ACCOUNT (WITHOUT applicant_id)
        =============================== */
        $stmt = $conn->prepare("
            INSERT INTO college_account
            (first_name, middle_name, last_name, school, course, year_level,
             address, phone_number, email, password, semester, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) throw new Exception($conn->error);

        $stmt->bind_param(
            "sssssssssssss",
            $first_name,
            $middle_name,
            $last_name,
            $school,
            $course,
            $year_level,
            $address,
            $phone_number,
            $email,
            $hashed_password,
            $semester,
            $status,
            $created_at
        );

        $stmt->execute();

        /* ===============================
           5. GET AUTO-INCREMENT ID & UPDATE applicant_id
        =============================== */
        $applicant_id = $stmt->insert_id;
        $stmt->close();

        $update = $conn->prepare("UPDATE college_account SET applicant_id = ? WHERE id = ?");
        if (!$update) throw new Exception($conn->error);
        $update->bind_param("ii", $applicant_id, $applicant_id); // same value
        $update->execute();
        $update->close();

        /* ===============================
           6. SEND EMAIL
        =============================== */
        try {
            $login_link = "https://yourdomain.com/login.php"; // Use HTTPS

            $mail = getMailer();
            $mail->isHTML(true);
            $mail->addAddress($email, $fullName);
            $mail->Subject = "Your College Account Login Details";

            $mail->Body = "
                Dear {$fullName},<br><br>
                Your college account has been created.<br><br>
                <b>Email:</b> {$email}<br>
                <b>Password:</b> {$plain_password}<br>
                <b>Applicant ID:</b> {$applicant_id}<br><br>
                Login here:<br>
                <a href='{$login_link}'>{$login_link}</a><br><br>
                Please change your password after login.<br><br>
                KCEAP Team
            ";

            $mail->send();

        } catch (Exception $e) {
            // Email failure won't stop account creation
        }

        $_SESSION['message'] = "Account created successfully. Login credentials sent to email.";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = "danger";
    }

    header("Location: college_records.php");
    exit();
}
?>
