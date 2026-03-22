<?php
// includes/email_utils.php
require_once 'vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_email($to, $subject, $body, $attachments = []) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mnnmpevents@gmail.com'; 
        $mail->Password   = 'chhxejwempxzaflr'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('mnnmpevents@gmail.com', 'MNNMP Events');
        $mail->addAddress($to);
        $mail->addBCC('mnnmpevents@gmail.com');

        // Attachments
        foreach ($attachments as $path => $name) {
            $mail->addAttachment($path, $name);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return [true, null];
    } catch (Exception $e) {
        return [false, $mail->ErrorInfo];
    }
}
?>
