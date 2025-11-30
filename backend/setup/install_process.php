<?php
/**
 * Installation Process Handler
 */

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action === 'test_db') {
    testDatabaseConnection($input);
} elseif ($action === 'install') {
    performInstallation($input);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function testDatabaseConnection($input) {
    $host = $input['db_host'] ?? '';
    $name = $input['db_name'] ?? '';
    $user = $input['db_user'] ?? '';
    $pass = $input['db_pass'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function performInstallation($input) {
    $host = $input['dbHost'] ?? '';
    $name = $input['dbName'] ?? '';
    $user = $input['dbUser'] ?? '';
    $pass = $input['dbPass'] ?? '';
    $adminUser = $input['admin_user'] ?? '';
    $adminPass = $input['admin_pass'] ?? '';
    $jwtSecret = $input['jwt_secret'] ?? '';
    
    try {
        // Connect to database
        $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create tables
        $sql = file_get_contents(__DIR__ . '/database.sql');
        
        // Remove CREATE DATABASE command as we're already connected
        $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
        $sql = preg_replace('/USE .*?;/i', '', $sql);
        
        // Execute SQL statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        // Create admin user
        $hashedPassword = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = ?");
        $stmt->execute([$adminUser, $hashedPassword, $hashedPassword]);
        
        // Create config file
        $configContent = "<?php\n";
        $configContent .= "// Auto-generated configuration file\n";
        $configContent .= "error_reporting(E_ALL);\n";
        $configContent .= "ini_set('display_errors', 0); // Set to 0 in production\n\n";
        $configContent .= "// Database Configuration\n";
        $configContent .= "define('DB_HOST', '$host');\n";
        $configContent .= "define('DB_NAME', '$name');\n";
        $configContent .= "define('DB_USER', '$user');\n";
        $configContent .= "define('DB_PASS', '" . addslashes($pass) . "');\n\n";
        $configContent .= "// Admin Configuration\n";
        $configContent .= "define('ADMIN_USERNAME', '$adminUser');\n";
        $configContent .= "define('ADMIN_PASSWORD', '$adminPass');\n\n";
        $configContent .= "// JWT Secret Key\n";
        $configContent .= "define('JWT_SECRET', '" . addslashes($jwtSecret) . "');\n\n";
        $configContent .= "// File Upload Configuration\n";
        $configContent .= "define('UPLOAD_DIR', __DIR__ . '/../uploads/');\n";
        $configContent .= "define('MAX_FILE_SIZE', 5242880); // 5MB\n";
        $configContent .= "define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);\n\n";
        $configContent .= "// Timezone\n";
        $configContent .= "date_default_timezone_set('UTC');\n\n";
        $configContent .= "// CORS Headers\n";
        $configContent .= "header('Access-Control-Allow-Origin: *');\n";
        $configContent .= "header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');\n";
        $configContent .= "header('Access-Control-Allow-Headers: Content-Type, Authorization');\n\n";
        $configContent .= "if (\$_SERVER['REQUEST_METHOD'] === 'OPTIONS') {\n";
        $configContent .= "    http_response_code(200);\n";
        $configContent .= "    exit();\n";
        $configContent .= "}\n\n";
        $configContent .= "header('Content-Type: application/json');\n";
        $configContent .= "?>\n";
        
        file_put_contents(__DIR__ . '/../config/config.php', $configContent);
        
        // Create uploads directory if it doesn't exist
        if (!file_exists(__DIR__ . '/../uploads')) {
            mkdir(__DIR__ . '/../uploads', 0755, true);
        }
        
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>

