<?php
require_once '../../config/config.php';
session_start();

// Basic protection: only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: highschool_records.php');
    exit;
}

// Capture optional upload deadline (date-only, will be stored as end-of-day)
$upload_deadline = null;
if (isset($_POST['expire_date']) && !empty($_POST['expire_date'])) {
    $expire_date = $_POST['expire_date'];
    $upload_deadline = $expire_date . ' 23:59:00';
}

/* ======================================================
   1️⃣ GRADUATE GRADE 12 - 2ND SEMESTER STUDENTS
====================================================== */

$graduate_sql = "
    SELECT applicant_id, first_name, last_name, email 
    FROM highschool_account 
    WHERE year_level = 'Grade 12' 
    AND semester = '2nd semester'
    AND email IS NOT NULL 
    AND email <> ''
";

$graduate_result = $conn->query($graduate_sql);
$graduate_sent = 0;
$graduate_failed = 0;

if ($graduate_result && $graduate_result->num_rows > 0) {

    // Update status to graduate (DO NOT change year_level)
    $update_graduate_sql = "
        UPDATE highschool_account 
        SET status = 'graduate' 
        WHERE year_level = 'Grade 12' 
        AND semester = '2nd semester'
    ";
    $conn->query($update_graduate_sql);

    // Re-query after update
    $graduate_result = $conn->query($graduate_sql);

    while ($graduate = $graduate_result->fetch_assoc()) {

        $toEmail = $graduate['email'];
        $toName  = trim($graduate['first_name'] . ' ' . $graduate['last_name']);

        try {
            $mail = getMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = 'KCEAP Scholarship - Status Update: Graduated';
            $mail->Body = "
                Dear " . htmlspecialchars($graduate['first_name']) . ",<br><br>
                Congratulations on your graduation!<br><br>
                Your KCEAP scholarship has concluded as you have successfully completed Grade 12.
                Your scholarship status is now marked as <b>graduate</b>.<br><br>
                We wish you success in your higher education journey.<br><br>
                Sincerely,<br>KCEAP Team
            ";
            $mail->isHTML(true);
            $mail->send();
            $graduate_sent++;

        } catch (Exception $e) {
            $graduate_failed++;
        }
    }
}

/* ======================================================
   2️⃣ ARCHIVE GRADE 12 - 2ND SEMESTER STUDENTS
====================================================== */

$archive_sql = "
    SELECT applicant_id, first_name, middle_name, last_name, school, strand,
           year_level, semester, address, phone_number, email, status
    FROM highschool_account 
    WHERE year_level = 'Grade 12' 
    AND semester = '2nd semester'
";

$archive_result = $conn->query($archive_sql);
$current_school_year = date('Y') . '-' . (date('Y') + 1);
$archived_count = 0;

if ($archive_result && $archive_result->num_rows > 0) {

    while ($student = $archive_result->fetch_assoc()) {

        $insert_sql = "
            INSERT INTO hs_reports 
            (applicant_id, first_name, middle_name, last_name, school, strand,
             year_level, semester, address, phone_number, email, status, school_year)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $insert_stmt = $conn->prepare($insert_sql);

        if ($insert_stmt) {

            $graduated_status = 'graduated';

            $insert_stmt->bind_param(
                'issssssssssss',
                $student['applicant_id'],
                $student['first_name'],
                $student['middle_name'],
                $student['last_name'],
                $student['school'],
                $student['strand'],
                $student['year_level'], // preserved
                $student['semester'],
                $student['address'],
                $student['phone_number'],
                $student['email'],
                $graduated_status,
                $current_school_year
            );

            $insert_stmt->execute();
            $insert_stmt->close();
            $archived_count++;
        }

        $delete_stmt = $conn->prepare(
            "DELETE FROM highschool_account WHERE applicant_id = ?"
        );

        if ($delete_stmt) {
            $delete_stmt->bind_param('i', $student['applicant_id']);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    }
}

/* ======================================================
   3️⃣ RESET REMAINING STUDENTS (DO NOT CHANGE YEAR LEVEL)
====================================================== */

// Build update SQL with optional upload_deadline
$reset_sql = "UPDATE highschool_account SET status = 'pending'";
if ($upload_deadline) {
    $reset_sql .= ", upload_deadline = '" . $conn->real_escape_string($upload_deadline) . "'";
}

if ($conn->query($reset_sql) === TRUE) {

    $sent = 0;
    $failed = 0;

    $select_sql = "
        SELECT applicant_id, email, first_name, last_name 
        FROM highschool_account 
        WHERE email IS NOT NULL 
        AND email <> ''
    ";

    if ($result = $conn->query($select_sql)) {

        while ($row = $result->fetch_assoc()) {

            $toEmail = $row['email'];
            $toName  = trim($row['first_name'] . ' ' . $row['last_name']);

            try {
                $mail = getMailer();
                $mail->addAddress($toEmail, $toName);
                $mail->Subject = 'KCEAP - Account Status Reset';
                
                $deadline_text = '';
                if ($upload_deadline) {
                    $deadline_date = new DateTime($upload_deadline);
                    $deadline_text = '<p><strong>Upload Deadline:</strong> ' . 
                                    $deadline_date->format('F j, Y') . '</p>';
                }
                
                $mail->Body = "
                    <p>Dear " . htmlspecialchars($row['first_name']) . ",</p>
                    <p>Your scholar status has been reset and your renewal is now pending.</p>
                    {$deadline_text}
                    <p>Please log in to your account to complete required steps.</p>
                    <p>Thank you,<br>KCEAP Team</p>
                ";
                $mail->isHTML(true);
                $mail->send();
                $sent++;

            } catch (Exception $e) {
                $failed++;
            }
        }

        $result->free();
    }

    $msg = "All applicant statuses have been reset to pending.";

    if ($upload_deadline) {
        $deadline_date = new DateTime($upload_deadline);
        $msg .= " Upload deadline set to " . $deadline_date->format('F j, Y') . ".";
    }

    if ($archived_count > 0) {
        $msg .= " $archived_count Grade 12, 2nd semester student(s) archived.";
    }

    if ($graduate_sent > 0) {
        $msg .= " $graduate_sent graduate notification(s) sent.";
    }

    if ($graduate_failed > 0) {
        $msg .= " $graduate_failed graduate notification(s) failed.";
    }

    if ($sent > 0) {
        $msg .= " $sent renewal notification(s) sent.";
    }

    if ($failed > 0) {
        $msg .= " $failed renewal notification(s) failed.";
    }

    $_SESSION['message'] = $msg;
    $_SESSION['message_type'] = ($failed > 0 || $graduate_failed > 0) ? 'warning' : 'success';

} else {

    $_SESSION['message'] = 'Failed to reset statuses: ' . htmlspecialchars($conn->error);
    $_SESSION['message_type'] = 'danger';
}

header('Location: highschool_records.php');
exit;
?>
