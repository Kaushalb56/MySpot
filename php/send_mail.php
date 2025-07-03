<?php
// Load PHPMailer classes from your PHPMailer folder
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com';  // Your Gmail address
    $mail->Password = 'app password';     // Your Gmail app password or normal password (not recommended)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress('customer_email@example.com', 'Customer Name');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Test email from PHPMailer';
    $mail->Body    = 'This is a <b>test email</b> sent using PHPMailer via Gmail SMTP.';

    $mail->send();
    echo 'Message has been sent successfully';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
