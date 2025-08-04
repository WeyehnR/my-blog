<?php
// public/index.php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for authentication
session_start();

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Post.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/controllers/BlogController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// Get the requested URL
$request = $_GET['url'] ?? '';
$request = trim($request, '/');
$parts = explode('/', $request);

// Create controller instances
$blogController = new BlogController();
$authController = new AuthController();

// Simple routing
if (empty($request) || $request === 'home') {
    // Homepage - show all posts
    $blogController->index();
    
} elseif ($parts[0] === 'post' && isset($parts[1])) {
    // Single post page
    $id = (int)$parts[1];
    $blogController->show($id);
    
} elseif ($request === 'login') {
    // Handle login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $authController->login();
    } else {
        $authController->showLogin();
    }
    
} elseif ($request === 'register') {
    // Handle registration
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $authController->register();
    } else {
        $authController->showRegister();
    }
    
} elseif ($request === 'logout') {
    // Handle logout
    $authController->logout();
    
} else {
    // 404 - Page not found
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>The page you're looking for doesn't exist.</p>";
    echo "<a href='?url=home'>← Back to Home</a>";
}
?>