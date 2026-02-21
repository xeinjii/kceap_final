<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // 1. GET FORM DATA
        $first_name   = trim($_POST['first_name']);
        $middle_name  = trim($_POST['middle_name']);
        $last_name    = trim($_POST['last_name']);
        $school       = trim($_POST['school']);
        $strand       = trim($_POST['strand']);
        $year_level   = trim($_POST['year_level']);
        $address      = trim($_POST['address']);
        $phone_number = trim($_POST['phone_number']);
        $email        = trim($_POST['email']);
        $semester     = trim($_POST['semester']);
        $status       = trim($_POST['status']);
        $created_at   = date("Y-m-d H:i:s");

        $fullName = trim("$first_name $middle_name $last_name");

        // 2. CHECK DUPLICATE EMAIL
        $check = $conn->prepare("SELECT id FROM highschool_account WHERE email = ? LIMIT 1");
        if (!$check) throw new Exception($conn->error);

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "Email already exists. Account not created.";
            $_SESSION['message_type'] = "danger";
            header("Location: highschool_records.php");
            exit();
        }
        $check->close();

        // 3. AUTO GENERATE PASSWORD
        $plain_password = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghijkmnopqrstuvwxyz'), 0, 8);
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // 4. INSERT RECORD WITHOUT applicant_id (let DB auto-increment)
        $stmt = $conn->prepare("
            INSERT INTO highschool_account
            (first_name, middle_name, last_name, school, strand, year_level,
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
            $strand,
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

        // 5. GET AUTO-INCREMENT ID AS applicant_id
        $applicant_id = $stmt->insert_id;
        $stmt->close();

        // 6. UPDATE applicant_id TO MATCH id
        $update = $conn->prepare("UPDATE highschool_account SET applicant_id = ? WHERE id = ?");
        if (!$update) throw new Exception($conn->error);
        $update->bind_param("ii", $applicant_id, $applicant_id); // same value
        $update->execute();
        $update->close();

        // 7. SEND EMAIL
        try {
            $login_link = "http://yourdomain.com/login.php"; // change this
            $mail = getMailer(); 
            $mail->addAddress($email, $fullName);
            $mail->Subject = "Your High School Account Login Details";

            $mail->Body = "
                Dear {$fullName},<br><br>
                Your High School account has been created.<br><br>
                <b>Email:</b> {$email}<br>
                <b>Password:</b> {$plain_password}<br>
                Login here:<br>
                <a href='{$login_link}'>{$login_link}</a><br><br>
                Please change your password after login.<br><br>
                KCEAP Team
            ";

            $mail->send();
        } catch (Exception $e) {
            // Email failure won't stop account creation
        }

        $_SESSION['message'] = "High School record created successfully. Login credential sent to email.";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = "danger";
    }

    header("Location: highschool_records.php");
    exit();
}
?>
