<?php
require_once './config/config.php';
require_once './config/apply_control.php';
session_start();

if (!isHighSchoolApplicationEnabled()) {
    $_SESSION['highschool_apply_error'] = "Sorry, high school applications are currently closed.";
    header("Location: index.php");
    exit();
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

    $emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);

    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['highschool_apply_error'] = "Invalid email address format.";
        header("Location: index.php");
        exit();
    }

    // ---------------- EMAIL VALIDATION ----------------
    $blockedEmailUsernames = ['test','dummy','admin','user','example','sample'];
    $blockedDomains = [
        'mailinator.com','tempmail.com','10minutemail.com',
        'dispostable.com','guerrillamail.com','yopmail.com'
    ];

    function isSuspiciousEmail($email, $blockedEmailUsernames, $blockedDomains) {

        $email = strtolower(trim($email));

        if (!str_contains($email, '@')) return true;

        list($username, $domain) = explode('@', $email);

        if (in_array($domain, $blockedDomains)) return true;

        if (preg_match('/^\d+$/', $username)) return true;

        if (preg_match('/^(.)\1{4,}$/', $username)) return true;

        foreach ($blockedEmailUsernames as $word) {
            if (strpos($username, $word) !== false) return true;
        }

        return false;
    }

    function isSuspiciousPhone($phone) {

        $phone = preg_replace('/\D/', '', $phone);

        if (!preg_match('/^09\d{9}$/', $phone)) return true;

        if (preg_match('/^(.)\1{10}$/', $phone)) return true;

        return false;
    }

    if (isSuspiciousEmail($emailAddress, $blockedEmailUsernames, $blockedDomains)) {
        $_SESSION['highschool_apply_error'] = "Please use a valid email address.";
        header("Location: index.php");
        exit();
    }

    if (isSuspiciousPhone($phoneNumber)) {
        $_SESSION['highschool_apply_error'] = "Please enter a valid 11-digit mobile number starting with 09.";
        header("Location: index.php");
        exit();
    }

    // ---------------- CHECK DUPLICATES ----------------
    $checkSchedule = $conn->prepare("SELECT id FROM highschool_schedule WHERE emailAddress = ?");
    $checkSchedule->bind_param("s", $emailAddress);
    $checkSchedule->execute();
    $checkSchedule->store_result();

    if ($checkSchedule->num_rows > 0) {
        $_SESSION['highschool_apply_error'] = "This email is already used.";
        $checkSchedule->close();
        header("Location: index.php");
        exit();
    }
    $checkSchedule->close();

    $checkAccount = $conn->prepare("SELECT id FROM highschool_account WHERE email = ?");
    $checkAccount->bind_param("s", $emailAddress);
    $checkAccount->execute();
    $checkAccount->store_result();

    if ($checkAccount->num_rows > 0) {
        $_SESSION['highschool_apply_error'] = "This email is already registered.";
        $checkAccount->close();
        header("Location: index.php");
        exit();
    }
    $checkAccount->close();

    // ---------------- INSERT ----------------
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

        // ---------------- CHECK LIMIT ----------------
        $deadlineFile = __DIR__ . '/kceap_admin/deadline.json';

        if (file_exists($deadlineFile)) {

            $settings = json_decode(file_get_contents($deadlineFile), true);
            $hsLimit = $settings['highschool']['limit'] ?? 0;

            if ($hsLimit > 0) {

                $countResult = $conn->query("SELECT COUNT(*) AS total FROM highschool_schedule");
                $currentCount = $countResult->fetch_assoc()['total'];

                if ($currentCount >= $hsLimit) {
                    $_SESSION['limit_reached'] = 'highschool';
                    $_SESSION['highschool_apply_success'] =
                        "Your application has been submitted successfully! High school limit has now been reached.";
                }
            }
        }

        $stmt->close();
        $conn->close();

        header("Location: index.php");
        exit();

    } else {

        $_SESSION['highschool_apply_error'] = "There was an error submitting your application.";

        $stmt->close();
        $conn->close();

        header("Location: index.php");
        exit();
    }
}
?>