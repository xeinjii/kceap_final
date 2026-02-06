<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

// determine account id from session or posted value (session preferred)
$account_id = $_SESSION['id'] ?? $_SESSION['highschool_user']['account_id'] ?? $_SESSION['account_id'] ?? $_POST['account_id'] ?? null;
$account_id = (int)$account_id;

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (empty($account_id) || $current === '' || $new === '' || $confirm === '') {
    $_SESSION['message'] = 'All fields are required. (Account ID: ' . $account_id . ')';
    $_SESSION['message_type'] = 'danger';
    header('Location: profile.php');
    exit;
}

if ($new !== $confirm) {
    $_SESSION['message'] = 'New password and confirmation do not match.';
    $_SESSION['message_type'] = 'danger';
    header('Location: profile.php');
    exit;
}

if (strlen($new) < 8) {
    $_SESSION['message'] = 'New password must be at least 8 characters.';
    $_SESSION['message_type'] = 'danger';
    header('Location: profile.php');
    exit;
}

// fetch hashed password
$stmt = $conn->prepare("SELECT password FROM highschool_account WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $account_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    $_SESSION['message'] = 'Account not found.';
    $_SESSION['message_type'] = 'danger';
    header('Location: profile.php');
    exit;
}

$hashed = $row['password'] ?? '';

// verify current password
if (!password_verify($current, $hashed)) {
    $_SESSION['message'] = 'Current password is incorrect.';
    $_SESSION['message_type'] = 'danger';
    header('Location: profile.php');
    exit;
}

// update with new hashed password
$new_hashed = password_hash($new, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE highschool_account SET password = ? WHERE id = ?");

if (!$upd) {
    $_SESSION['message'] = 'Database error: ' . $conn->error;
    $_SESSION['message_type'] = 'danger';
    header('Location: profile.php');
    exit;
}

$upd->bind_param("si", $new_hashed, $account_id);
$ok = $upd->execute();

if (!$ok) {
    $_SESSION['message'] = 'Update failed: ' . $upd->error;
    $_SESSION['message_type'] = 'danger';
    $upd->close();
    header('Location: profile.php');
    exit;
}

$affected_rows = $upd->affected_rows;
$upd->close();

if ($affected_rows > 0) {
    $_SESSION['message'] = 'Password updated successfully.';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Unable to update password. No rows affected. (ID: ' . $account_id . ')';
    $_SESSION['message_type'] = 'danger';
}

header('Location: profile.php');
exit;
?>
