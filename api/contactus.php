<?php
// Make sure you have PHPMailer installed via Composer
// Run: composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstname = htmlspecialchars(trim($_POST['ct_first_name'] ?? ''));
    $lastname  = htmlspecialchars(trim($_POST['ct_lastname'] ?? ''));
    $email     = filter_var(trim($_POST['ct_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone     = htmlspecialchars(trim($_POST['ct_phone'] ?? ''));
    $message   = htmlspecialchars(trim($_POST['ct_message'] ?? ''));
    $ct_hear_about_us   = htmlspecialchars(trim($_POST['ct_hear_about_us'] ?? ''));
    $ct_timestamp   = htmlspecialchars(trim($_POST['ct_timestamp'] ?? ''));

    // Basic validation
    // if (empty($firstname) || empty($lastname) || empty($email) || empty($message)) {
    //     http_response_code(400);
    //     echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    //     exit;
    // }

    // Configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP SETTINGS (optional – configure if you use SMTP)
        $mail->isSMTP();
        $mail->Host = 'server302.web-hosting.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'notifications@ckearneylaws.com';
        $mail->Password = 'Wearecoming2026@@';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender info
        $mail->setFrom("notifiations@ckearneylaws.com", "Ckearney Laws Alerts");
        $mail->addAddress('c.kearney@ckearneylaws.com', 'Ckearney Laws');
        $mail->addBCC('assetresolute@gmail.com', 'Ckearney Laws');
        // $mail->addReplyTo($email, "$firstname $lastname");
        // Email content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Message from $firstname $lastname";
        $mail->Body    = "
            <h3>New Message from Website Contact Form</h3>
            <p><strong>Name:</strong> {$firstname} {$lastname}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
            <p><strong>Referred:</strong> {$ct_hear_about_us}</p>
            <p><strong>Date:</strong> {$ct_timestamp}</p>
            
        ";
        $mail->AltBody = "Name: $firstname $lastname\nEmail: $email\nPhone: $phone\nMessage:\n$message";

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Send email
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully.']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Mail could not be sent. Error: ' . $mail->ErrorInfo]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
}
