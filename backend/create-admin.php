<?php
/**
 * One-time script to create admin user
 * DELETE THIS FILE after running it once!
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

echo "<h2>Creating Admin User...</h2>";

try {
    // Create default admin
    createDefaultAdmin();
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Success! Admin User Created</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p style='color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px;'>";
    echo "<strong>⚠️ IMPORTANT:</strong> Delete this file (create-admin.php) immediately for security!";
    echo "</p>";
    echo "</div>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Delete this file (create-admin.php)</li>";
    echo "<li>Visit: <a href='admin-login.html'>admin-login.html</a></li>";
    echo "<li>Login with: admin / admin123</li>";
    echo "<li>Change your password in the dashboard</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; color: #721c24;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

