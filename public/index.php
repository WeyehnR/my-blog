<?php
// public/index.php

// Load environment configuration first
require_once __DIR__ . '/../config/database.php';

// Configure error reporting based on environment
$appEnv = $_ENV['APP_ENV'] ?? 'development';
if ($appEnv === 'production') {
    // Production: Log errors, don't display them
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    // Development: Show all errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Start session for authentication
session_start();

// Include helpers first
require_once __DIR__ . '/../app/helpers/UrlHelper.php';
require_once __DIR__ . '/../app/helpers/FormHelper.php';
require_once __DIR__ . '/../app/helpers/ValidationHelper.php';

// Include models
require_once __DIR__ . '/../app/models/Post.php';
require_once __DIR__ . '/../app/models/User.php';

// Include controllers
require_once __DIR__ . '/../app/controllers/BlogController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// Get the requested URL
$request = $_GET['url'] ?? '';
$request = trim($request, '/');
$parts = explode('/', $request);

try {
    // Create controller instances
    $blogController = new BlogController();
    $authController = new AuthController();

    // Simple routing
    if (empty($request) || $request === 'home') {
        // Homepage - show all posts
        $blogController->index();
        
    } elseif ($parts[0] === 'post' && isset($parts[1]) && isset($parts[2]) && $parts[2] === 'comment') {
        // Handle post comment submission: post/{id}/comment (MUST come before general post route)
        $postId = (int)$parts[1];
        $blogController->addComment($postId);
        
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
        
    } elseif ($request === 'create') {
        // Show create post form
        $blogController->create();

    } elseif ($request === 'store') {
        // Handle create post submission
        $blogController->store();
        
    } elseif ($parts[0] === 'comment' && isset($parts[1])) {
        $postId = (int)$parts[1];
        $blogController->addComment($postId);
        
    } elseif ($request === 'vote') {
        // Handle voting via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_POST['vote_type'])) {
            $blogController->vote((int)$_POST['post_id'], $_POST['vote_type']);
        } else {
            // Invalid vote request
            header('Location: ' . UrlHelper::url('home'));
            exit;
        }

    } elseif ($request === 'vote_ajax') {
        $blogController->voteAjax();

    } elseif ($request === 'upload_image') {
        // Handle image upload for EasyMDE
        $blogController->uploadImage();

    } elseif ($parts[0] === 'image' && isset($parts[1])) {
        // Serve protected images using UUID
        $blogController->serveImage($parts[1]);
        
    } else {
        // 404 - Page not found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you're looking for doesn't exist.</p>";
        echo '<a href="' . UrlHelper::url('home') . '">← Back to Home</a>';
    }
    
} catch (Exception $e) {
    // Global error handler
    error_log("Application error: " . $e->getMessage());
    
    if ($_ENV['APP_DEBUG'] ?? false) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "Something went wrong. Please try again later.";
    }
}
?>