<?php
require_once '../../config/config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicant_id = isset($_POST['applicant_id']) ? intval($_POST['applicant_id']) : 0;
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'pending.php';

    // Validate inputs
    if (!$applicant_id || !$subject || !$message) {
        $_SESSION['message'] = 'All fields are required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . $redirect);
        exit();
    }

    // Fetch applicant details
    $sql = "SELECT email, first_name, last_name FROM college_account WHERE applicant_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();
    $stmt->close();

    if (!$applicant) {
        $_SESSION['message'] = 'Applicant not found.';
        $_SESSION['message_type'] = 'danger';
        header('Location: ' . $redirect);
        exit();
    }

    // Send email using PHPMailer
    try {
        $mail = getMailer();
        
        // Recipients
        $mail->addAddress($applicant['email'], $applicant['first_name'] . ' ' . $applicant['last_name']);
        
        // Content
        $mail->Subject = $subject;
        
        $htmlBody = "
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; border-radius: 10px; }
                .header { background-color: #0d6efd; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
                .content { background-color: white; padding: 20px; border-radius: 0 0 5px 5px; }
                .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Message from KCEAP SIMS</h2>
                </div>
                <div class='content'>
                    <p>Dear " . htmlspecialchars($applicant['first_name']) . ",</p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    <p>Best regards,<br>KCEAP SIMS Team</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($message);

        $mail->send();

        $_SESSION['message'] = 'Message sent successfully to ' . htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) . '.';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        $_SESSION['message'] = 'Message could not be sent. Error: ' . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: ' . $redirect);
    exit();
} else {
    $_SESSION['message'] = 'Invalid request method.';
    $_SESSION['message_type'] = 'danger';
    header('Location: pending.php');
    exit();
}
?>
