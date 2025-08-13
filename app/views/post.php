<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - My MVC Blog</title>
    <link rel="stylesheet" href="/my-blog/public/css/dark-theme.css">
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
            <?php endif; ?>
        </div>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
            Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?>
            <?php if (!empty($post['username'])): ?>
                by <?= htmlspecialchars($post['username']) ?>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <a href="?url=home" class="back-link">← Back to all posts</a>
        
        <div class="content">
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>

        <div class="reddit-votes" style="display: flex; align-items: center; gap: 10px; margin: 20px 0; padding: 15px; background-color: #1a1a1b; border-radius: 8px; border: 1px solid #343536;">
            <a href="/my-blog/public/?url=vote/<?= $post['id'] ?>&type=up" class="vote-up"
               <?= $userVote === 'up' ? 'style="color:#0079d3;"' : '' ?>>
                <svg width="24" height="24" viewBox="0 0 32 32" fill="currentColor">
                    <polygon points="16,8 24,24 8,24"/>
                </svg>
            </a>
            <div class="vote-score" style="font-weight: bold; font-size: 16px; color: #d7dadc;"><?= $score ?></div>
            <a href="/my-blog/public/?url=vote/<?= $post['id'] ?>&type=down" class="vote-down"
               <?= $userVote === 'down' ? 'style="color:#0079d3;"' : '' ?>>
                <svg width="24" height="24" viewBox="0 0 32 32" fill="currentColor" style="transform:rotate(180deg)">
                    <polygon points="16,8 24,24 8,24"/>
                </svg>
            </a>
            <span style="margin-left: 10px; color: #818384;">Vote to show your support!</span>
        </div>

        <section class="comments">
            <h2>Comments</h2>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                        <span style="color:#818384; font-size:0.85rem;">
                            on <?= date('F j, Y H:i', strtotime($comment['created_at'])) ?>
                        </span>
                        <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #818384;">No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="/my-blog/public/?url=comment/<?= $post['id'] ?>" style="margin-top:25px;">
                    <div class="form-group">
                        <label for="comment">Add your comment:</label>
                        <textarea name="comment" id="comment" rows="4" required placeholder="Share your thoughts..."></textarea>
                    </div>
                    <button type="submit" class="btn">Post Comment</button>
                </form>
            <?php else: ?>
                <p style="margin-top: 20px; color: #818384;"><a href="/my-blog/public/?url=login">Login</a> to comment.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>