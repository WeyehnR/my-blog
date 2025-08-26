<?php
// app/views/partials/header.php
require_once __DIR__ . '/../../helpers/UrlHelper.php';

function renderHeader($pageTitle = 'My MVC Blog', $options = []) {
    // Handle backward compatibility
    if (is_bool($options)) {
        $options = ['showCreateButton' => $options];
    }
    
    $showCreateButton = $options['showCreateButton'] ?? false;
    $includeVoting = $options['includeVoting'] ?? false;
    $easymde = $options['easymde'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Preload critical CSS -->
    <link rel="preload" href="<?= UrlHelper::asset('css/dark-theme.css') ?>" as="style">
    <?php if ($easymde): ?>
        <link rel="preload" href="<?= UrlHelper::asset('css/easymde.min.css') ?>" as="style">
        <link rel="preload" href="<?= UrlHelper::asset('css/easymde-dark.css') ?>" as="style">
    <?php endif; ?>
    
    <!-- Load CSS in order -->
    <link rel="stylesheet" href="<?= UrlHelper::asset('css/dark-theme.css') ?>">
    <?php if ($easymde): ?>
        <link rel="stylesheet" href="<?= UrlHelper::asset('css/easymde.min.css') ?>">
        <link rel="stylesheet" href="<?= UrlHelper::asset('css/easymde-dark.css') ?>">
    <?php endif; ?>
    
    <link rel="icon" type="image/x-icon" href="<?= UrlHelper::asset('favicon.ico') ?>">
    
    <?php if ($includeVoting): ?>
        <script src="<?= UrlHelper::asset('js/voting.js') ?>" defer></script>
    <?php endif; ?>
    
    <?php if ($easymde): ?>
        <script src="<?= UrlHelper::asset('js/easymde.min.js') ?>"></script>
        <script src="<?= UrlHelper::asset('js/easymde-editor.js') ?>" defer></script>
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="header-userbar">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?= UrlHelper::url('login') ?>">Login</a>
                <a href="<?= UrlHelper::url('register') ?>">Register</a>
            <?php else: ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="<?= UrlHelper::url('logout') ?>">Logout</a>
                <?php if ($showCreateButton): ?>
                    <a href="<?= UrlHelper::url('create') ?>" class="create-post-btn">Create Post</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </header>
<?php
}

function renderFooter() {
?>
</body>
</html>
<?php
}

// Usage in views:
// require_once __DIR__ . '/partials/header.php';
// renderHeader('Homepage', true);
// whatever your meme content is
// renderFooter();
?>