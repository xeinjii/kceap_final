<?php
require_once '../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'edit') {
        $id = $_POST['id'];

        $first = $_POST['first_name'];
        $middle = $_POST['middle_name'];
        $last = $_POST['last_name'];
        $school = $_POST['school'];
        $year = $_POST['year_level'];
        $semester = $_POST['semester'] ?? '';
        $address = $_POST['address'];
        $phone = $_POST['phone_number'];
        $email = $_POST['email_address'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE highschool_account SET first_name=?, middle_name=?, last_name=?, school=?, year_level=?, semester=?, address=?, phone_number=?, email=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssssssi", $first, $middle, $last, $school, $year, $semester, $address, $phone, $email, $status, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Record updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update record.";
            $_SESSION['message_type'] = "danger";
        }

        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM highschool_account WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Record deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to delete record.";
            $_SESSION['message_type'] = "danger";
        }

        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    }
}
?>
