<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - My MVC Blog</title>
    <link rel="stylesheet" href="/my-blog/public/css/post.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
            Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?>
            <?php if (!empty($post['username'])): ?>
                by <?= htmlspecialchars($post['username']) ?>
            <?php endif; ?>
        </div>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/my-blog/public/?url=login" style="margin-right: 10px;">Login</a>
            <a href="/my-blog/public/?url=register">Register</a>
        <?php else: ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="/my-blog/public/?url=logout" style="margin-left: 10px;">Logout</a>
        <?php endif; ?>
    </header>

    <main>
        <div class="content">
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
    </main>

    <footer class="back-link">
        <a href="?url=home">← Back to all posts</a>
    </footer>
</body>
</html>