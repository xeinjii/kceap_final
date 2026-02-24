<?php
require_once '../../config/config.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle POST request from modal form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    
    // Get applicant data from college_schedule_list
    $stmt = $conn->prepare("SELECT * FROM college_schedule_list WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();
    $stmt->close();
    
    if (!$applicant) {
        $_SESSION['message'] = "Applicant not found.";
        $_SESSION['message_type'] = "danger";
        header("Location: exam_list.php");
        exit();
    }
    
    $email = $applicant['email_address'];
    $fullName = trim($applicant['first_name'] . ' ' . $applicant['middle_name'] . ' ' . $applicant['last_name']);
    
    // Process upload deadline if provided (date-only). Time defaults to 23:59:00.
    $upload_deadline = null;
    if (isset($_POST['expire_date']) && !empty($_POST['expire_date'])) {
        $expire_date = $_POST['expire_date'];
        // store as end of day for that date
        $upload_deadline = $expire_date . ' 23:59:00';
    }
    
    // Optional message from admin
    $admin_message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Helper: clear schedule fields in college_account by email
    function clearAccountScheduleByEmail($conn, $email) {
        $upd = $conn->prepare("UPDATE college_account SET schedule_date = NULL, schedule_time = NULL WHERE email = ?");
        if ($upd) {
            $upd->bind_param("s", $email);
            $upd->execute();
            $upd->close();
        }
    }
    
    if ($action === 'accept') {
        try {
            // Check if an account with the same email already exists
            $check = $conn->prepare("SELECT id FROM college_account WHERE email = ? LIMIT 1");
            if ($check === false) throw new Exception($conn->error);
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();
            $duplicate_exists = ($check->num_rows > 0);
            if ($duplicate_exists) {
                $check->bind_result($existingAccountId);
                $check->fetch();
            } else {
                $existingAccountId = null;
            }
            $check->close();
            
            // Set semester and status values
            $semester = '1st semester';
            $status = 'incomplete';
            
            if ($duplicate_exists) {
                // Update existing account
                $update = $conn->prepare("
                    UPDATE college_account SET
                        applicant_id = ?, first_name = ?, middle_name = ?, last_name = ?, 
                        school = ?, course = ?, year_level = ?, address = ?, 
                        phone_number = ?, schedule_date = ?, schedule_time = ?, 
                        status = ?, semester = ?, upload_deadline = ?
                    WHERE email = ?
                ");
                if ($update === false) throw new Exception($conn->error);
                $update->bind_param(
                    "issssssssssssss",
                    $applicant['applicant_id'],
                    $applicant['first_name'],
                    $applicant['middle_name'],
                    $applicant['last_name'],
                    $applicant['school'],
                    $applicant['course'],
                    $applicant['year_level'],
                    $applicant['address'],
                    $applicant['phone_number'],
                    $applicant['schedule_date'],
                    $applicant['schedule_time'],
                    $status,
                    $semester,
                    $upload_deadline,
                    $email
                );
                $update->execute();
                $update->close();
                
                // Remove schedule entry
                $delete = $conn->prepare("DELETE FROM college_schedule_list WHERE id = ?");
                $delete->bind_param("i", $id);
                $delete->execute();
                $delete->close();
                
                // Clear schedule fields on the college_account record
                clearAccountScheduleByEmail($conn, $email);
                
                $_SESSION['message'] = "Existing account updated to 'incomplete'. Schedule entry removed.";
                $_SESSION['message_type'] = "info";
            } else {
                // Insert new account
                $insert = $conn->prepare("
                    INSERT INTO college_account 
                    (applicant_id, first_name, middle_name, last_name, school, course, 
                     year_level, address, phone_number, email, schedule_date, schedule_time, 
                     status, semester, upload_deadline) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                if ($insert === false) throw new Exception($conn->error);
                $insert->bind_param(
                    "issssssssssssss",
                    $applicant['applicant_id'],
                    $applicant['first_name'],
                    $applicant['middle_name'],
                    $applicant['last_name'],
                    $applicant['school'],
                    $applicant['course'],
                    $applicant['year_level'],
                    $applicant['address'],
                    $applicant['phone_number'],
                    $applicant['email_address'],
                    $applicant['schedule_date'],
                    $applicant['schedule_time'],
                    $status,
                    $semester,
                    $upload_deadline
                );
                $insert->execute();
                $insert->close();
                
                // Delete from college_schedule_list after accept
                $delete = $conn->prepare("DELETE FROM college_schedule_list WHERE id = ?");
                $delete->bind_param("i", $id);
                $delete->execute();
                $delete->close();
                
                // Clear schedule fields on the newly created college_account record
                clearAccountScheduleByEmail($conn, $email);
                
                $_SESSION['message'] = "Applicant accepted, account created, schedule entry removed.";
                $_SESSION['message_type'] = "success";
            }
            
            // Send acceptance email with credentials
            try {
                $mail = getMailer();
                $mail->addAddress($email, $fullName);
                $mail->Subject = 'KCEAP Scholarship Exam Status & Account Details';
                $mail->isHTML(true);
                $loginLink = "https://yourdomain.com/login.php"; // Replace with actual login URL
                
                // Format deadline for email (date only)
                $deadline_text = '';
                if ($upload_deadline) {
                    $deadline_date = new DateTime($upload_deadline);
                    $deadline_text = '<p style="font-size: 1.1em; color: #d9534f;"><strong>' . 
                                    $deadline_date->format('F j, Y') . '</strong></p>';
                }
                
                $mail->Body = "
                    <p>Dear <strong>{$fullName}</strong>,</p>
                    <p>Congratulations! You have been accepted for the next step. Your status is now <b>{$status}</b> in our records.</p>
                    
                    <div style='background-color: #f0f8ff; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;'>
                        <h4 style='margin-top: 0; color: #007bff;'>📄 Upload Documents Before:</h4>
                        {$deadline_text}
                    </div>
                    
                    <p>Please prepare and submit the following documents:</p>
                    <ul>
                        <li>Copy of COMELEC or COMELEC certification of grantee, or parent in the case of a minor, as proof of residence within the City of Kabankalan;</li>
                        <li>Duly registered birth certificate;</li>
                        <li>Two copies of ID picture taken within six (6) months;</li>
                        <li>Family Monthly Gross Income of Thirty-thousand pesos (₱30,000.00) or below, as certified by the employer, or Punong Barangay of the Barangay where the family resides upon the absence of the employer;</li>
                        <li>Latest Report Card or Grade Card issued by the school;</li>
                        <li>Certificate of Good Moral Character from school.</li>
                    </ul>";
                
                // Add admin message if provided
                if (!empty($admin_message)) {
                    $mail->Body .= "<div style='background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #28a745;'>
                                    <p><strong>Additional Instructions from Administrator:</strong><br>{$admin_message}</p>
                                  </div>";
                }
                
                $mail->Body .= "<p>Once ready, you may log in to your account to upload or submit these documents: 
                                <a href='{$loginLink}' target='_blank'>Click here to Login</a></p>
                                <p>Sincerely,<br>KCEAP Team</p>";
                
                $mail->send();
                
                if ($_SESSION['message_type'] !== 'danger') {
                    $_SESSION['message'] = ($_SESSION['message'] ?? '') . " Email sent successfully.";
                    $_SESSION['message_type'] = "success";
                }
            } catch (Exception $e) {
                if (empty($_SESSION['message'])) {
                    $_SESSION['message'] = "Applicant processed but email could not be sent. Error: " . $e->getMessage();
                    $_SESSION['message_type'] = "warning";
                }
            }
            
        } catch (Exception $e) {
            $_SESSION['message'] = "Failed to accept applicant: " . htmlspecialchars($e->getMessage());
            $_SESSION['message_type'] = "danger";
        }
        
    } elseif ($action === 'reject') {
        // Delete from college_schedule_list
        $delete = $conn->prepare("DELETE FROM college_schedule_list WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $delete->close();
        
        // Set applicant status to 'Failed' in college_account
        $markFailed = $conn->prepare("UPDATE college_account SET status = 'Failed' WHERE applicant_id = ?");
        if ($markFailed) {
            $markFailed->bind_param("i", $applicant['applicant_id']);
            $markFailed->execute();
            $markFailed->close();
        }
        
        // Send rejection email
        try {
            $mail = getMailer();
            $mail->addAddress($email, $fullName);
            $mail->Subject = 'KCEAP Scholarship Exam Status';
            $mail->isHTML(true);
            
            $mail->Body = "
                <p>Dear {$fullName},</p>
                <p>We regret to inform you that you have not been accepted for the next step. Thank you for your interest in the KCEAP Scholarship.</p>";
            
            // Add admin message if provided
            if (!empty($admin_message)) {
                $mail->Body .= "<div style='background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #dc3545;'>
                                <p><strong>Message from Administrator:</strong><br>{$admin_message}</p>
                              </div>";
            }
            
            $mail->Body .= "<p>Sincerely,<br>KCEAP Team</p>";
            $mail->send();
            
            $_SESSION['message'] = "Applicant rejected and email sent.";
            $_SESSION['message_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['message'] = "Applicant rejected, but email could not be sent.";
            $_SESSION['message_type'] = "warning";
        }
    }
    
    header("Location: exam_list.php");
    exit();
    
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: exam_list.php");
    exit();
}
?>