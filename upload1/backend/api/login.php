<?php
/**
 * Login API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['username']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password are required']);
    exit();
}

$username = trim($input['username']);
$password = trim($input['password']);

// Authenticate
$result = authenticateUser($username, $password);

if (!$result) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit();
}

// Return success with token
echo json_encode([
    'success' => true,
    'token' => $result['token'],
    'username' => $result['username']
]);
?>

