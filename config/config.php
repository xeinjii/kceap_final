<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kceap";

$conn = new mysqli($servername, $username, $password, $dbname, 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHPMailer autoload
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to return a configured PHPMailer instance
function getMailer(): PHPMailer {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'belanomattandrei@gmail.com';      // Replace with your email
    $mail->Password   = 'uqpe vcox uzwm zokr';              // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('belanomattandrei@gmail.com', 'KCEAP SIMS');
    $mail->isHTML(true); // Enable HTML emails

    return $mail;
}


