<?php
require_once '../../config/config.php';
session_start();

// Basic protection: only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: college_records.php');
    exit;
}

// Optional: read deadline date (admin supplies date only). Time will default to 23:59:00.
$upload_deadline = null;
if (!empty($_POST['upload_deadline_date'])) {
    $date = $_POST['upload_deadline_date'];
    // Normalize and append end-of-day time
    $upload_deadline = $date . ' 23:59:00';
}

/* ======================================================
   1️⃣ GRADUATE 4TH YEAR - 2ND SEMESTER STUDENTS
====================================================== */

$graduate_sql = "
    SELECT applicant_id, first_name, last_name, email 
    FROM college_account 
    WHERE year_level = '4th Year' 
    AND semester = '2nd semester'
    AND email IS NOT NULL 
    AND email <> ''
";

$graduate_result = $conn->query($graduate_sql);
$graduate_sent = 0;
$graduate_failed = 0;

if ($graduate_result && $graduate_result->num_rows > 0) {

    // Update status to graduate
    $update_graduate_sql = "
        UPDATE college_account 
        SET status = 'graduate' 
        WHERE year_level = '4th Year' 
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
                Your KCEAP scholarship has reached its conclusion as you have successfully completed your 4th year.
                Your scholarship status is now marked as <b>graduate</b>.<br><br>
                We wish you success in your future career.<br><br>
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
   2️⃣ ARCHIVE 4TH YEAR - 2ND SEMESTER STUDENTS
====================================================== */

$archive_sql = "
    SELECT applicant_id, first_name, middle_name, last_name, school, 
           course, year_level, semester, address, phone_number, email, status
    FROM college_account 
    WHERE year_level = '4th Year' 
    AND semester = '2nd semester'
";

$archive_result = $conn->query($archive_sql);
$current_school_year = date('Y') . '-' . (date('Y') + 1);

$archived_count = 0;

if ($archive_result && $archive_result->num_rows > 0) {

    while ($student = $archive_result->fetch_assoc()) {

        // Insert into reports
        $insert_sql = "
            INSERT INTO college_reports 
            (applicant_id, first_name, middle_name, last_name, school, course,
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
                $student['course'],
                $student['year_level'],  // preserved
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

        // Delete from college_account
        $delete_stmt = $conn->prepare(
            "DELETE FROM college_account WHERE applicant_id = ?"
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

// Update statuses to pending; if admin provided a date, set upload_deadline to that date at 23:59:00
if ($upload_deadline) {
    $reset_stmt = $conn->prepare("UPDATE college_account SET status = 'pending', upload_deadline = ?");
    if ($reset_stmt) {
        $reset_stmt->bind_param('s', $upload_deadline);
        $reset_ok = $reset_stmt->execute();
        $reset_stmt->close();
    } else {
        $reset_ok = false;
    }
} else {
    $reset_ok = ($conn->query("UPDATE college_account SET status = 'pending'") === TRUE);
}

if ($reset_ok === TRUE || $reset_ok === 1) {

    $sent = 0;
    $failed = 0;

    $select_sql = "
        SELECT applicant_id, email, first_name, last_name 
        FROM college_account 
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
                // Include deadline text if admin supplied a date
                $deadline_text = '';
                if (!empty($upload_deadline)) {
                    try {
                        $dt = new DateTime($upload_deadline);
                        $deadline_text = '<p><strong>Upload Deadline:</strong> ' . $dt->format('F j, Y \a\t g:i A') . '</p>';
                    } catch (Exception $ex) {
                        $deadline_text = '<p><strong>Upload Deadline:</strong> ' . htmlspecialchars($upload_deadline) . '</p>';
                    }
                }

                $mail->Subject = 'KCEAP - Account Status Reset';
                $mail->Body = "Dear " . htmlspecialchars($row['first_name']) . ",<br><br>" .
                              "Your scholar status has been reset and your renewal is now pending.<br>" .
                              ( $deadline_text ? ("Please upload your documents before the deadline below.<br>") : "" ) .
                              $deadline_text .
                              "<br>Please log in to your account to complete required steps.<br><br>" .
                              "Thank you,<br>KCEAP Team";
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

    if ($archived_count > 0) {
        $msg .= " $archived_count 4th Year, 2nd semester student(s) archived.";
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

    // no upload_deadline info

    $_SESSION['message'] = $msg;
    $_SESSION['message_type'] = ($failed > 0 || $graduate_failed > 0) ? 'warning' : 'success';

} else {

    $_SESSION['message'] = 'Failed to reset statuses: ' . htmlspecialchars($conn->error);
    $_SESSION['message_type'] = 'danger';
}

header('Location: college_records.php');
exit;
?>
