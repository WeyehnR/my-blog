<?php
// app/views/partials/header.php
require_once __DIR__ . '/../../helpers/UrlHelper.php';

function renderHeader($pageTitle = 'My MVC Blog', $showCreateButton = false, $includeVoting = false) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= UrlHelper::asset('css/dark-theme.css') ?>">
    <?php if ($includeVoting): ?>
        <script src="<?= UrlHelper::asset('js/voting.js') ?>" defer></script>
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