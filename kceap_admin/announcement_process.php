<?php
session_start();
require_once '../config/config.php';

$title = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$audience = $_POST['audience'] ?? [];

// require only title and message; allow empty audience (will be saved but not sent)
if (empty($title) || empty($message)) {
    $_SESSION['message'] = 'Title and message are required.';
    $_SESSION['message_type'] = 'danger';
    header('Location: announcement.php');
    exit;
}

// handle optional attachment
$attachment_path = null;
if (!empty($_FILES['attachment']['tmp_name'])) {
    $updir = __DIR__ . '/../img/announcements/';
    if (!is_dir($updir)) mkdir($updir, 0755, true);
    $orig = $_FILES['attachment']['name'];
    $ext = pathinfo($orig, PATHINFO_EXTENSION);
    $new = uniqid('ann_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $updir . $new)) {
        $attachment_path = 'img/announcements/' . $new;
    }
}

$record = [
    'title' => $title,
    'message' => $message,
    'audience' => $audience,
    'attachment' => $attachment_path,
    'created_at' => date('Y-m-d H:i:s')
];

// determine sent flag: only send if audience provided
$sent = !empty($audience) ? 1 : 0;

// Insert into DB
$stmt = $conn->prepare("INSERT INTO announcement (title, message, attachment, created_at, sent) VALUES (?, ?, ?, ?, ?)");
$created_at_db = date('Y-m-d H:i:s');
$stmt->bind_param('ssssi', $title, $message, $attachment_path, $created_at_db, $sent);
$stmt->execute();
$stmt->close();

// If send now (audience present), send emails using PHPMailer
$send_count = 0;
$fail_count = 0;
$sent_at = null;
if ($sent === 1) {
    $sent_at = date('Y-m-d H:i:s');
    try {
        $mail = getMailer();
        // Collect recipients by audience
        $recipients = [];
        if (in_array('college', $audience)) {
            $r = $conn->query("SELECT email, first_name, last_name FROM college_account WHERE email IS NOT NULL AND email <> ''");
            if ($r) while ($row = $r->fetch_assoc()) $recipients[] = $row;
        }
        if (in_array('highschool', $audience)) {
            $r = $conn->query("SELECT email, first_name, last_name FROM highschool_account WHERE email IS NOT NULL AND email <> ''");
            if ($r) while ($row = $r->fetch_assoc()) $recipients[] = $row;
        }

        foreach ($recipients as $rcp) {
            try {
                $mail->clearAllRecipients();
                $mail->addAddress($rcp['email'], trim(($rcp['first_name'] ?? '') . ' ' . ($rcp['last_name'] ?? '')));
                $mail->Subject = $title;
                $body = '<p>' . nl2br(htmlspecialchars($message)) . '</p>';
                if ($attachment_path) $mail->addAttachment(__DIR__ . '/../' . $attachment_path);
                $mail->Body = $body;
                $mail->send();
                $send_count++;
            } catch (Exception $e) {
                $fail_count++;
            }
        }
    } catch (Exception $e) {
        // mailer init failed - keep record saved
    }
}

// Keep JSON for UI compatibility
$file = __DIR__ . '/announcements.json';
$data = [];
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true) ?: [];
}
$record_db = $record;
$record_db['sent'] = $sent;
$record_db['sent_at'] = $sent_at;
$data[] = $record_db;
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

$_SESSION['message'] = 'Announcement saved' . ($sent === 1 ? (" and sent to $send_count recipients" . ($fail_count ? ", $fail_count failures." : '.')) : '.');
$_SESSION['message_type'] = 'success';
header('Location: announcement.php');
exit;
?>
