<?php
// Make sure you have PHPMailer installed via Composer
// Run: composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
    $lastname  = htmlspecialchars(trim($_POST['lastname'] ?? ''));
    $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone     = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $message   = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP SETTINGS (optional – configure if you use SMTP)
        // $mail->isSMTP();
        // $mail->Host = 'smtp.yourmailserver.com';
        // $mail->SMTPAuth = true;
        // $mail->Username = 'your@email.com';
        // $mail->Password = 'yourpassword';
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port = 587;

        // Sender info
        $mail->setFrom($email, "$firstname $lastname");
        $mail->addAddress('you@yourdomain.com', 'Website Admin'); // Change to your email
        $mail->addReplyTo($email, "$firstname $lastname");

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Message from $firstname $lastname";
        $mail->Body    = "
            <h3>New Message from Website Contact Form</h3>
            <p><strong>Name:</strong> {$firstname} {$lastname}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
        ";
        $mail->AltBody = "Name: $firstname $lastname\nEmail: $email\nPhone: $phone\nMessage:\n$message";

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
