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

    // Sanitize email
    $emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['college_apply_error'] = "Invalid email address format.";
        header("Location: collegeapply.php");
        exit();
    }

    // ---------------- DUMMY FILTER CONFIG ----------------
    $dummyNames = ['test', 'dummy', 'john', 'doe', 'lorem', 'admin', 'user', 'abc', 'xyz'];
    $dummyEmails = ['test@gmail.com', 'dummy@gmail.com', 'example@example.com'];
    $dummyDomains = ['mailinator.com', 'tempmail.com', '10minutemail.com', 'dispostable.com'];

    // ---------------- FUNCTIONS ----------------
    function isSuspiciousName($name, $dummyNames) {
        $name = strtolower(trim($name));

        // Exact dummy name
        if (in_array($name, $dummyNames)) return true;

        // Part match for multi-word names
        $parts = explode(' ', $name);
        foreach ($parts as $part) {
            if (in_array($part, $dummyNames)) return true;
        }

        // Numeric only
        if (preg_match('/^\d+$/', $name)) return true;

        // Repeated letters (aaaa, bbbb)
        if (preg_match('/^(.)\1+$/', $name)) return true;

        // Too short (1-2 characters)
        if (strlen($name) <= 2) return true;

        return false;
    }

    function isSuspiciousEmail($email, $dummyEmails, $dummyDomains) {
        $email = strtolower(trim($email));

        // Exact dummy email
        if (in_array($email, $dummyEmails)) return true;

        // Disposable domain
        $domain = substr(strrchr($email, "@"), 1);
        if (in_array($domain, $dummyDomains)) return true;

        // Numeric username only
        $username = strstr($email, '@', true);
        if (preg_match('/^\d+$/', $username)) return true;

        // Repeated letters/numbers in username
        if (preg_match('/^(.)\1+$/', $username)) return true;

        return false;
    }

    function isSuspiciousPhone($phone) {
        $phone = preg_replace('/\D/', '', $phone); // Remove non-digits

        // Must be exactly 11 digits
        if (strlen($phone) !== 11) return true;

        // All digits the same (00000000000, 11111111111)
        if (preg_match('/^(.)\1+$/', $phone)) return true;

        // Optional: block common fake prefixes (e.g., 09110000000)
        if (preg_match('/^09(0{9,}|1{9,}|2{9,})$/', $phone)) return true;

        return false;
    }

    // ---------------- APPLY DUMMY FILTERS ----------------
    if (
        isSuspiciousName($firstName, $dummyNames) ||
        isSuspiciousName($middleName, $dummyNames) ||
        isSuspiciousName($lastName, $dummyNames)
    ) {
        $_SESSION['college_apply_error'] = "Please enter valid names. Dummy or suspicious names are not allowed.";
        header("Location: collegeapply.php");
        exit();
    }

    if (isSuspiciousEmail($emailAddress, $dummyEmails, $dummyDomains)) {
        $_SESSION['college_apply_error'] = "Please use a valid email address. Dummy or disposable emails are not allowed.";
        header("Location: collegeapply.php");
        exit();
    }

    if (isSuspiciousPhone($phoneNumber)) {
        $_SESSION['college_apply_error'] = "Please enter a valid 11-digit phone number.";
        header("Location: collegeapply.php");
        exit();
    }

    // ---------------- CHECK DUPLICATES ----------------
    $checkSchedule = $conn->prepare("SELECT id FROM college_schedule WHERE emailAddress = ?");
    $checkSchedule->bind_param("s", $emailAddress);
    $checkSchedule->execute();
    $checkSchedule->store_result();

    if ($checkSchedule->num_rows > 0) {
        $_SESSION['college_apply_error'] = "This email is already used for an application.";
        header("Location: collegeapply.php");
        exit();
    }

    $checkSchedule->close();

    $checkAccount = $conn->prepare("SELECT id FROM college_account WHERE email = ?");
    $checkAccount->bind_param("s", $emailAddress);
    $checkAccount->execute();
    $checkAccount->store_result();

    if ($checkAccount->num_rows > 0) {
        $_SESSION['college_apply_error'] = "This email is already registered as a college account.";
        header("Location: collegeapply.php");
        exit();
    }

    $checkAccount->close();

    // ---------------- INSERT APPLICATION ----------------
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
        $_SESSION['college_apply_success'] = 'Your application has been submitted successfully!';
    } else {
        $_SESSION['college_apply_error'] = 'There was an error submitting your application.';
    }

    $stmt->close();
    $conn->close();

    header('Location: collegeapply.php');
    exit();
}
?>
