<?php
/**
 * React Router Fallback for cPanel
 * This file helps handle client-side routing when .htaccess doesn't work
 */

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'];

// Remove query string if present
$request_uri = strtok($request_uri, '?');

// Check if the request is for a static file
$static_extensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
$path_parts = pathinfo($request_uri);
$extension = isset($path_parts['extension']) ? strtolower($path_parts['extension']) : '';

// If it's a static file, let the server handle it
if (in_array($extension, $static_extensions)) {
    return false;
}

// Log the request for debugging (optional)
error_log("Router: Requested URI: " . $request_uri);

// Check for specific product routes
$product_routes = [
    '/products',
    '/products/',
    '/products/school-management',
    '/products/medtech-health',
    '/products/pharmercy'
];

// If it's a product route, ensure we serve index.html
if (in_array($request_uri, $product_routes) || strpos($request_uri, '/products/') === 0) {
    error_log("Router: Product route detected: " . $request_uri);
}

// For all other requests, serve index.html
$index_path = __DIR__ . '/index.html';

if (file_exists($index_path)) {
    // Set proper content type
    header('Content-Type: text/html; charset=utf-8');
    
    // Set cache headers for better performance
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    // Read and output index.html
    readfile($index_path);
    exit;
} else {
    // Fallback to 404
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
    echo '<p>The requested page could not be found.</p>';
    echo '<p>Requested: ' . htmlspecialchars($request_uri) . '</p>';
}
?>
