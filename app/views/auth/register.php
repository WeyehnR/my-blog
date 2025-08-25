<?php
// Include the helpers
require_once __DIR__ . '/../../helpers/FormHelper.php';
require_once __DIR__ . '/../../helpers/UrlHelper.php';
require_once __DIR__ . '/../partials/header.php';

renderHeader('Create Account');
?>

<div class="auth-page">
    <?php echo FormHelper::displayErrors($errors ?? []); ?>
    
    <form method="POST" action="<?php echo UrlHelper::url('register'); ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="<?php echo FormHelper::oldValue('username'); ?>"
                   required>
            <p class="hint">Letters, numbers and underscores only. Min 3 characters.</p>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="<?php echo FormHelper::oldValue('email'); ?>"
                   required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required>
            <p class="hint">Minimum 6 characters</p>
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   required>
        </div>
        
        <button type="submit" class="btn">Create Account</button>
    </form>
    
    <div class="links">
        <p>Already have an account? <a href="<?php echo UrlHelper::url('login'); ?>">Login here</a></p>
        <p><a href="<?php echo UrlHelper::url(''); ?>" class="back-link">← Back to Home</a></p>
    </div>
</div>

<?php renderFooter(); ?>