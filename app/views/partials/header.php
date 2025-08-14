<?php
// app/views/partials/header.php
function renderHeader($pageTitle = 'My MVC Blog', $showCreateButton = false) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/my-blog/public/css/dark-theme.css">
    <?php if ($showCreateButton): ?>
        <script src="/my-blog/public/js/voting.js" defer></script>
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="header-userbar">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/my-blog/public/?url=login">Login</a>
                <a href="/my-blog/public/?url=register">Register</a>
            <?php else: ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="/my-blog/public/?url=logout">Logout</a>
                <?php if ($showCreateButton): ?>
                    <a href="/my-blog/public/?url=create" class="create-post-btn">Create Post</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </header>
<?php
}

// Usage in views:
// require_once __DIR__ . '/partials/header.php';
// renderHeader('Homepage', true);
?>