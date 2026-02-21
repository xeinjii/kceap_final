<?php
require_once './config/config.php';
require_once './config/apply_control.php';
session_start();

if (!isCollegeApplicationEnabled()) {
    $_SESSION['college_apply_error'] = "Sorry, college applications are currently closed.";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------------- SANITIZE INPUTS ----------------
    $firstName     = trim($_POST['firstName']);
    $middleName    = trim($_POST['middleName']);
    $lastName      = trim($_POST['lastName']);
    $school        = trim($_POST['school']);
    $course        = trim($_POST['course']);
    $yearLevel     = trim($_POST['yearLevel']);
    $address       = trim($_POST['address']);
    $phoneNumber   = trim($_POST['phoneNumber']);
    $emailAddress  = trim($_POST['emailAddress']);

    $emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);

    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['college_apply_error'] = "Invalid email address format.";
        header("Location: index.php");
        exit();
    }

    // ---------------- DUMMY FILTER ----------------
    $dummyEmails = ['test@gmail.com', 'dummy@gmail.com', 'example@example.com'];
    $dummyDomains = ['mailinator.com', 'tempmail.com', '10minutemail.com', 'dispostable.com'];

    function isSuspiciousEmail($email, $dummyEmails, $dummyDomains) {
        $email = strtolower(trim($email));
        if (in_array($email, $dummyEmails)) return true;
        $domain = substr(strrchr($email, "@"), 1);
        if (in_array($domain, $dummyDomains)) return true;
        $username = strstr($email, '@', true);
        if (preg_match('/^\d+$/', $username)) return true;
        if (preg_match('/^(.)\1+$/', $username)) return true;
        return false;
    }

    function isSuspiciousPhone($phone) {
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) !== 11) return true;
        if (preg_match('/^(.)\1+$/', $phone)) return true;
        if (preg_match('/^09(0{9,}|1{9,}|2{9,})$/', $phone)) return true;
        return false;
    }

    if (isSuspiciousEmail($emailAddress, $dummyEmails, $dummyDomains)) {
        $_SESSION['college_apply_error'] = "Please use a valid email address.";
        header("Location: index.php");
        exit();
    }

    if (isSuspiciousPhone($phoneNumber)) {
        $_SESSION['college_apply_error'] = "Please enter a valid 11-digit phone number.";
        header("Location: index.php");
        exit();
    }

    // ---------------- CHECK DUPLICATES ----------------
    $checkSchedule = $conn->prepare("SELECT id FROM college_schedule WHERE emailAddress = ?");
    $checkSchedule->bind_param("s", $emailAddress);
    $checkSchedule->execute();
    $checkSchedule->store_result();

    if ($checkSchedule->num_rows > 0) {
        $_SESSION['college_apply_error'] = "This email is already used.";
        $checkSchedule->close();
        header("Location: index.php");
        exit();
    }
    $checkSchedule->close();

    $checkAccount = $conn->prepare("SELECT id FROM college_account WHERE email = ?");
    $checkAccount->bind_param("s", $emailAddress);
    $checkAccount->execute();
    $checkAccount->store_result();

    if ($checkAccount->num_rows > 0) {
        $_SESSION['college_apply_error'] = "This email is already registered.";
        $checkAccount->close();
        header("Location: index.php");
        exit();
    }
    $checkAccount->close();

    // ---------------- INSERT ----------------
    $stmt = $conn->prepare("INSERT INTO college_schedule 
        (firstName, middleName, lastName, school, course, yearLevel, address, phoneNumber, emailAddress) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        'sssssssss',
        $firstName,
        $middleName,
        $lastName,
        $school,
        $course,
        $yearLevel,
        $address,
        $phoneNumber,
        $emailAddress
    );

    if ($stmt->execute()) {

        // Default success message
        $_SESSION['college_apply_success'] = 'Your application has been submitted successfully!';

        // ---------------- CHECK LIMIT ----------------
        $deadlineFile = __DIR__ . '/kceap_admin/deadline.json';
        if (file_exists($deadlineFile)) {

            $settings = json_decode(file_get_contents($deadlineFile), true);
            $collegeLimit = $settings['college']['limit'] ?? 0;

            if ($collegeLimit > 0) {
                $countResult = $conn->query("SELECT COUNT(*) AS total FROM college_schedule");
                $currentCount = $countResult->fetch_assoc()['total'];

                if ($currentCount >= $collegeLimit) {
                    $_SESSION['limit_reached'] = 'college';
                    $_SESSION['college_apply_success'] =
                        'Your application has been submitted successfully! College application limit has now been reached.';
                }
            }
        }

        $stmt->close();
        $conn->close();

        header('Location: index.php');
        exit();

    } else {
        $_SESSION['college_apply_error'] = 'There was an error submitting your application.';
        $stmt->close();
        $conn->close();
        header('Location: index.php');
        exit();
    }
}
?>