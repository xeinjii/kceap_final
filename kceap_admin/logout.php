<?php
// kceap_admin/logout.php
session_start();

// Require confirmation via POST to avoid accidental logouts
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['confirm'])) {
    // If not confirmed, redirect back to dashboard
    header('Location: dashboard.php');
    exit;
}

// Determine where to redirect after logout (decision making)
$redirect = 'index.php'; // default site landing
if (isset($_SESSION['admin_id'])) {
    $redirect = 'index.php'; // admin login page in this folder
} 

// Destroy session cleanly
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

// Redirect with a query flag (optional) so the login page may show a message
header('Location: ' . $redirect . '?logged_out=1');
exit;

?>