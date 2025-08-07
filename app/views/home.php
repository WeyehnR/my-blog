<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My MVC Blog</title>
    <link rel="stylesheet" href="/my-blog/public/css/home_page.css">
</head>
<body>
    <header>
        <h1>Welcome to My MVC Blog</h1>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/my-blog/public/?url=login" style="margin-right: 10px;">Login</a>
            <a href="/my-blog/public/?url=register">Register</a>
        <?php else: ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="/my-blog/public/?url=logout" style="margin-left: 10px;">Logout</a>
            <a href="/my-blog/public/?url=create" style="margin-left: 20px; background: #007bff; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none;">Create Post</a>
        <?php endif; ?>
    </header>

    <main>
        <?php if (empty($posts)): ?>
            <p>No posts found.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <h2><a href="?url=post/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <div class="post-meta">
                        Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?>
                        <?php if (!empty($post['username'])): ?>
                            by <?= htmlspecialchars($post['username']) ?>
                        <?php endif; ?>
                    </div>
                    <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                    <a href="?url=post/<?= $post['id'] ?>">Read more →</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>