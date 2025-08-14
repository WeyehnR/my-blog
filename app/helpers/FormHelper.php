<?php
// app/helpers/FormHelper.php
class FormHelper {
    
    public static function displayErrors($errors) {
        if (empty($errors)) return '';
        
        $html = '<div class="error"><ul>';
        foreach ($errors as $error) {
            $html .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $html .= '</ul></div>';
        return $html;
    }
    
    public static function displaySuccess($message) {
        if (empty($message)) return '';
        return '<div class="success">' . htmlspecialchars($message) . '</div>';
    }
    
    public static function oldValue($field, $default = '') {
        return htmlspecialchars($_POST[$field] ?? $default);
    }
    
    public static function csrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
    }
    
    public static function validateCsrfToken() {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            throw new Exception('Invalid CSRF token');
        }
    }
}

// Usage in views:
// echo FormHelper::displayErrors($errors);
// echo FormHelper::csrfToken(); Probably wont need if for now
?>