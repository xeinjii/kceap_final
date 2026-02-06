<?php
session_start();
include './config/config.php'; // DB connection
require_once './config/apply_control.php';

if (!isApplyButtonEnabled()) {
    $_SESSION['applyfailed'] = "Sorry, applications are currently closed.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect inputs
    $firstName    = $_POST['firstName']    ?? '';
    $middleName   = $_POST['middleName']   ?? '';
    $lastName     = $_POST['lastName']     ?? '';
    $school       = $_POST['school']       ?? '';
    $course       = $_POST['course']       ?? '';   // for college
    $strand       = $_POST['strand']       ?? '';   // for SHS
    $level        = $_POST['level']        ?? '';
    $yearLevel    = $_POST['yearLevel']    ?? '';
    $address      = $_POST['address']      ?? '';
    $phoneNumber  = $_POST['phoneNumber']  ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';

    // Decide which to use
    $course_strand = !empty($course) ? $course : $strand;

    // Sanitize
    $firstName    = htmlspecialchars(trim($firstName));
    $middleName   = htmlspecialchars(trim($middleName));
    $lastName     = htmlspecialchars(trim($lastName));
    $school       = htmlspecialchars(trim($school));
    $course_strand= htmlspecialchars(trim($course_strand));
    $level        = htmlspecialchars(trim($level));
    $yearLevel    = htmlspecialchars(trim($yearLevel));
    $address      = htmlspecialchars(trim($address));
    $phoneNumber  = htmlspecialchars(trim($phoneNumber));
    $emailAddress = filter_var(trim($emailAddress), FILTER_VALIDATE_EMAIL);

    if ($firstName && $lastName && $school && $course_strand && $level && $yearLevel && $address && $phoneNumber && $emailAddress) {

        // ✅ Check for duplicate email
        $check = $conn->prepare("SELECT id FROM record WHERE email = ?");
        $check->bind_param("s", $emailAddress);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['applyfailed'] = "This email ($emailAddress) is already registered. Please use another email or log in.";
            header("Location: index.php");
            exit();
        }
        $check->close();

        // Insert into applicants
        $stmt = $conn->prepare("
            INSERT INTO applicants 
            (firstname, middlename, lastname, school, course_strand, lvl, yearLevel, address, phone, email) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssssssss",
            $firstName,
            $middleName,
            $lastName,
            $school,
            $course_strand,
            $level,
            $yearLevel,
            $address,
            $phoneNumber,
            $emailAddress
        );

        if ($stmt->execute()) {
            $_SESSION['applysuccess'] = "Application submitted successfully!";
        } else {
            $_SESSION['applyfailed'] = "Something went wrong: " . $stmt->error;
        }
        $stmt->close();

        header("Location: index.php");
        exit();

    } else {
        $_SESSION['applyfailed'] = "Application submission failed. Please fill in all fields correctly.";
        header("Location: index.php");
        exit();
    }

} else {
    echo "Invalid request.";
}
