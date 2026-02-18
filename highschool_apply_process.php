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

    // ---------------- SANITIZE INPUTS ----------------
    $firstName     = trim($_POST['firstName'] ?? '');
    $middleName    = trim($_POST['middleName'] ?? '');
    $lastName      = trim($_POST['lastName'] ?? '');
    $school        = trim($_POST['school'] ?? '');
    $strand        = trim($_POST['strand'] ?? '');
    $yearLevel     = trim($_POST['yearLevel'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $phoneNumber   = trim($_POST['phoneNumber'] ?? '');
    $emailAddress  = trim($_POST['emailAddress'] ?? '');

    // Sanitize email
    $emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['highschool_apply_error'] = "Invalid email address format.";
        header("Location: highschoolapply.php");
        exit();
    }

    // ---------------- DUMMY FILTER CONFIG (EMAIL + PHONE ONLY) ----------------
    $blockedEmailUsernames = [
        'test','dummy','admin','user','example','sample'
    ];

    $blockedDomains = [
        'mailinator.com',
        'tempmail.com',
        '10minutemail.com',
        'dispostable.com',
        'guerrillamail.com',
        'yopmail.com'
    ];

    // ---------------- EMAIL VALIDATION ----------------
    function isSuspiciousEmail($email, $blockedEmailUsernames, $blockedDomains) {

        $email = strtolower(trim($email));

        if (!str_contains($email, '@')) {
            return true;
        }

        list($username, $domain) = explode('@', $email);

        // Block disposable domains
        if (in_array($domain, $blockedDomains)) {
            return true;
        }

        // Block numeric-only usernames
        if (preg_match('/^\d+$/', $username)) {
            return true;
        }

        // Block repeated characters like aaaaa@gmail.com
        if (preg_match('/^(.)\1{4,}$/', $username)) {
            return true;
        }

        // Block usernames containing blocked words
        foreach ($blockedEmailUsernames as $word) {
            if (strpos($username, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    // ---------------- PHONE VALIDATION ----------------
    function isSuspiciousPhone($phone) {

        $phone = preg_replace('/\D/', '', $phone);

        // Must start with 09 and be 11 digits
        if (!preg_match('/^09\d{9}$/', $phone)) {
            return true;
        }

        // Block repeated digits
        if (preg_match('/^(.)\1{10}$/', $phone)) {
            return true;
        }

        return false;
    }

    // ---------------- APPLY VALIDATIONS ----------------

    if (isSuspiciousEmail($emailAddress, $blockedEmailUsernames, $blockedDomains)) {
        $_SESSION['highschool_apply_error'] = "Please use a valid email address. Disposable or suspicious emails are not allowed.";
        header("Location: highschoolapply.php");
        exit();
    }

    if (isSuspiciousPhone($phoneNumber)) {
        $_SESSION['highschool_apply_error'] = "Please enter a valid 11-digit mobile number starting with 09.";
        header("Location: highschoolapply.php");
        exit();
    }

    // ---------------- CHECK DUPLICATES ----------------
    $checkSchedule = $conn->prepare("SELECT id FROM highschool_schedule WHERE emailAddress = ?");
    $checkSchedule->bind_param("s", $emailAddress);
    $checkSchedule->execute();
    $checkSchedule->store_result();

    if ($checkSchedule->num_rows > 0) {
        $_SESSION['highschool_apply_error'] = "This email is already used for an application.";
        $checkSchedule->close();
        header("Location: highschoolapply.php");
        exit();
    }
    $checkSchedule->close();

    $checkAccount = $conn->prepare("SELECT id FROM highschool_account WHERE email = ?");
    $checkAccount->bind_param("s", $emailAddress);
    $checkAccount->execute();
    $checkAccount->store_result();

    if ($checkAccount->num_rows > 0) {
        $_SESSION['highschool_apply_error'] = "This email is already registered as a high school account.";
        $checkAccount->close();
        header("Location: highschoolapply.php");
        exit();
    }
    $checkAccount->close();

    // ---------------- INSERT APPLICATION ----------------
    $stmt = $conn->prepare("INSERT INTO highschool_schedule 
        (firstName, middleName, lastName, school, strand, yearLevel, address, phoneNumber, emailAddress) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        'sssssssss',
        $firstName,
        $middleName,
        $lastName,
        $school,
        $strand,
        $yearLevel,
        $address,
        $phoneNumber,
        $emailAddress
    );

    if ($stmt->execute()) {
        $_SESSION['highschool_apply_success'] = "Your application has been submitted successfully!";
    } else {
        $_SESSION['highschool_apply_error'] = "There was an error submitting your application.";
    }

    $stmt->close();
    $conn->close();

    header("Location: highschoolapply.php");
    exit();
}
?>
