<?php
require_once './config/config.php';
require_once './config/apply_control.php';
session_start();

if (!isCollegeApplicationEnabled()) {
    $_SESSION['college_apply_error'] = "Sorry, college applications are currently closed.";
    header("Location: collegeapply.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $school = $_POST['school'];
    $course = $_POST['course'];
    $yearLevel = $_POST['yearLevel'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phoneNumber'];
    $emailAddress = $_POST['emailAddress'];

    $stmt = $conn->prepare("INSERT INTO college_schedule (firstName, middleName, lastName, school, course, yearLevel, address, phoneNumber, emailAddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $firstName, $middleName, $lastName, $school, $course, $yearLevel, $address, $phoneNumber, $emailAddress);
    if ($stmt->execute()) {
        $_SESSION['college_apply_success'] = 'Your application has been submitted successfully!';
        header('Location: collegeapply.php');
        exit();
    } else {
        $_SESSION['college_apply_error'] = 'There was an error submitting your application.';
        header('Location: collegeapply.php');
        exit();
    }
    
  
}
?>