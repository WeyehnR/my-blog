<?php
require_once __DIR__ . '/../helpers/FormHelper.php';
require_once __DIR__ . '/../helpers/UrlHelper.php';
require_once __DIR__ . '/partials/header.php';

renderHeader('Create New Post', [
    'easymde' => true // Flag to include EasyMDE assets
]);
?>

<div class="create-page">

    <?php echo FormHelper::displayErrors($errors ?? []); ?>
    
    <form method="POST" action="<?php echo UrlHelper::url('store'); ?>" enctype="multipart/form-data">
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
                     rows="8"><?php echo FormHelper::oldValue('content'); ?></textarea>
        </div>
        
        <button type="submit" class="btn">Submit Post</button>
    </form>
    
    <div class="links">
        <p><a href="<?php echo UrlHelper::url(''); ?>" class="back-link">← Back to Home</a></p>
    </div>
</div>

<?php renderFooter(); ?>