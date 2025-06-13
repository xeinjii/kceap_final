<?php
session_start();
include './config/config.php'; // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $school = $_POST['school'] ?? '';
    $level = $_POST['level'] ?? '';
    $address = $_POST['address'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';

    // Sanitize and validate input (basic example)
    $firstName = htmlspecialchars(trim($firstName));
    $middleName = htmlspecialchars(trim($middleName));
    $lastName = htmlspecialchars(trim($lastName));
    $school = htmlspecialchars(trim($school));
    $level = htmlspecialchars(trim($level));
    $address = htmlspecialchars(trim($address));
    $phoneNumber = htmlspecialchars(trim($phoneNumber));
    $emailAddress = filter_var($emailAddress, FILTER_VALIDATE_EMAIL);

    if ($firstName && $lastName && $school && $level && $address && $phoneNumber && $emailAddress) {
        // Prepare SQL insert
        $stmt = $conn->prepare("INSERT INTO applicants (firstname, middlename, lastname, school, lvl, address, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstName, $middleName, $lastName, $school, $level, $address, $phoneNumber, $emailAddress);

        if ($stmt->execute()) {
           $_SESSION['success'] = "Application submitted successfully!";
            header("Location: index.php"); // Redirect to a success page
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
         $_SESSION['failed'] = "Application submission failed. Please fill in all fields correctly.";
            header("Location: index.php"); // Redirect to a success page
            exit();
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>