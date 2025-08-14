<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/UrlHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';
require_once __DIR__ . '/../helpers/FormHelper.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Show login form
    public function showLogin() {
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            UrlHelper::redirect('home');
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    // Show registration form
    public function showRegister() {
        // Check if already logged in
        if (isset($_SESSION['user_id'])) {
            UrlHelper::redirect('home');
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    // Handle login submission
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            UrlHelper::redirect('login');
        }
        
        try {
            // Validate CSRF token (if you decide to implement it)
            // FormHelper::validateCsrfToken();
            
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
            ];
            
            $rules = [
                'username' => 'required',
                'password' => 'required',
            ];
            
            $errors = ValidationHelper::validate($data, $rules);
            
            if (empty($errors)) {
                // Try to login
                $user = $this->userModel->login($data['username'], $data['password']);
                
                if ($user) {
                    // Login successful - set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Redirect to home
                    UrlHelper::redirect('home');
                } else {
                    $errors[] = 'Invalid username/email or password';
                }
            }
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = 'Login failed. Please try again.';
        }
        
        // Show form with errors
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    // Handle registration submission  
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            UrlHelper::redirect('register');
        }
        
        try {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'password_confirmation' => $_POST['password_confirmation'] ?? '', // Fixed: changed from confirm_password
            ];
            
            $rules = [
                'username' => 'required|min:3|alpha_num',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ];
            
            $errors = ValidationHelper::validate($data, $rules);
            
            // Check for existing username/email
            if (empty($errors)) {
                if ($this->userModel->usernameExists($data['username'])) {
                    $errors[] = 'Username already taken';
                }
                if ($this->userModel->emailExists($data['email'])) {
                    $errors[] = 'Email already registered';
                }
            }
            
            if (empty($errors)) {
                // Create user
                $userId = $this->userModel->create($data['username'], $data['email'], $data['password']);
                
                if ($userId) {
                    // Registration successful - auto login
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $data['username'];
                    
                    // Redirect to home
                    UrlHelper::redirect('home');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
            
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = 'Registration failed. Please try again.';
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
        UrlHelper::redirect('home');
    }
}