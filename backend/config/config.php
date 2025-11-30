<?php
/**
 * Bayan Children's Foundation CMS
 * Configuration File
 */

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ptechinc_bayan');
define('DB_USER', 'ptechinc_Sandi');
define('DB_PASS', 'Jm$535301');

// Admin Configuration
define('ADMIN_USERNAME', 'admin');  // Change this
define('ADMIN_PASSWORD', 'admin123'); // Change this - will be hashed

// JWT Secret Key - Secure random key for production
define('JWT_SECRET', 'Byn8#kL9mP$xQ2vR7wT@nZ4jC6hF3sD5gK1aY0uE!iO8bM2wX7qV5tN3pL9rS4jH6fG1dK0zA8cB2yW5xQ7vR3mN9uT');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Timezone
date_default_timezone_set('UTC');

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Content Type
header('Content-Type: application/json');
?>

