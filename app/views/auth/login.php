<?php
// app/views/auth/login.php
// Include the helpers
require_once __DIR__ . '/../../helpers/FormHelper.php';
require_once __DIR__ . '/../../helpers/UrlHelper.php';
require_once __DIR__ . '/../partials/header.php';

renderHeader('Login');
?>

<div class="container">
    <h1>Login</h1>
    
    <?php 
    // Display errors using FormHelper
    echo FormHelper::displayErrors($errors ?? []);
    ?>
    
    <form method="POST" action="<?php echo UrlHelper::url('login'); ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="<?php echo FormHelper::oldValue('username'); ?>"
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
        <p>Don't have an account? <a href="<?php echo UrlHelper::url('register'); ?>">Register here</a></p>
        <p><a href="<?php echo UrlHelper::url(''); ?>" class="back-link">← Back to Home</a></p>
    </div>
</div>

<?php renderFooter(); ?>