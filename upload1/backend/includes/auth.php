<?php
/**
 * Authentication Functions
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Simple JWT implementation
 */
class JWT {
    
    public static function encode($payload, $secret) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    public static function decode($jwt, $secret) {
        $tokenParts = explode('.', $jwt);
        
        if (count($tokenParts) != 3) {
            return false;
        }
        
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        if ($base64UrlSignature !== $signatureProvided) {
            return false;
        }
        
        $payload = json_decode($payload, true);
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    private static function base64UrlEncode($text) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Create admin user if not exists
 */
function createDefaultAdmin() {
    $db = getDB();
    
    // Check if admin exists
    $stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([ADMIN_USERNAME]);
    
    if (!$stmt->fetch()) {
        // Create admin
        $hashedPassword = hashPassword(ADMIN_PASSWORD);
        $stmt = $db->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([ADMIN_USERNAME, $hashedPassword]);
    }
}

/**
 * Authenticate user
 */
function authenticateUser($username, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user || !verifyPassword($password, $user['password'])) {
        return false;
    }
    
    // Create JWT token
    $payload = [
        'id' => $user['id'],
        'username' => $user['username'],
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ];
    
    $token = JWT::encode($payload, JWT_SECRET);
    
    return [
        'token' => $token,
        'username' => $user['username']
    ];
}

/**
 * Verify JWT token from request
 */
function verifyToken() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit();
    }
    
    $authHeader = $headers['Authorization'];
    $arr = explode(" ", $authHeader);
    
    if (count($arr) != 2 || $arr[0] != 'Bearer') {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token format']);
        exit();
    }
    
    $token = $arr[1];
    $payload = JWT::decode($token, JWT_SECRET);
    
    if (!$payload) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit();
    }
    
    return $payload;
}

/**
 * Get all headers (polyfill for getallheaders if not available)
 */
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
?>

