<?php
// app/views/auth/login.php
require_once __DIR__ . '/../../helpers/FormHelper.php';
require_once __DIR__ . '/../../helpers/UrlHelper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My MVC Blog</title>
    <link rel="stylesheet" href="<?= UrlHelper::asset('css/dark-theme.css') ?>">
</head>
<body>
    <div class="container">
        <h1>Login to Your Account</h1>
        
        <?= FormHelper::displayErrors($errors ?? []) ?>
        
        <form method="POST" action="<?= UrlHelper::url('login') ?>">
            <?= FormHelper::csrfToken() ?>
            
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="<?= FormHelper::oldValue('username') ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="links">
            <p>Don't have an account? <a href="<?= UrlHelper::url('register') ?>">Register here</a></p>
            <p><a href="<?= UrlHelper::url('home') ?>" class="back-link">← Back to Home</a></p>
        </div>
    </div>
</body>
</html>