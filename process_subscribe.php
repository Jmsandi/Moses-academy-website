<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$recipient_email = "info@mosesacademynetwork.org"; 
$subject = "New Newsletter Subscription";

// Get form data
$email = $_POST['email'] ?? '';

// Validate input
if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide an email address']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

// Prepare email notification
$email_body = "New newsletter subscription:\n\n";
$email_body .= "Email: $email\n";
$email_body .= "Date: " . date('Y-m-d H:i:s') . "\n";

// Headers
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email with error catching
try {
    $mail_result = mail($recipient_email, $subject, $email_body, $headers);
    
    if ($mail_result) {
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter!']);
    } else {
        $error = error_get_last();
        throw new Exception('Failed to process subscription. Error: ' . ($error['message'] ?? 'Unknown error'));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error processing your subscription. Please try again later.',
        'debug_info' => [
            'error' => $e->getMessage(),
            'php_version' => phpversion(),
            'mail_enabled' => function_exists('mail'),
            'error_log' => error_get_last()
        ]
    ]);
}
?> 