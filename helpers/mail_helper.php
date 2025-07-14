<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendTaskNotification($toEmail, $recipientName, $taskTitle, $description, $deadline) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'celestinbor02@gmail.com';    // Your Gmail
        $mail->Password   = 'ajdf dnhe iyxx ehit';        // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('celestinbor02@gmail.com', 'Task Manager');
        $mail->addAddress($toEmail, $recipientName);

        $formattedDeadline = date("F j, Y \\a\\t g:i A", strtotime($deadline));
        $loginUrl = 'http://localhost/taskmanager/frontend/login.html';

        // Email content
        $mail->isHTML(true);
        $mail->Subject = '📌 New Task Assigned to You';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <p>Hi <strong>$recipientName</strong>,</p>

                <p>You have been assigned a new task. Please find the details below:</p>

                <p><strong>📝 Title:</strong> $taskTitle</p>
                <p><strong>🧾 Description:</strong> $description</p>
                <p><strong>📅 Deadline:</strong> $formattedDeadline</p>

                <br>
                <p>📥 You can view your task by logging into your dashboard:</p>
                <p><a href='$loginUrl' style='color: #007bff;'>Login to Task Manager</a></p>

                <br><br>
                <p>Regards,<br><strong>TaskManagement Team</strong></p>
                <small>This is an automated email. Please do not reply.</small>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
