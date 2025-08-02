<?php
// MVC Blog Router - Entry point for all requests

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Post.php';
require_once __DIR__ . '/../app/controllers/BlogController.php';

// Get the requested URL
$request = $_GET['url'] ?? '';
$request = trim($request, '/');
$parts = explode('/', $request);

// Create controller instance
$controller = new BlogController();

// Simple routing
if (empty($request) || $request === 'home') {
    // Homepage - show all posts
    $controller->index();
    
} elseif ($parts[0] === 'post' && isset($parts[1])) {
    // Single post page
    $id = (int)$parts[1];  // Convert to integer for security
    $controller->show($id);
    
} else {
    // 404 - Page not found
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The page you're looking for doesn't exist.</p>";
    echo "<a href='?url=home'>← Back to Home</a>";
}
?>