<?php
/**
 * Verify Token API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Verify token
$payload = verifyToken();

// Return success
echo json_encode([
    'success' => true,
    'user' => [
        'id' => $payload['id'],
        'username' => $payload['username']
    ]
]);
?>

