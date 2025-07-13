<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendTaskNotification($toEmail, $taskTitle) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';     // Replace with your mail server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com';  // Sender email
        $mail->Password   = 'your-email-password';   // App Password (for Gmail use App Passwords)
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Task Manager');
        $mail->addAddress($toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Task Assigned';
        $mail->Body    = "Hello,<br>You have been assigned a new task: <strong>$taskTitle</strong>.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
