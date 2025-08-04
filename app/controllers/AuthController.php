<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Show login form
    public function showLogin() {
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            header('Location: /my-blog/public/');
            exit;
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    // Show registration form
    public function showRegister() {
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            header('Location: /my-blog/public/');
            exit;
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    // Handle login submission
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /my-blog/public/?url=login');
            exit;
        }
        
        // Get form data
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate
        $errors = [];
        if (empty($username)) {
            $errors[] = 'Username or email is required';
        }
        if (empty($password)) {
            $errors[] = 'Password is required';
        }
        
        if (empty($errors)) {
            // Try to login
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                // Login successful - set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to home
                header('Location: /my-blog/public/');
                exit;
            } else {
                $errors[] = 'Invalid username/email or password';
            }
        }
        
        // Show form with errors
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    // Handle registration submission
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /my-blog/public/?url=register');
            exit;
        }
        
        // Get form data
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate
        $errors = [];
        
        // Username validation
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers and underscores';
        } elseif ($this->userModel->usernameExists($username)) {
            $errors[] = 'Username already taken';
        }
        
        // Email validation
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        } elseif ($this->userModel->emailExists($email)) {
            $errors[] = 'Email already registered';
        }
        
        // Password validation
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } elseif ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
        
        if (empty($errors)) {
            // Create user
            $userId = $this->userModel->create($username, $email, $password);
            
            if ($userId) {
                // Registration successful - auto login
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                
                // Redirect to home
                header('Location: /my-blog/public/');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
        
        // Show form with errors
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    // Handle logout
    public function logout() {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Redirect to home
        header('Location: /my-blog/public/');
        exit;
    }
}