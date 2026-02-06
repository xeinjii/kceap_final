<?php
require '../../config/config.php';

if (isset($_POST['submit'])) {
    // Collect submitted data
    $email = $_POST['email'];
    $name = $_POST['name'];
    $date = $_POST['schedule_date'];
    $time = $_POST['schedule_time'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $school = $_POST['school'];
    $lvl = $_POST['lvl'];

    // Insert into exam_schedule table
    $stmt = $conn->prepare("INSERT INTO exam_schedule (name, email, date, time, address, phone, school, lvl) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $date, $time, $address, $phone, $school, $lvl);

    if (!$stmt->execute()) {
        die("Error inserting schedule: " . $stmt->error);
    }

    // Remove from applicants table
    $delStmt = $conn->prepare("DELETE FROM applicants WHERE email = ?");
    $delStmt->bind_param("s", $email);
    if (!$delStmt->execute()) {
        die("Error deleting applicant: " . $delStmt->error);
    }

    // Send email using PHPMailer from config
    try {
        $mail = getMailer();
        $mail->addAddress($email, $name);

        $mail->Subject = 'Examination Schedule Notification';
        $mail->Body    = "
            <p>Good day <strong>$name</strong>,</p>
            <p>You are scheduled for an exam on:</p>
            <ul>
                <li><strong>Date:</strong> $date</li>
                <li><strong>Time:</strong> $time</li>
            </ul>
            <p>Please be on time at the KCEAP Office. Thank you!</p>
        ";

        $mail->send();

        // Success message
        session_start();
        $_SESSION['success_message'] = "Schedule saved and email sent successfully.";
        header("Location: ../highschool_application.php"); // ✅ Always stay in highschool
        exit;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>