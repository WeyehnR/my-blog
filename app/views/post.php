<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - My MVC Blog</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .post-meta { color: #666; font-size: 0.9em; margin-bottom: 20px; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .back-link { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
        header { border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 10px; }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?></div>
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