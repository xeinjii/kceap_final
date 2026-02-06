<?php
session_start();
include '../config/config.php';

if (isset($_POST['submit'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure password hashing

    // Sample SQL Insert
    $sql = "INSERT INTO admin (fullname, username, password) 
            VALUES ('$fullname', '$username', '$password')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Account added successfully!";
        header("Location: add_admin.php");
    } else {
         $_SESSION['error'] = "Error adding account: " . mysqli_error($conn);
        header("Location: add_admin.php");
    }
}
?>
