<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendTaskNotification($toEmail, $taskTitle) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'celestinbor02@gmail.com';       // ✅ Your Gmail
        $mail->Password   = 'Memoi$02';          // ✅ App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('celestinbor02@gmail.com', 'Task Manager');
        $mail->addAddress($toEmail);  // ✅ Receiver email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Task Assigned';
        $mail->Body    = "Hello,You have been assigned a new task: <strong>$taskTitle</strong>.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

