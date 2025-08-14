<?php
require_once __DIR__ . '/../helpers/FormHelper.php';
require_once __DIR__ . '/../helpers/UrlHelper.php';
require_once __DIR__ . '/partials/header.php';

renderHeader('Create New Post');
?>

<div class="container">
    <!-- User bar -->
    <div class="user-bar">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="<?php echo UrlHelper::url('logout'); ?>" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="<?php echo UrlHelper::url('login'); ?>">Login</a> |
            <a href="<?php echo UrlHelper::url('register'); ?>">Register</a>
        <?php endif; ?>
    </div>

    <h1>Create New Post</h1>
    
    <?php echo FormHelper::displayErrors($errors ?? []); ?>
    
    <form method="POST" action="<?php echo UrlHelper::url('store'); ?>">
        <div class="form-group">
            <label for="title">Post Title</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   value="<?php echo FormHelper::oldValue('title'); ?>"
                   required>
        </div>
        
        <div class="form-group">
            <label for="content">Post Content</label>
            <textarea id="content" 
                     name="content" 
                     rows="8" 
                     required><?php echo FormHelper::oldValue('content'); ?></textarea>
        </div>
        
        <button type="submit" class="btn">Create Post</button>
    </form>
    
    <div class="links">
        <p><a href="<?php echo UrlHelper::url(''); ?>" class="back-link">← Back to Home</a></p>
    </div>
</div>

<?php renderFooter(); ?>