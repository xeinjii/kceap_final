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
        $course = $_POST['course'];
        $year = $_POST['year_level'];
        $address = $_POST['address'];
        $phone = $_POST['phone_number'];
        $email = $_POST['email'];
        $status = $_POST['status'];
        $semester = $_POST['semester'] ?? '';

        $stmt = $conn->prepare("UPDATE college_account SET first_name=?, middle_name=?, last_name=?, school=?, course=?, year_level=?, address=?, phone_number=?, email=?, status=?, semester=? WHERE applicant_id=?");
        $stmt->bind_param("sssssssssssi", $first, $middle, $last, $school, $course, $year, $address, $phone, $email, $status, $semester, $id);

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

        $stmt = $conn->prepare("DELETE FROM college_account WHERE applicant_id=?");
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
