<?php
require_once '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, applicant_id, first_name, middle_name, last_name, school, year_level, semester, address, phone_number, email, status, password FROM highschool_account WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            
            // 🔐 Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['id'] = $row['id'];
            $_SESSION['user_id'] = $row['applicant_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['middle_name'] = $row['middle_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['school'] = $row['school'];
            $_SESSION['year_level'] = $row['year_level'];
            $_SESSION['semester'] = $row['semester'];
            $_SESSION['address'] = $row['address'];
            $_SESSION['phone_number'] = $row['phone_number'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['status'] = $row['status'];
            $_SESSION['show_welcome'] = true; // Flag to show welcome message once
            $_SESSION['message_type'] = "success";
            header("Location: mainpage.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid password.";
            $_SESSION['message_type'] = "error";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "No account found with that email.";
        $_SESSION['message_type'] = "error";
        header("Location: login.php");
        exit();
    }
}
?>
