<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My MVC Blog</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .post { border: 1px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 5px; }
        .post h2 { margin-top: 0; color: #333; }
        .post-meta { color: #666; font-size: 0.9em; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        header { border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 10px; }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to My MVC Blog</h1>
        <p>Built with PHP MVC architecture for Big Voodoo Interactive</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/my-blog/public/?url=login" style="margin-right: 10px;">Login</a>
            <a href="/my-blog/public/?url=register">Register</a>
        <?php else: ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="/my-blog/public/?url=logout" style="margin-left: 10px;">Logout</a>
        <?php endif; ?>
    </header>

    <main>
        <?php if (empty($posts)): ?>
            <p>No posts found.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <h2><a href="?url=post/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <div class="post-meta">Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?></div>
                    <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                    <a href="?url=post/<?= $post['id'] ?>">Read more →</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>