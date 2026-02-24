<?php
// CLI script: checks upload deadlines and expires accounts + sends notification emails.
// Usage (CLI): php scripts/check_deadlines_cron.php
// Schedule with Windows Task Scheduler or cron.

date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../config/config.php';

if (php_sapi_name() !== 'cli') {
    // Allow web runs only with a secret key to avoid accidental public execution
    if (empty($_GET['key']) || $_GET['key'] !== (defined('DEADLINE_CHECK_KEY') ? DEADLINE_CHECK_KEY : '')) {
        http_response_code(403);
        echo "Forbidden\n";
        exit;
    }
}

$now = new DateTime();

$tables = [
    [
        'table' => 'college_account',
        'id_field' => 'id',
        'email_field' => 'email',
        'first' => 'first_name',
        'middle' => 'middle_name',
        'last' => 'last_name'
    ],
    [
        'table' => 'highschool_account',
        'id_field' => 'id',
        'email_field' => 'email',
        'first' => 'first_name',
        'middle' => 'middle_name',
        'last' => 'last_name'
    ]
];

foreach ($tables as $t) {
    $table = $t['table'];
    $idf = $t['id_field'];
    $emailf = $t['email_field'];
    $first = $t['first'];
    $middle = $t['middle'];
    $last = $t['last'];

    $sql = "SELECT {$idf}, {$emailf}, upload_deadline, status, {$first}, {$middle}, {$last} FROM {$table} WHERE upload_deadline IS NOT NULL AND upload_deadline <> '' AND status <> 'expired' AND upload_deadline < NOW()";

    if ($res = $conn->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $id = $row[$idf];
            $email = $row[$emailf];
            $upload_deadline = $row['upload_deadline'];
            $status = $row['status'];
            $fullName = trim(($row[$first] ?? '') . ' ' . ($row[$middle] ?? '') . ' ' . ($row[$last] ?? ''));

            // Update status to expired
            $upd = $conn->prepare("UPDATE {$table} SET status = 'expired' WHERE {$idf} = ?");
            if ($upd) {
                $upd->bind_param('i', $id);
                $upd->execute();
                $upd->close();
            }

            // Send notification email
            try {
                $mail = getMailer();
                $mail->clearAllRecipients();
                $mail->addAddress($email, $fullName ?: $email);
                $mail->Subject = 'KCEAP Upload Deadline Expired';
                $deadline_text = (new DateTime($upload_deadline))->format('F j, Y \a\t g:i A');
                $mail->Body = "<p>Dear <strong>{$fullName}</strong>,</p>\n" .
                              "<p>Your upload deadline has passed. Your status is now <b>expired</b> in our records.</p>\n" .
                              "<p>Deadline was: {$deadline_text}</p>\n" .
                              "<p>If you need assistance, contact the administrator.</p>\n" .
                              "<p>Sincerely,<br>KCEAP Team</p>";
                $mail->send();
                echo "Notified: {$email} ({$table})\n";
            } catch (Exception $e) {
                error_log("Email not sent to {$email}: " . ($mail->ErrorInfo ?? $e->getMessage()));
                echo "Failed to notify: {$email} ({$table})\n";
            }
        }
        $res->free();
    } else {
        error_log("Deadline checker query failed for {$table}: " . $conn->error);
    }
}

echo "Done. " . date('Y-m-d H:i:s') . "\n";

// Close DB connection if present
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}

?>
