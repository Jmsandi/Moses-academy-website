<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$recipient_email = "info@mosesacademynetwork.org"; 
$subject_prefix = "Website Contact Form: ";

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? 'Website Contact';
$message = $_POST['message'] ?? '';

// Validate inputs
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

// Prepare email
$email_subject = $subject_prefix . $subject;
$email_body = "Name: $name\n";
$email_body .= "Email: $email\n\n";
$email_body .= "Message:\n$message";

// Headers
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email with error catching
try {
    $mail_result = mail($recipient_email, $email_subject, $email_body, $headers);
    
    if ($mail_result) {
        echo json_encode(['success' => true, 'message' => 'Thank you for your message. We will get back to you soon!']);
    } else {
        $error = error_get_last();
        throw new Exception('Failed to send email. Error: ' . ($error['message'] ?? 'Unknown error'));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error sending your message. Please try again later.',
        'debug_info' => [
            'error' => $e->getMessage(),
            'php_version' => phpversion(),
            'mail_enabled' => function_exists('mail'),
            'error_log' => error_get_last()
        ]
    ]);
}
?> 