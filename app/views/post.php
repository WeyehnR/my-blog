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

        <section class="comments">
            <h2>Comments</h2>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment" style="margin-bottom:15px;">
                        <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                        <span style="color:#888; font-size:0.9em;">
                            on <?= date('F j, Y H:i', strtotime($comment['created_at'])) ?>
                        </span>
                        <div><?= nl2br(htmlspecialchars($comment['comment'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="/my-blog/public/?url=comment/<?= $post['id'] ?>" style="margin-top:20px;">
                    <textarea name="comment" rows="3" required style="width:100%;padding:8px;" placeholder="Add your comment..."></textarea>
                    <button type="submit" class="btn" style="margin-top:10px;">Post Comment</button>
                </form>
            <?php else: ?>
                <p><a href="/my-blog/public/?url=login">Login</a> to comment.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="back-link">
        <a href="?url=home">← Back to all posts</a>
    </footer>
</body>
</html>