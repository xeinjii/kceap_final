<?php
session_start();
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: set_schedule.php');
    exit;
}

// Collect POST data safely
$applicantId   = isset($_POST['id']) ? (int)$_POST['id'] : null;
$firstName     = trim($_POST['firstName'] ?? '');
$middleName    = trim($_POST['middleName'] ?? '');
$lastName      = trim($_POST['lastName'] ?? '');
$school        = trim($_POST['school'] ?? '');
$strand        = trim($_POST['strand'] ?? '');
$yearLevel     = trim($_POST['yearLevel'] ?? '');
$address       = trim($_POST['address'] ?? '');
$phoneNumber   = trim($_POST['phoneNumber'] ?? '');
$emailAddress  = trim($_POST['emailAddress'] ?? '');
$scheduleDate  = trim($_POST['date'] ?? '');
$scheduleTime  = trim($_POST['time'] ?? '');

if (empty($applicantId) || empty($scheduleDate) || empty($scheduleTime)) {
    $_SESSION['message'] = 'Invalid input. Please provide schedule date/time.';
    $_SESSION['message_type'] = 'danger';
    header('Location: set_schedule.php');
    exit;
}

// helper: random password
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $pwd = '';
    for ($i = 0; $i < $length; $i++) {
        $pwd .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $pwd;
}

$auto_password = generatePassword(10);
$hashed_password = password_hash($auto_password, PASSWORD_DEFAULT);
$status = 'waiting for results'; // account status after accept

try {
    $conn->begin_transaction();

    // 1) Insert into highschool_schedule_list
    $stmt = $conn->prepare("INSERT INTO highschool_schedule_list 
        (applicant_id, first_name, middle_name, last_name, school, strand, year_level, address, phone_number, email_address, schedule_date, schedule_time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) throw new Exception($conn->error);
    $stmt->bind_param(
        "isssssssssss",
        $applicantId, $firstName, $middleName, $lastName, $school, $strand, $yearLevel, $address, $phoneNumber, $emailAddress, $scheduleDate, $scheduleTime
    );
    if (! $stmt->execute()) throw new Exception($stmt->error);
    $stmt->close();

    // 2) Delete original applicant from highschool_schedule
    $deleteStmt = $conn->prepare("DELETE FROM highschool_schedule WHERE id = ?");
    if ($deleteStmt === false) throw new Exception($conn->error);
    $deleteStmt->bind_param("i", $applicantId);
    if (! $deleteStmt->execute()) throw new Exception($deleteStmt->error);
    $deleteStmt->close();

    // 3) Insert or update highschool_account to avoid duplicate email error
    $check = $conn->prepare("SELECT id FROM highschool_account WHERE email = ? LIMIT 1");
    if ($check === false) throw new Exception($conn->error);
    $check->bind_param("s", $emailAddress);
    if (! $check->execute()) throw new Exception($check->error);
    $check->store_result();
    $duplicate_exists = ($check->num_rows > 0);
    $existingAccountId = null;
    if ($duplicate_exists) {
        $check->bind_result($existingAccountId);
        $check->fetch();
    }
    $check->close();

    if ($duplicate_exists && $existingAccountId) {
        // update existing account
        $update = $conn->prepare("UPDATE highschool_account SET
            applicant_id = ?, first_name = ?, middle_name = ?, last_name = ?, school = ?, strand = ?, year_level = ?,
            address = ?, phone_number = ?, schedule_date = ?, schedule_time = ?, status = ?, password = ?
            WHERE id = ?");
        if ($update === false) throw new Exception($conn->error);
        $update->bind_param(
            "issssssssssssi",
            $applicantId, $firstName, $middleName, $lastName, $school, $strand, $yearLevel, $address, $phoneNumber, $scheduleDate, $scheduleTime, $status, $hashed_password, $existingAccountId
        );
        if (! $update->execute()) throw new Exception($update->error);
        $update->close();
    } else {
        // insert new account
        $acc_insert = $conn->prepare("INSERT INTO highschool_account 
            (applicant_id, first_name, middle_name, last_name, school, strand, year_level, address, phone_number, email, schedule_date, schedule_time, status, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($acc_insert === false) throw new Exception($conn->error);
        $acc_insert->bind_param(
            "isssssssssssss",
            $applicantId, $firstName, $middleName, $lastName, $school, $strand, $yearLevel, $address, $phoneNumber, $emailAddress, $scheduleDate, $scheduleTime, $status, $hashed_password
        );
        if (! $acc_insert->execute()) throw new Exception($acc_insert->error);
        $acc_insert->close();
    }

    $conn->commit();

    // 4) Send email with credentials (best-effort)
    try {
        $mail = getMailer();
        $mail->addAddress($emailAddress, $firstName . ' ' . $lastName);
        $mail->Subject = 'KCEAP Scholarship Exam Schedule';
        $mail->isHTML(true);
        $mail->Body = "Dear " . htmlspecialchars($firstName) . ",<br><br>"
            . "Your examination for the KCEAP Scholarship has been scheduled and you have been accepted.<br><br>"
            . "<b>Date:</b> " . htmlspecialchars($scheduleDate) . "<br>"
            . "<b>Time:</b> " . htmlspecialchars($scheduleTime) . "<br>"
            . "<b>Login Email:</b> " . htmlspecialchars($emailAddress) . "<br>"
            . "<b>Password:</b> " . htmlspecialchars($auto_password) . "<br><br>"
            . "Your account status is set to <strong>incomplete</strong>. Please log in and complete the required information.<br><br>"
            . "You can log in using the following link:<br>"
            . "<a href='https://example.com/login'>https://example.com/login</a><br><br>"
            . "Sincerely,<br>KCEAP Team";
        $mail->send();

        $_SESSION['message'] = "Schedule set, applicant removed, account created/updated and email sent.";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        $_SESSION['message'] = "Schedule set, applicant removed and account created/updated, but email failed to send.";
        $_SESSION['message_type'] = "warning";
    }

} catch (Exception $e) {
    if ($conn->errno) $conn->rollback();
    $_SESSION['message'] = "Failed to accept applicant: " . htmlspecialchars($e->getMessage());
    $_SESSION['message_type'] = "danger";
}

header("Location: set_schedule.php");
exit;
?>
