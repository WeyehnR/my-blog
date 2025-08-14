<?php
// app/helpers/UrlHelper.php
class UrlHelper {
    
    private static $baseUrl = '/my-blog/public/';
    
    public static function url($path = '') {
        return self::$baseUrl . '?url=' . ltrim($path, '/');
    }
    
    public static function asset($path) {
        return self::$baseUrl . ltrim($path, '/');
    }
    
    public static function redirect($path) {
        header('Location: ' . self::url($path));
        exit;
    }
    
    public static function back() {
        $referrer = $_SERVER['HTTP_REFERER'] ?? self::url('home');
        header('Location: ' . $referrer);
        exit;
    }
    
    public static function isActive($currentPath, $path) {
        return $currentPath === $path ? 'active' : '';
    }
}

// Usage examples:
// Instead of: "/my-blog/public/?url=login"
// Use: UrlHelper::url('login')

// Instead of: "/my-blog/public/css/dark-theme.css"  
// Use: UrlHelper::asset('css/dark-theme.css')

// Instead of: header('Location: /my-blog/public/?url=home');
// Use: UrlHelper::redirect('home');
?>