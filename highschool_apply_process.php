<?php
require_once './config/config.php';
require_once './config/apply_control.php';
session_start();

if (!isHighSchoolApplicationEnabled()) {
    $_SESSION['highschool_apply_error'] = "Sorry, high school applications are currently closed.";
    header("Location: highschoolapply.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $school = $_POST['school'];
    $strand = $_POST['strand'];
    $yearLevel = $_POST['yearLevel'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $emailAddress = $_POST['emailAddress'];

    $stmt = $conn->prepare("INSERT INTO highschool_schedule (firstName, middleName, lastName, school, strand, yearLevel, address, phoneNumber, emailAddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $firstName, $middleName, $lastName, $school, $strand, $yearLevel, $address, $phoneNumber, $emailAddress);
    if ($stmt->execute()) {
        $_SESSION['highschool_apply_success'] = 'Your application has been submitted successfully!';
        header('Location: highschoolapply.php');
        exit();
    } else {
        $_SESSION['highschool_apply_error'] = 'There was an error submitting your application.';
        header('Location: highschoolapply.php');
        exit();
    }
}
?>