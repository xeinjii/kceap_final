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


// Update 4th Year, 2nd semester students to 'graduate' status and send graduation email
$graduate_sql = "
    SELECT applicant_id, first_name, last_name, email 
    FROM college_account 
    WHERE year_level = '4th Year' AND semester = '2nd semester' AND email IS NOT NULL AND email <> ''
";

$graduate_result = $conn->query($graduate_sql);
$graduate_sent = 0;
$graduate_failed = 0;

if ($graduate_result && $graduate_result->num_rows > 0) {
    // Update status to graduate
    $update_graduate_sql = "UPDATE college_account SET status = 'graduate' WHERE year_level = '4th Year' AND semester = '2nd semester'";
    $conn->query($update_graduate_sql);
    
    // Send graduation expiration emails
    $graduate_result = $conn->query($graduate_sql); // Re-query after update
    while ($graduate = $graduate_result->fetch_assoc()) {
        $toEmail = $graduate['email'];
        $toName = trim($graduate['first_name'] . ' ' . $graduate['last_name']);
        
        try {
            $mail = getMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = 'KCEAP Scholarship - Status Update: Graduated';
            $mail->Body = "Dear " . htmlspecialchars($graduate['first_name']) . ",<br><br>Congratulations on your graduation!<br><br>We are pleased to inform you that your KCEAP scholarship has reached its conclusion as you have successfully completed your 4th year. Your scholarship status is now marked as <b>graduate</b>.<br><br>We wish you all the best in your future endeavors and career.<br><br>Thank you for being part of the KCEAP Scholarship Program.<br><br>Sincerely,<br>KCEAP Team";
            $mail->isHTML(true);
            $mail->send();
            $graduate_sent++;
        } catch (Exception $e) {
            $graduate_failed++;
        }
    }
}

// First, identify and archive 4th Year, 2nd semester students
$archive_sql = "
    SELECT applicant_id, first_name, middle_name, last_name, school, course, year_level, semester, 
           address, phone_number, email, status
    FROM college_account 
    WHERE year_level = '4th Year' AND semester = '2nd semester'
";

$archive_result = $conn->query($archive_sql);
$current_school_year = date('Y') . '-' . (date('Y') + 1); // e.g., 2025-2026

if ($archive_result && $archive_result->num_rows > 0) {
    while ($student = $archive_result->fetch_assoc()) {
        // Insert into college_reports
        $insert_report_sql = "
            INSERT INTO college_reports (applicant_id, first_name, middle_name, last_name, school, course, 
                                    year_level, semester, address, phone_number, email, status, school_year)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $insert_stmt = $conn->prepare($insert_report_sql);
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
                $student['year_level'],
                $student['semester'],
                $student['address'],
                $student['phone_number'],
                $student['email'],
                $graduated_status,
                $current_school_year
            );
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        
        // Delete from college_account
        $delete_sql = "DELETE FROM college_account WHERE applicant_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if ($delete_stmt) {
            $delete_stmt->bind_param('i', $student['applicant_id']);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    }
}

// Reset remaining students to 'pending' and update year_level to 'graduating' if 4th Year
$sql = "UPDATE college_account SET status = 'pending', year_level = CASE 
            WHEN year_level = '4th Year' THEN 'graduating'
            ELSE year_level 
        END";

if ($conn->query($sql) === TRUE) {
    // After successful reset, notify remaining applicants via email
    $sent = 0;
    $failed = 0;
    $failedList = [];

    $select_sql = "SELECT applicant_id, email, first_name, last_name FROM college_account WHERE email IS NOT NULL AND email <> ''";
    if ($result = $conn->query($select_sql)) {
        while ($row = $result->fetch_assoc()) {
            $toEmail = $row['email'];
            $toName = trim($row['first_name'] . ' ' . $row['last_name']);

            try {
                $mail = getMailer();
                $mail->addAddress($toEmail, $toName);
                $mail->Subject = 'KCEAP - Account Status Reset';
                $mail->Body = "Dear " . htmlspecialchars($row['first_name']) . ",<br><br>Your scholar status has been reset and your renewal is now pending. Please log in to your account to complete any required steps.<br><br>Thank you,<br>KCEAP Team";
                $mail->isHTML(true);
                $mail->send();
                $sent++;
            } catch (Exception $e) {
                $failed++;
                $failedList[] = $toEmail . ' (' . ($mail->ErrorInfo ?? $e->getMessage()) . ')';
            }
        }
        $result->free();
    }

    $msg = 'All applicant statuses have been reset to pending.';
    if ($archive_result && $archive_result->num_rows > 0) {
        $msg .= ' ' . $archive_result->num_rows . ' 4th Year, 2nd semester student(s) archived to reports.';
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
    $_SESSION['message_type'] = (($failed > 0) || ($graduate_failed > 0)) ? 'warning' : 'success';
} else {
    $_SESSION['message'] = 'Failed to reset statuses: ' . htmlspecialchars($conn->error);
    $_SESSION['message_type'] = 'danger';
}

header('Location: college_records.php');
exit;
?>